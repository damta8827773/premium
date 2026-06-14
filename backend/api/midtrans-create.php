<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

require_once '../config/app.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

$order_id  = $input['order_id']  ?? '';
$amount    = (int)($input['amount']    ?? 0);
$user_id   = $input['user_id']   ?? '';
$email     = $input['email']     ?? '';
$name      = $input['name']      ?? 'User';

if (!$order_id || $amount < 1000 || !$user_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

$payload = [
    'transaction_details' => [
        'order_id'     => $order_id,
        'gross_amount' => $amount,
    ],
    'customer_details' => [
        'first_name' => $name,
        'email'      => $email ?: 'noemail@example.com',
    ],
    'item_details' => [
        [
            'id'       => 'DEPOSIT',
            'price'    => $amount,
            'quantity' => 1,
            'name'     => 'Deposit Saldo',
        ],
    ],
    'metadata' => [
        'user_id' => $user_id,
        'type'    => 'deposit',
    ],
];

$base_url = MIDTRANS_IS_PRODUCTION
    ? 'https://app.midtrans.com/snap/v1/transactions'
    : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

$ch = curl_init($base_url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Basic ' . base64_encode(MIDTRANS_SERVER_KEY . ':'),
    ],
    CURLOPT_TIMEOUT        => 30,
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_err  = curl_error($ch);
curl_close($ch);

if ($curl_err) {
    http_response_code(500);
    echo json_encode(['error' => 'cURL error: ' . $curl_err]);
    exit;
}

$data = json_decode($response, true);

if ($http_code !== 201 || empty($data['token'])) {
    http_response_code(500);
    echo json_encode(['error' => $data['error_messages'][0] ?? 'Midtrans error', 'raw' => $data]);
    exit;
}

echo json_encode([
    'token'        => $data['token'],
    'redirect_url' => $data['redirect_url'] ?? '',
]);
