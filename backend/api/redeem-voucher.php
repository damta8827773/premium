<?php
/**
 * Server-side voucher redemption.
 * POST { code }
 * Header: Authorization: Bearer <Firebase ID token>
 *
 * Previously redeem-50f75f021d39.php ran this as a client-side Firestore transaction,
 * meaning the credited `amount` could only ever be as trustworthy as the
 * browser choosing to read it correctly from the voucher doc - a tampered
 * client could commit any amount it liked. This endpoint reads the voucher
 * server-side and credits balance via the service-account Firestore REST
 * client (see backend/includes/firestore-rest.php), so the amount can no
 * longer be forged. The deterministic voucher_uses/{code}_{uid} doc ID
 * (see firestore.rules) still guards against double redemption.
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

$input = json_decode(file_get_contents('php://input'), true);
$code  = strtoupper(trim((string)($input['code'] ?? '')));
if ($code === '' || !preg_match('/^[A-Z0-9_-]{1,64}$/', $code)) {
    http_response_code(400);
    echo json_encode(['error' => 'Kode voucher tidak valid.']);
    exit;
}

$uid = verify_firebase_id_token();
if (!$uid) {
    if (function_exists('security_log')) security_log('invalid_id_token', 'medium', ['endpoint' => 'redeem-voucher']);
    http_response_code(401);
    echo json_encode(['error' => 'Sesi tidak valid, silakan login ulang.']);
    exit;
}

if (function_exists('security_rate_limit')) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    if (!security_rate_limit('redeem:uid:' . $uid, 10, 60) || !security_rate_limit('redeem:ip:' . $ip, 20, 60)) {
        http_response_code(429);
        echo json_encode(['error' => 'Terlalu banyak permintaan, coba lagi sebentar.']);
        exit;
    }
}

try {
    $use_doc_id = $code . '_' . $uid;
    $already = firestore_get("voucher_uses/$use_doc_id");
    if ($already['exists']) {
        http_response_code(400);
        echo json_encode(['error' => 'Kamu sudah pernah menggunakan voucher ini.']);
        exit;
    }

    $max_attempts = 3;
    $credited = null;

    for ($attempt = 0; $attempt < $max_attempts; $attempt++) {
        $voucher = firestore_get("vouchers/$code");
        if (!$voucher['exists']) {
            http_response_code(404);
            echo json_encode(['error' => 'Kode voucher tidak valid']);
            exit;
        }
        $v = $voucher['fields'];
        if (empty($v['is_active'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Voucher sudah tidak aktif']);
            exit;
        }
        $used_count = (int)($v['used_count'] ?? 0);
        $max_uses   = (int)($v['max_uses'] ?? 0);
        if ($used_count >= $max_uses) {
            http_response_code(400);
            echo json_encode(['error' => 'Voucher sudah habis digunakan']);
            exit;
        }
        $amount = (int)($v['amount'] ?? 0);

        $result = firestore_commit([
            [
                'op' => 'increment', 'path' => "vouchers/$code",
                'field' => 'used_count', 'delta' => 1,
                'precondition' => ['update_time' => $voucher['update_time']],
            ],
            [
                'op' => 'increment', 'path' => "users/$uid",
                'field' => 'balance', 'delta' => $amount,
            ],
            [
                'op' => 'set', 'path' => "voucher_uses/$use_doc_id",
                'fields' => [
                    'user_id' => $uid, 'voucher_code' => $code, 'amount' => $amount,
                    'created_at' => FIRESTORE_SERVER_TIMESTAMP,
                ],
                'precondition' => ['exists' => false],
            ],
            [
                'op' => 'set', 'path' => 'balance_history/' . 'VC' . date('YmdHis') . random_int(100, 999),
                'fields' => [
                    'user_id' => $uid, 'type' => 'voucher', 'amount' => $amount,
                    'description' => 'Redeem Voucher: ' . $code,
                    'created_at' => FIRESTORE_SERVER_TIMESTAMP,
                ],
                'precondition' => ['exists' => false],
            ],
            [
                'op' => 'set', 'path' => 'notifications/' . 'NT' . date('YmdHis') . random_int(100, 999),
                'fields' => [
                    'user_id' => $uid, 'title' => 'Voucher berhasil',
                    'message' => 'Voucher ' . $code . ' berhasil dipakai, saldo +Rp ' . number_format($amount, 0, ',', '.') . ' sudah masuk.',
                    'read' => false, 'created_at' => FIRESTORE_SERVER_TIMESTAMP,
                ],
            ],
        ]);

        if ($result['ok']) { $credited = $amount; break; }
        if (!$result['failed_precondition']) {
            if (function_exists('security_log')) security_log('redeem_commit_error', 'high', ['http_code' => $result['http_code'], 'uid' => $uid]);
            http_response_code(500);
            echo json_encode(['error' => 'Gagal memproses voucher, coba lagi.']);
            exit;
        }
        // Voucher changed (another redemption) since we read it - retry with a fresh read.
    }

    if ($credited === null) {
        http_response_code(409);
        echo json_encode(['error' => 'Voucher sedang ramai digunakan, coba lagi.']);
        exit;
    }

    echo json_encode(['ok' => true, 'amount' => $credited]);
} catch (Exception $e) {
    if (function_exists('security_log')) security_log('redeem_exception', 'high', ['message' => $e->getMessage()]);
    http_response_code(500);
    echo json_encode(['error' => 'Terjadi kesalahan server.']);
}
