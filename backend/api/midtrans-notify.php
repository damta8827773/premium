<?php
/**
 * Midtrans Payment Notification Webhook
 * URL: https://yourdomain.com/backend/api/midtrans-notify.php
 * Set this in Midtrans Dashboard → Settings → Configuration → Payment Notification URL
 */

require_once '../config/app.php';

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    http_response_code(400);
    exit('Bad Request');
}

$order_id           = $data['order_id']           ?? '';
$transaction_status = $data['transaction_status'] ?? '';
$payment_type       = $data['payment_type']       ?? '';
$gross_amount       = (int)($data['gross_amount'] ?? 0);
$signature_key      = $data['signature_key']      ?? '';
$status_code        = $data['status_code']        ?? '';
$fraud_status       = $data['fraud_status']       ?? '';

// Verify signature (timing-safe comparison)
$expected_sig = hash('sha512', $order_id . $status_code . $gross_amount . MIDTRANS_SERVER_KEY);
if (!hash_equals($expected_sig, $signature_key)) {
    if (file_exists(__DIR__ . '/../includes/security-monitor.php')) {
        require_once __DIR__ . '/../includes/security-monitor.php';
        if (function_exists('security_log')) {
            security_log('invalid_webhook_signature', 'high', ['order_id' => $order_id]);
        }
    }
    http_response_code(403);
    exit('Invalid signature');
}

// Only process successful payments
$is_success = ($transaction_status === 'settlement') ||
              ($transaction_status === 'capture' && $fraud_status === 'accept');

if (!$is_success) {
    // Log other statuses but return 200 to Midtrans
    http_response_code(200);
    exit('OK');
}

$safe_order_id = preg_replace('/[^a-zA-Z0-9_\-]/', '', $order_id);

// Write a status file so the frontend can poll it (fast, low-latency signal only)
$status_file = TEMP_DIR . $safe_order_id . '.json';
file_put_contents($status_file, json_encode([
    'order_id'   => $order_id,
    'status'     => 'success',
    'amount'     => $gross_amount,
    'paid_at'    => date('c'),
    'pay_method' => $payment_type,
]));

// Credit the deposit for real, server-side - this (not the temp file above,
// and not deposit.php's client) is the actual source of truth for balance.
// See backend/includes/firestore-rest.php for why this goes through the
// Firestore REST API instead of the Admin SDK/Cloud Functions.
require_once __DIR__ . '/../includes/firestore-rest.php';

function midtrans_notify_log($event, $severity, $details) {
    if (file_exists(__DIR__ . '/../includes/security-monitor.php')) {
        require_once __DIR__ . '/../includes/security-monitor.php';
        if (function_exists('security_log')) security_log($event, $severity, $details);
    }
}

try {
    $deposit = firestore_get("deposits/$safe_order_id");
    if (!$deposit['exists'] || ($deposit['fields']['status'] ?? '') === 'success') {
        // Nothing to credit (unknown order) or already credited by an earlier
        // delivery of this webhook - Midtrans retries are at-least-once.
        http_response_code(200);
        echo 'OK';
        exit;
    }

    $user_id = (string)($deposit['fields']['user_id'] ?? '');
    $amount  = (int)($deposit['fields']['amount'] ?? $gross_amount);

    $result = firestore_commit([
        [
            'op' => 'update_fields', 'path' => "deposits/$safe_order_id",
            'fields' => ['status' => 'success', 'completed_at' => FIRESTORE_SERVER_TIMESTAMP],
            'precondition' => ['update_time' => $deposit['update_time']],
        ],
        [
            'op' => 'increment', 'path' => "users/$user_id",
            'field' => 'balance', 'delta' => $amount,
        ],
        [
            'op' => 'set', 'path' => 'balance_history/DEP' . date('YmdHis') . random_int(100, 999),
            'fields' => [
                'user_id' => $user_id, 'type' => 'deposit', 'amount' => $amount,
                'description' => 'Deposit via Midtrans', 'created_at' => FIRESTORE_SERVER_TIMESTAMP,
            ],
            'precondition' => ['exists' => false],
        ],
    ]);

    if (!$result['ok'] && !$result['failed_precondition']) {
        // Real failure - return non-200 so Midtrans's webhook retry brings it back.
        midtrans_notify_log('deposit_credit_failed', 'high', ['order_id' => $safe_order_id, 'http_code' => $result['http_code']]);
        http_response_code(500);
        exit('Deposit crediting failed, will retry');
    }
    // failed_precondition here means a concurrent delivery of this same webhook
    // already transitioned the deposit - that's fine, not an error.
} catch (Exception $e) {
    midtrans_notify_log('deposit_credit_exception', 'high', ['order_id' => $safe_order_id, 'message' => $e->getMessage()]);
    http_response_code(500);
    exit('Deposit crediting error, will retry');
}

http_response_code(200);
echo 'OK';
