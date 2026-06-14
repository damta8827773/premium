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

// Verify signature
$expected_sig = hash('sha512', $order_id . $status_code . $gross_amount . MIDTRANS_SERVER_KEY);
if ($signature_key !== $expected_sig) {
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

// Write a status file so the frontend can poll it
$status_file = TEMP_DIR . preg_replace('/[^a-zA-Z0-9_\-]/', '', $order_id) . '.json';
file_put_contents($status_file, json_encode([
    'order_id'   => $order_id,
    'status'     => 'success',
    'amount'     => $gross_amount,
    'paid_at'    => date('c'),
    'pay_method' => $payment_type,
]));

http_response_code(200);
echo 'OK';
