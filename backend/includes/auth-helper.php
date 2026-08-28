<?php
/**
 * Verifies a Firebase ID token from the Authorization header, server-side.
 * There's no Firebase Admin SDK in this project (see firestore-rest.php's
 * doc comment for why), so this uses Google's accounts:lookup REST endpoint
 * instead - the officially supported way to verify a token without it.
 * Must be included after backend/config/app.php (uses FIREBASE_WEB_API_KEY).
 *
 * Returns the verified uid, or null if the header is missing or the token
 * is invalid/expired.
 */
function verify_firebase_id_token() {
    $auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');
    if (!preg_match('/^Bearer\s+(.+)$/i', $auth_header, $m)) return null;
    $id_token = $m[1];

    $ch = curl_init('https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=' . urlencode(FIREBASE_WEB_API_KEY));
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode(['idToken' => $id_token]),
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT        => 15,
    ]);
    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = json_decode($response, true);
    $uid  = $data['users'][0]['localId'] ?? null;
    return ($http_code === 200 && $uid) ? $uid : null;
}
