<?php
/**
 * Minimal Firestore REST API client, authenticated as a service account.
 *
 * Why this exists instead of the Firebase Admin SDK or Cloud Functions: this
 * project has no Composer/vendor directory (every backend/api/*.php endpoint
 * is curl-only, no dependencies) and no Cloud Functions/Blaze plan set up.
 * A service-account JWT + plain curl calls to the Firestore REST API matches
 * the existing architecture exactly (see midtrans-create.php's curl pattern)
 * while granting a privileged server identity that Firestore Security Rules
 * do not apply to - needed so purchase/deposit/voucher crediting can be
 * trusted without letting the browser write balance/stock directly.
 *
 * Must be included after backend/config/app.php (uses FIREBASE_PROJECT_ID,
 * FIREBASE_SERVICE_ACCOUNT_PATH, TEMP_DIR).
 *
 * Only the operations this project actually needs are implemented: get a
 * single document, run an equality-filtered query, and commit a batch of
 * writes (plain field sets, partial field updates, and atomic numeric
 * increments), each optionally guarded by an optimistic-concurrency
 * precondition (fails the whole commit if the document changed since it was
 * read). This is not a general Firestore client.
 */

function firestore_service_account() {
    static $sa = null;
    if ($sa !== null) return $sa;
    if (!defined('FIREBASE_SERVICE_ACCOUNT_PATH') || !file_exists(FIREBASE_SERVICE_ACCOUNT_PATH)) {
        throw new Exception('Firebase service account file not found. Generate one via Firebase Console -> Project Settings -> Service Accounts and save it at the path configured as FIREBASE_SERVICE_ACCOUNT_PATH.');
    }
    $sa = json_decode(file_get_contents(FIREBASE_SERVICE_ACCOUNT_PATH), true);
    if (!$sa || empty($sa['private_key']) || empty($sa['client_email'])) {
        throw new Exception('Invalid Firebase service account file.');
    }
    return $sa;
}

function firestore_base64url($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function firestore_mint_jwt() {
    $sa = firestore_service_account();
    $now = time();
    $header = ['alg' => 'RS256', 'typ' => 'JWT'];
    $claims = [
        'iss'   => $sa['client_email'],
        'scope' => 'https://www.googleapis.com/auth/datastore',
        'aud'   => 'https://oauth2.googleapis.com/token',
        'iat'   => $now,
        'exp'   => $now + 3600,
    ];
    $segments = [
        firestore_base64url(json_encode($header)),
        firestore_base64url(json_encode($claims)),
    ];
    $signingInput = implode('.', $segments);
    $privateKey = openssl_pkey_get_private($sa['private_key']);
    if (!$privateKey) throw new Exception('Could not load service account private key.');
    $signature = '';
    if (!openssl_sign($signingInput, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
        throw new Exception('Failed to sign service account JWT.');
    }
    $segments[] = firestore_base64url($signature);
    return implode('.', $segments);
}

function firestore_access_token() {
    $cacheFile = TEMP_DIR . 'firestore_token_cache.json';
    if (file_exists($cacheFile)) {
        $cached = json_decode(@file_get_contents($cacheFile), true);
        if ($cached && !empty($cached['access_token']) && !empty($cached['expires_at']) && $cached['expires_at'] > time() + 120) {
            return $cached['access_token'];
        }
    }
    $jwt = firestore_mint_jwt();
    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]),
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_TIMEOUT    => 15,
    ]);
    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_err  = curl_error($ch);
    curl_close($ch);
    if ($curl_err) throw new Exception('Token request cURL error: ' . $curl_err);

    $data = json_decode($response, true);
    if ($http_code !== 200 || empty($data['access_token'])) {
        throw new Exception('Failed to obtain Firestore access token (' . $http_code . '): ' . $response);
    }
    $expires_at = time() + (int)($data['expires_in'] ?? 3600);
    @file_put_contents($cacheFile, json_encode(['access_token' => $data['access_token'], 'expires_at' => $expires_at]), LOCK_EX);
    return $data['access_token'];
}

// Sentinel marker: use as a field value to request Firestore's own
// setToServerValue transform (equivalent to the client SDK's
// firebase.firestore.FieldValue.serverTimestamp(), used everywhere else in
// this app) instead of a client-supplied timestamp.
define('FIRESTORE_SERVER_TIMESTAMP', "\x00__FIRESTORE_SERVER_TIMESTAMP__\x00");

function firestore_documents_base() {
    return 'https://firestore.googleapis.com/v1/projects/' . FIREBASE_PROJECT_ID . '/databases/(default)/documents';
}

function firestore_request($method, $url, $body = null) {
    $token = firestore_access_token();
    $opts = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $token, 'Content-Type: application/json'],
        CURLOPT_TIMEOUT        => 20,
    ];
    if ($body !== null) $opts[CURLOPT_POSTFIELDS] = json_encode($body);
    $ch = curl_init($url);
    curl_setopt_array($ch, $opts);
    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_err  = curl_error($ch);
    curl_close($ch);
    if ($curl_err) throw new Exception('Firestore request cURL error: ' . $curl_err);
    return ['http_code' => $http_code, 'body' => json_decode($response, true), 'raw' => $response];
}

// ---- typed-value <-> plain PHP conversion ----

function firestore_is_list($arr) {
    if (!is_array($arr)) return false;
    return array_keys($arr) === range(0, count($arr) - 1);
}

function firestore_encode_value($v) {
    if ($v === null) return ['nullValue' => null];
    if (is_bool($v)) return ['booleanValue' => $v];
    if (is_int($v)) return ['integerValue' => (string)$v];
    if (is_float($v)) return ['doubleValue' => $v];
    if (is_string($v)) return ['stringValue' => $v];
    if (is_array($v)) {
        if (firestore_is_list($v)) {
            return ['arrayValue' => ['values' => array_map('firestore_encode_value', $v)]];
        }
        return ['mapValue' => ['fields' => (object) firestore_encode_fields($v)]];
    }
    return ['stringValue' => (string)$v];
}

function firestore_decode_value($fv) {
    if (!is_array($fv)) return null;
    if (array_key_exists('nullValue', $fv)) return null;
    if (array_key_exists('booleanValue', $fv)) return $fv['booleanValue'];
    if (array_key_exists('integerValue', $fv)) return (int)$fv['integerValue'];
    if (array_key_exists('doubleValue', $fv)) return (float)$fv['doubleValue'];
    if (array_key_exists('stringValue', $fv)) return $fv['stringValue'];
    if (array_key_exists('timestampValue', $fv)) return $fv['timestampValue'];
    if (array_key_exists('referenceValue', $fv)) return $fv['referenceValue'];
    if (array_key_exists('arrayValue', $fv)) return array_map('firestore_decode_value', $fv['arrayValue']['values'] ?? []);
    if (array_key_exists('mapValue', $fv)) return firestore_decode_fields($fv['mapValue']['fields'] ?? []);
    return null;
}

function firestore_encode_fields($assoc) {
    $out = [];
    foreach ($assoc as $k => $v) $out[$k] = firestore_encode_value($v);
    return $out;
}
function firestore_decode_fields($fields) {
    $out = [];
    foreach ((array)$fields as $k => $v) $out[$k] = firestore_decode_value($v);
    return $out;
}
function firestore_decode_document($doc) {
    if (empty($doc['name'])) return ['exists' => false];
    return [
        'exists'      => true,
        'path'        => preg_replace('#^.*/documents/#', '', $doc['name']),
        'fields'      => firestore_decode_fields($doc['fields'] ?? []),
        'update_time' => $doc['updateTime'] ?? null,
    ];
}

// ---- reads ----

/** Get a single document by path relative to the documents root, e.g. "users/abc123". */
function firestore_get($relative_path) {
    $res = firestore_request('GET', firestore_documents_base() . '/' . $relative_path);
    if ($res['http_code'] === 404) return ['exists' => false];
    if ($res['http_code'] !== 200) {
        throw new Exception('Firestore get failed (' . $res['http_code'] . '): ' . $res['raw']);
    }
    return firestore_decode_document($res['body']);
}

/** Equality-filtered query. $wheres is ['field' => value, ...], all AND-ed. */
function firestore_run_query($collection_id, $wheres = [], $limit = null, $parent = '') {
    $filters = [];
    foreach ($wheres as $field => $value) {
        $filters[] = [
            'fieldFilter' => [
                'field' => ['fieldPath' => $field],
                'op'    => 'EQUAL',
                'value' => firestore_encode_value($value),
            ],
        ];
    }
    $structured_query = ['from' => [['collectionId' => $collection_id]]];
    if (count($filters) === 1) {
        $structured_query['where'] = $filters[0];
    } elseif (count($filters) > 1) {
        $structured_query['where'] = ['compositeFilter' => ['op' => 'AND', 'filters' => $filters]];
    }
    if ($limit !== null) $structured_query['limit'] = $limit;

    $url = firestore_documents_base() . ($parent !== '' ? '/' . $parent : '') . ':runQuery';
    $res = firestore_request('POST', $url, ['structuredQuery' => $structured_query]);
    if ($res['http_code'] !== 200) {
        throw new Exception('Firestore query failed (' . $res['http_code'] . '): ' . $res['raw']);
    }
    $out = [];
    foreach ((array)$res['body'] as $entry) {
        if (!empty($entry['document'])) $out[] = firestore_decode_document($entry['document']);
    }
    return $out;
}

// ---- writes ----

/**
 * Commit a batch of writes atomically (all succeed or all fail together).
 * Each element of $writes is one of:
 *   ['op'=>'set',           'path'=>..., 'fields'=>[...], 'precondition'=>?]   full doc replace/create
 *   ['op'=>'update_fields', 'path'=>..., 'fields'=>[...], 'precondition'=>?]   partial field update
 *   ['op'=>'increment',     'path'=>..., 'field'=>'balance', 'delta'=>-5000, 'precondition'=>?]  atomic numeric delta
 * $precondition is optional: ['exists'=>false] (must not already exist, for
 * true creates) or ['update_time'=>'...'] (must not have changed since read,
 * for optimistic-concurrency updates). Use the FIRESTORE_SERVER_TIMESTAMP
 * constant as a field's value in 'set'/'update_fields' to get a real
 * server-assigned timestamp (matches FieldValue.serverTimestamp() elsewhere
 * in this app) instead of a client-supplied one.
 *
 * Returns ['ok'=>bool, 'http_code'=>int, 'failed_precondition'=>bool, 'body'=>...].
 * On failed_precondition, re-read the affected documents and retry with a
 * fresh precondition - do not blindly retry with the same one.
 */
function firestore_commit($writes) {
    $docs_prefix = 'projects/' . FIREBASE_PROJECT_ID . '/databases/(default)/documents/';
    $rest_writes = [];

    foreach ($writes as $w) {
        $name = $docs_prefix . $w['path'];
        $write = [];
        $transforms = [];

        if ($w['op'] === 'set' || $w['op'] === 'update_fields') {
            // Split out any FIRESTORE_SERVER_TIMESTAMP sentinel fields into a
            // transform - they can't be encoded as a regular typed value.
            $regular_fields = [];
            foreach ($w['fields'] as $k => $v) {
                if ($v === FIRESTORE_SERVER_TIMESTAMP) {
                    $transforms[] = ['fieldPath' => $k, 'setToServerValue' => 'REQUEST_TIME'];
                } else {
                    $regular_fields[$k] = $v;
                }
            }
            $write['update'] = ['name' => $name, 'fields' => (object) firestore_encode_fields($regular_fields)];
            if ($w['op'] === 'update_fields') {
                $write['updateMask'] = ['fieldPaths' => array_keys($regular_fields)];
            }
        } elseif ($w['op'] === 'increment') {
            $write['update']     = ['name' => $name, 'fields' => new stdClass()];
            $write['updateMask'] = ['fieldPaths' => []];
            $transforms[] = ['fieldPath' => $w['field'], 'increment' => firestore_encode_value($w['delta'])];
        } else {
            throw new Exception('Unknown Firestore write op: ' . $w['op']);
        }

        if ($transforms) $write['updateTransforms'] = $transforms;

        if (!empty($w['precondition'])) {
            if (array_key_exists('exists', $w['precondition'])) {
                $write['currentDocument'] = ['exists' => $w['precondition']['exists']];
            } elseif (!empty($w['precondition']['update_time'])) {
                $write['currentDocument'] = ['updateTime' => $w['precondition']['update_time']];
            }
        }

        $rest_writes[] = $write;
    }

    $res = firestore_request('POST', firestore_documents_base() . ':commit', ['writes' => $rest_writes]);
    $failed_precondition = in_array($res['http_code'], [400, 409], true)
        && is_string($res['raw']) && stripos($res['raw'], 'FAILED_PRECONDITION') !== false;

    return [
        'ok'                  => $res['http_code'] === 200,
        'http_code'           => $res['http_code'],
        'failed_precondition' => $failed_precondition,
        'body'                => $res['body'],
    ];
}
