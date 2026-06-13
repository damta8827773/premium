<?php
/**
 * Poll endpoint — frontend calls this after Snap popup closes to confirm payment
 * GET /api/check-payment.php?order_id=INV...
 */
header('Content-Type: application/json');

require_once '../config/app.php';

$order_id = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_GET['order_id'] ?? '');
if (!$order_id) {
    echo json_encode(['status' => 'not_found']);
    exit;
}

$status_file = TEMP_DIR . $order_id . '.json';
if (!file_exists($status_file)) {
    echo json_encode(['status' => 'pending']);
    exit;
}

$data = json_decode(file_get_contents($status_file), true);

// Clean up the temp file after reading
@unlink($status_file);

echo json_encode($data ?: ['status' => 'pending']);
