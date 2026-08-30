<?php
/**
 * Live-chat auto-reply endpoint.
 * POST { chat_id, message, history: [{role:'user'|'ai'|'admin', text}] }
 * Header: Authorization: Bearer <Firebase ID token>
 *
 * Uses a free keyword-matched FAQ bot (backend/includes/faq-bot.php)
 * instead of a real LLM - no API key, no per-message cost, works the
 * moment the server starts. Same approach as this project's other app
 * (perpustakaan-digital's chatBot.ts): a fixed keyword->answer list,
 * first match wins, and anything unrecognized gets a generic
 * acknowledgement plus a hand-off to a human admin rather than a guess.
 * `history` is accepted for API-shape compatibility with the previous
 * LLM-backed version but isn't used - the bot only looks at the current
 * message, it has no notion of conversation context.
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
require_once __DIR__ . '/../includes/auth-helper.php';
require_once __DIR__ . '/../includes/faq-bot.php';
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
$uid = verify_firebase_id_token();
if (!$uid) {
    if (function_exists('security_log')) security_log('invalid_id_token', 'medium', ['chat_id' => $chat_id]);
    http_response_code(401);
    echo json_encode(['error' => 'Sesi tidak valid, silakan login ulang.']);
    exit;
}

// ---- Rate limit (cheap now, but still a public endpoint worth throttling) ----
if (function_exists('security_rate_limit')) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $allowed_uid = security_rate_limit('ai:uid:' . $uid, 30, 60);
    $allowed_ip  = security_rate_limit('ai:ip:' . $ip, 60, 60);
    if (!$allowed_uid || !$allowed_ip) {
        http_response_code(429);
        echo json_encode(['error' => 'Terlalu banyak permintaan, coba lagi sebentar.']);
        exit;
    }
}

$result = faq_bot_reply($message);
echo json_encode(['reply' => $result['reply'], 'handoff' => $result['handoff']]);
