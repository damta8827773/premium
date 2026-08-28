<?php
/**
 * AI-assisted review of a manual QRIS deposit's uploaded proof image.
 * POST { deposit_id }
 * Header: Authorization: Bearer <Firebase ID token>
 *
 * This does NOT credit balance or approve anything - it only writes an
 * `ai_analysis` recommendation onto the deposit doc for the admin to see
 * while reviewing (admin/pembayaran-*.php). The admin still clicks
 * Approve/Tolak themselves (see approveDeposit() there, unchanged). Written
 * via the service account rather than the buyer's own client write because
 * firestore.rules only allows admins to update a deposit doc after
 * creation - if the buyer's own session could write this field, a
 * malicious buyer could just fake "looks_valid: true" themselves and skip
 * the AI entirely.
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
$deposit_id = (string)($input['deposit_id'] ?? '');
if ($deposit_id === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing deposit_id']);
    exit;
}

$uid = verify_firebase_id_token();
if (!$uid) {
    if (function_exists('security_log')) security_log('invalid_id_token', 'medium', ['endpoint' => 'analyze-deposit-proof']);
    http_response_code(401);
    echo json_encode(['error' => 'Sesi tidak valid, silakan login ulang.']);
    exit;
}

if (function_exists('security_rate_limit')) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    if (!security_rate_limit('analyzeproof:uid:' . $uid, 10, 60) || !security_rate_limit('analyzeproof:ip:' . $ip, 20, 60)) {
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

try {
    $deposit = firestore_get("deposits/$deposit_id");
    if (!$deposit['exists']) {
        http_response_code(404);
        echo json_encode(['error' => 'Deposit tidak ditemukan.']);
        exit;
    }
    $f = $deposit['fields'];
    if (($f['user_id'] ?? null) !== $uid) {
        http_response_code(403);
        echo json_encode(['error' => 'Deposit ini bukan milik kamu.']);
        exit;
    }
    if (($f['method'] ?? null) !== 'manual' || ($f['status'] ?? null) !== 'pending') {
        http_response_code(400);
        echo json_encode(['error' => 'Deposit ini tidak bisa dianalisa.']);
        exit;
    }
    $proof_url = (string)($f['proof_image'] ?? '');
    $expected_amount = (int)($f['amount'] ?? 0);
    if ($proof_url === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Bukti pembayaran belum diupload.']);
        exit;
    }

    // ---- Download the proof image and base64-encode it for Claude vision ----
    $ch = curl_init($proof_url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 20]);
    $image_bytes = curl_exec($ch);
    $img_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE) ?: 'image/jpeg';
    curl_close($ch);
    if ($img_http_code !== 200 || !$image_bytes) {
        http_response_code(502);
        echo json_encode(['error' => 'Gagal mengambil gambar bukti.']);
        exit;
    }
    if (strlen($image_bytes) > 5 * 1024 * 1024) {
        http_response_code(400);
        echo json_encode(['error' => 'Gambar bukti terlalu besar.']);
        exit;
    }
    $media_type = in_array($content_type, ['image/png', 'image/jpeg', 'image/webp', 'image/gif'], true) ? $content_type : 'image/jpeg';
    $b64 = base64_encode($image_bytes);

    $system_prompt = <<<PROMPT
Kamu membantu admin toko online meninjau screenshot bukti pembayaran QRIS manual. Ini HANYA rekomendasi awal - admin manusia tetap yang memutuskan akhir, jadi jangan berlebihan yakin.

Nominal yang diharapkan: Rp {$expected_amount}.

Lihat gambar dan balas HANYA dengan JSON valid (tanpa markdown/backtick), persis dengan bentuk ini:
{"looks_like_payment_proof": true/false, "extracted_amount": angka atau null, "extracted_datetime": "string atau null", "matches_expected_amount": true/false/null, "notes": "catatan singkat 1-2 kalimat dalam Bahasa Indonesia"}
PROMPT;

    $payload = [
        'model' => ANTHROPIC_MODEL,
        'max_tokens' => 400,
        'system' => $system_prompt,
        'messages' => [[
            'role' => 'user',
            'content' => [
                ['type' => 'image', 'source' => ['type' => 'base64', 'media_type' => $media_type, 'data' => $b64]],
                ['type' => 'text', 'text' => 'Analisa screenshot bukti pembayaran ini.'],
            ],
        ]],
    ];

    $ch = curl_init('https://api.anthropic.com/v1/messages');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'x-api-key: ' . ANTHROPIC_API_KEY,
            'anthropic-version: 2023-06-01',
        ],
        CURLOPT_TIMEOUT => 30,
    ]);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_err = curl_error($ch);
    curl_close($ch);

    if ($curl_err) {
        http_response_code(500);
        echo json_encode(['error' => 'cURL error: ' . $curl_err]);
        exit;
    }
    $data = json_decode($response, true);
    if ($http_code !== 200 || empty($data['content'][0]['text'])) {
        if (function_exists('security_log')) security_log('ai_proof_analysis_error', 'low', ['http_code' => $http_code]);
        http_response_code(502);
        echo json_encode(['error' => 'AI sedang tidak tersedia, coba lagi sebentar.']);
        exit;
    }

    $raw = trim($data['content'][0]['text']);
    $raw = preg_replace('/^```(json)?|```$/m', '', $raw);
    $analysis = json_decode(trim($raw), true);
    if (!is_array($analysis)) {
        $analysis = ['looks_like_payment_proof' => null, 'extracted_amount' => null, 'extracted_datetime' => null, 'matches_expected_amount' => null, 'notes' => 'AI response tidak bisa diparse.'];
    }
    $analysis['analyzed_at_client'] = date('c');

    $commit = firestore_commit([
        ['op' => 'update_fields', 'path' => "deposits/$deposit_id", 'fields' => [
            'ai_analysis' => $analysis,
            'ai_analyzed_at' => FIRESTORE_SERVER_TIMESTAMP,
        ]],
    ]);
    if (!$commit['ok']) {
        if (function_exists('security_log')) security_log('ai_proof_write_failed', 'medium', ['deposit_id' => $deposit_id]);
    }

    echo json_encode(['ok' => true, 'analysis' => $analysis]);
} catch (Exception $e) {
    if (function_exists('security_log')) security_log('analyze_deposit_proof_exception', 'high', ['message' => $e->getMessage()]);
    http_response_code(500);
    echo json_encode(['error' => 'Terjadi kesalahan server.']);
}
