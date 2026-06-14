<?php
// Create a Premium Store deposit transaction - PHP
$apiUrl = "https://yourdomain.com/backend/api/midtrans-create.php";

$payload = [
    "order_id" => "DEP-" . time(),
    "amount"   => 50000,
    "user_id"  => "firebase-uid",
    "email"    => "customer@example.com",
    "name"     => "Customer Name",
];

$ch = curl_init($apiUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => ["Content-Type: application/json"],
    CURLOPT_POSTFIELDS     => json_encode($payload),
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
echo "Snap token : {$data['token']}\n";
echo "Redirect   : {$data['redirect_url']}\n";
