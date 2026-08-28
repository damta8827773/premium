<?php
/**
 * Credits a referral bonus to both the new user and whoever referred them.
 * POST { referral_code }
 * Header: Authorization: Bearer <Firebase ID token> (the NEW user's session,
 * called once right after register.php finishes creating their account)
 *
 * Balance changes only ever happen server-side via the service account
 * (see firestore-rest.php) - same pattern as checkout.php/redeem-voucher.php
 * - so a client can't just fabricate a referral credit for itself.
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

require_once '../config/app.php';
require_once __DIR__ . '/../includes/auth-helper.php';
require_once __DIR__ . '/../includes/firestore-rest.php';
if (file_exists(__DIR__ . '/../includes/security-monitor.php')) {
    require_once __DIR__ . '/../includes/security-monitor.php';
}

const REFERRAL_BONUS = 5000; // Rp, credited to both the new user and the referrer

$input = json_decode(file_get_contents('php://input'), true);
$code = strtolower(trim((string)($input['referral_code'] ?? '')));
if ($code === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Kode referral kosong']);
    exit;
}

$uid = verify_firebase_id_token();
if (!$uid) {
    if (function_exists('security_log')) security_log('invalid_id_token', 'medium', ['endpoint' => 'apply-referral']);
    http_response_code(401);
    echo json_encode(['error' => 'Sesi tidak valid, silakan login ulang.']);
    exit;
}

if (function_exists('security_rate_limit')) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    if (!security_rate_limit('referral:uid:' . $uid, 5, 60) || !security_rate_limit('referral:ip:' . $ip, 10, 60)) {
        http_response_code(429);
        echo json_encode(['error' => 'Terlalu banyak permintaan, coba lagi sebentar.']);
        exit;
    }
}

try {
    $usernameDoc = firestore_get("usernames/$code");
    if (!$usernameDoc['exists']) {
        http_response_code(404);
        echo json_encode(['error' => 'Kode referral tidak ditemukan.']);
        exit;
    }
    $referrerUid = (string)($usernameDoc['fields']['uid'] ?? '');
    if ($referrerUid === '' || $referrerUid === $uid) {
        http_response_code(400);
        echo json_encode(['error' => 'Kode referral tidak valid.']);
        exit;
    }

    $newUser = firestore_get("users/$uid");
    if (!$newUser['exists']) {
        http_response_code(404);
        echo json_encode(['error' => 'Akun tidak ditemukan.']);
        exit;
    }
    if (!empty($newUser['fields']['referred_by'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Kode referral sudah pernah dipakai untuk akun ini.']);
        exit;
    }
    $referrer = firestore_get("users/$referrerUid");
    if (!$referrer['exists']) {
        http_response_code(404);
        echo json_encode(['error' => 'Akun pemilik kode referral tidak ditemukan.']);
        exit;
    }

    $result = firestore_commit([
        ['op' => 'update_fields', 'path' => "users/$uid", 'fields' => [
            'referred_by' => $code,
        ], 'precondition' => ['update_time' => $newUser['update_time']]],
        ['op' => 'increment', 'path' => "users/$uid", 'field' => 'balance', 'delta' => REFERRAL_BONUS],
        ['op' => 'increment', 'path' => "users/$referrerUid", 'field' => 'balance', 'delta' => REFERRAL_BONUS],
        ['op' => 'set', 'path' => 'balance_history/' . 'REF' . date('YmdHis') . random_int(100, 999), 'fields' => [
            'user_id' => $uid, 'type' => 'referral', 'amount' => REFERRAL_BONUS,
            'description' => 'Bonus daftar pakai kode referral ' . $code,
            'created_at' => FIRESTORE_SERVER_TIMESTAMP,
        ]],
        ['op' => 'set', 'path' => 'balance_history/' . 'REF' . date('YmdHis') . random_int(100, 999) . 'r', 'fields' => [
            'user_id' => $referrerUid, 'type' => 'referral', 'amount' => REFERRAL_BONUS,
            'description' => 'Bonus referral - user baru mendaftar',
            'created_at' => FIRESTORE_SERVER_TIMESTAMP,
        ]],
    ]);

    if (!$result['ok']) {
        if (function_exists('security_log')) security_log('referral_commit_error', 'medium', ['http_code' => $result['http_code'], 'uid' => $uid]);
        http_response_code(409);
        echo json_encode(['error' => 'Gagal menerapkan kode referral, coba lagi.']);
        exit;
    }

    echo json_encode(['ok' => true, 'bonus' => REFERRAL_BONUS]);
} catch (Exception $e) {
    if (function_exists('security_log')) security_log('apply_referral_exception', 'high', ['message' => $e->getMessage()]);
    http_response_code(500);
    echo json_encode(['error' => 'Terjadi kesalahan server.']);
}
