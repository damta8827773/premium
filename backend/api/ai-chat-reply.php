<?php
/**
 * AI live-chat auto-reply endpoint.
 * POST { chat_id, message, history: [{role:'user'|'ai'|'admin', text}] }
 * Header: Authorization: Bearer <Firebase ID token>
 *
 * There is no Firebase Admin SDK / Composer in this project, so the
 * client's Firebase ID token is verified the same way every other backend
 * call here talks to an external API: a curl POST, this time to Google's
 * accounts:lookup endpoint. The client then writes the returned reply to
 * Firestore itself (same pattern as midtrans-create.php returning a token
 * for the client to act on).
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
if (file_exists(__DIR__ . '/../includes/security-monitor.php')) {
    require_once __DIR__ . '/../includes/security-monitor.php';
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

$chat_id = (string)($input['chat_id'] ?? '');
$message = trim((string)($input['message'] ?? ''));
$history = is_array($input['history'] ?? null) ? $input['history'] : [];

if ($chat_id === '' || $message === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}
if (mb_strlen($message) > 2000) {
    http_response_code(400);
    echo json_encode(['error' => 'Pesan terlalu panjang']);
    exit;
}

// ---- Verify the Firebase ID token (no Admin SDK available server-side) ----
$auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');
if (!preg_match('/^Bearer\s+(.+)$/i', $auth_header, $m)) {
    http_response_code(401);
    echo json_encode(['error' => 'Missing authorization']);
    exit;
}
$id_token = $m[1];

$verify_ch = curl_init('https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=' . urlencode(FIREBASE_WEB_API_KEY));
curl_setopt_array($verify_ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode(['idToken' => $id_token]),
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_TIMEOUT        => 15,
]);
$verify_response  = curl_exec($verify_ch);
$verify_http_code = curl_getinfo($verify_ch, CURLINFO_HTTP_CODE);
curl_close($verify_ch);

$verify_data = json_decode($verify_response, true);
$uid = $verify_data['users'][0]['localId'] ?? null;

if ($verify_http_code !== 200 || !$uid) {
    if (function_exists('security_log')) security_log('invalid_id_token', 'medium', ['chat_id' => $chat_id]);
    http_response_code(401);
    echo json_encode(['error' => 'Sesi tidak valid, silakan login ulang.']);
    exit;
}

// ---- Rate limit (this endpoint costs real money per call) ----
if (function_exists('security_rate_limit')) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $allowed_uid = security_rate_limit('ai:uid:' . $uid, 20, 60); // 20 msg/min per user
    $allowed_ip  = security_rate_limit('ai:ip:' . $ip, 40, 60);   // 40 msg/min per IP
    if (!$allowed_uid || !$allowed_ip) {
        http_response_code(429);
        echo json_encode(['error' => 'Terlalu banyak permintaan, coba lagi sebentar.']);
        exit;
    }
}

if (!defined('ANTHROPIC_API_KEY') || ANTHROPIC_API_KEY === 'YOUR_ANTHROPIC_API_KEY') {
    http_response_code(503);
    echo json_encode(['error' => 'AI belum dikonfigurasi di server.']);
    exit;
}

// ---- Build the Anthropic request ----
$system_prompt = <<<PROMPT
Kamu adalah asisten customer service untuk "Premium Store", toko akun premium digital (Netflix, Spotify, Canva, CapCut, dll), top up saldo, dan voucher. Jawab singkat, jelas, ramah, dalam Bahasa Indonesia.

Kamu HANYA membantu pertanyaan umum: cara pakai produk, cara top up saldo, cara redeem voucher, cara klaim garansi, dan info umum toko. Kamu TIDAK memiliki akses ke data akun, saldo, atau pesanan spesifik pengguna.

Jika pertanyaan butuh melihat data akun/pesanan spesifik, terkait pembayaran bermasalah, komplain, atau pengguna secara eksplisit minta bicara dengan admin/manusia, jawab sebaik mungkin lalu WAJIB tambahkan baris baru persis berisi: [HANDOFF]
PROMPT;

$anthropic_messages = [];
foreach ($history as $h) {
    if (!isset($h['role'], $h['text'])) continue;
    $role = ($h['role'] === 'admin' || $h['role'] === 'ai') ? 'assistant' : 'user';
    $text = trim((string)$h['text']);
    if ($text === '') continue;
    $anthropic_messages[] = ['role' => $role, 'content' => $text];
}
$anthropic_messages[] = ['role' => 'user', 'content' => $message];

// bound token usage/cost - keep only the most recent turns
if (count($anthropic_messages) > 12) {
    $anthropic_messages = array_slice($anthropic_messages, -12);
}

$payload = [
    'model'      => ANTHROPIC_MODEL,
    'max_tokens' => 500,
    'system'     => $system_prompt,
    'messages'   => $anthropic_messages,
];

$ch = curl_init('https://api.anthropic.com/v1/messages');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'x-api-key: ' . ANTHROPIC_API_KEY,
        'anthropic-version: 2023-06-01',
    ],
    CURLOPT_TIMEOUT        => 30,
]);
$response  = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_err  = curl_error($ch);
curl_close($ch);

if ($curl_err) {
    http_response_code(500);
    echo json_encode(['error' => 'cURL error: ' . $curl_err]);
    exit;
}

$data = json_decode($response, true);
if ($http_code !== 200 || empty($data['content'][0]['text'])) {
    if (function_exists('security_log')) security_log('ai_api_error', 'low', ['http_code' => $http_code]);
    http_response_code(502);
    echo json_encode(['error' => 'AI sedang tidak tersedia, coba lagi sebentar.']);
    exit;
}

$reply   = trim($data['content'][0]['text']);
$handoff = false;
if (preg_match('/\[HANDOFF\]\s*$/u', $reply)) {
    $handoff = true;
    $reply   = trim(preg_replace('/\[HANDOFF\]\s*$/u', '', $reply));
}

echo json_encode(['reply' => $reply, 'handoff' => $handoff]);
