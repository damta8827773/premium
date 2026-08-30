<?php
/**
 * Server-side purchase fulfillment.
 * POST { product_id, variant_id }
 * Header: Authorization: Bearer <Firebase ID token>
 *
 * This used to happen entirely client-side (see toko/e3514627bb16 git history):
 * the browser read an unused stock_items doc directly (exposing the
 * delivered account credentials of every unsold item to any signed-in
 * user) and decremented its own balance with nothing server-side ever
 * checking it. This endpoint moves both onto the service-account-backed
 * Firestore REST client (see backend/includes/firestore-rest.php) so the
 * browser never touches stock_items or writes balance directly again -
 * see firestore.rules for the matching lockdown.
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
if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

$product_id = (string)($input['product_id'] ?? '');
$variant_id = (string)($input['variant_id'] ?? '');
if ($product_id === '' || $variant_id === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

$uid = verify_firebase_id_token();
if (!$uid) {
    if (function_exists('security_log')) security_log('invalid_id_token', 'medium', ['endpoint' => 'checkout']);
    http_response_code(401);
    echo json_encode(['error' => 'Sesi tidak valid, silakan login ulang.']);
    exit;
}

if (function_exists('security_rate_limit')) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    if (!security_rate_limit('checkout:uid:' . $uid, 10, 60) || !security_rate_limit('checkout:ip:' . $ip, 20, 60)) {
        http_response_code(429);
        echo json_encode(['error' => 'Terlalu banyak permintaan, coba lagi sebentar.']);
        exit;
    }
}

function checkout_generate_id($prefix) {
    return $prefix . date('YmdHis') . random_int(100, 999);
}

try {
    $variant = firestore_get("products/$product_id/variants/$variant_id");
    if (!$variant['exists'] || empty($variant['fields']['is_active'])) {
        http_response_code(404);
        echo json_encode(['error' => 'Produk/varian tidak ditemukan.']);
        exit;
    }

    $product = firestore_get("products/$product_id");
    if (!$product['exists']) {
        http_response_code(404);
        echo json_encode(['error' => 'Produk tidak ditemukan.']);
        exit;
    }

    $max_attempts = 3;
    $order = null;
    $last_error = 'Stok habis, coba lagi.';

    for ($attempt = 0; $attempt < $max_attempts; $attempt++) {
        $user = firestore_get("users/$uid");
        if (!$user['exists']) {
            http_response_code(404);
            echo json_encode(['error' => 'Akun tidak ditemukan.']);
            exit;
        }
        $balance      = (int)($user['fields']['balance'] ?? 0);
        $is_reseller  = !empty($user['fields']['is_reseller']);
        $price        = $is_reseller && !empty($variant['fields']['reseller_price'])
            ? (int)$variant['fields']['reseller_price']
            : (int)($variant['fields']['price'] ?? 0);

        if ($balance < $price) {
            http_response_code(400);
            echo json_encode(['error' => 'Saldo tidak cukup. Silakan deposit dulu.']);
            exit;
        }

        $stock_candidates = firestore_run_query('stock_items', ['variant_id' => $variant_id, 'is_used' => false], 1);
        if (!$stock_candidates) {
            http_response_code(400);
            echo json_encode(['error' => 'Stok habis!']);
            exit;
        }
        $stock_item = $stock_candidates[0];
        $stock_id   = basename($stock_item['path']);

        $order_id = checkout_generate_id('INV');

        $result = firestore_commit([
            [
                'op' => 'update_fields', 'path' => "stock_items/$stock_id",
                'fields' => ['is_used' => true, 'order_id' => $order_id],
                'precondition' => ['update_time' => $stock_item['update_time']],
            ],
            [
                'op' => 'increment', 'path' => "products/$product_id/variants/$variant_id",
                'field' => 'stock', 'delta' => -1,
            ],
            [
                'op' => 'increment', 'path' => "users/$uid",
                'field' => 'balance', 'delta' => -$price,
                'precondition' => ['update_time' => $user['update_time']],
            ],
            [
                'op' => 'set', 'path' => "orders/$order_id",
                'fields' => [
                    'id' => $order_id, 'invoice' => $order_id, 'user_id' => $uid,
                    'product_id' => $product_id, 'product_name' => (string)($product['fields']['name'] ?? ''),
                    'variant_id' => $variant_id, 'variant_name' => (string)($variant['fields']['name'] ?? ''),
                    'price' => $price, 'status' => 'selesai',
                    'stock_item_id' => $stock_id, 'stock_content' => (string)($stock_item['fields']['content'] ?? ''),
                    'created_at' => FIRESTORE_SERVER_TIMESTAMP, 'completed_at' => FIRESTORE_SERVER_TIMESTAMP,
                ],
                'precondition' => ['exists' => false],
            ],
            [
                'op' => 'set', 'path' => 'balance_history/' . checkout_generate_id('BH'),
                'fields' => [
                    'user_id' => $uid, 'type' => 'pembelian', 'amount' => -$price,
                    'description' => 'Pembelian ' . ($product['fields']['name'] ?? '') . ' - ' . ($variant['fields']['name'] ?? ''),
                    'created_at' => FIRESTORE_SERVER_TIMESTAMP,
                ],
                'precondition' => ['exists' => false],
            ],
            [
                'op' => 'set', 'path' => 'notifications/' . checkout_generate_id('NT'),
                'fields' => [
                    'user_id' => $uid, 'title' => 'Pesanan berhasil',
                    'message' => ($product['fields']['name'] ?? 'Produk') . ' - ' . ($variant['fields']['name'] ?? '') . ' sudah aktif, cek riwayat pesanan.',
                    'read' => false, 'created_at' => FIRESTORE_SERVER_TIMESTAMP,
                ],
            ],
        ]);

        if ($result['ok']) {
            $order = ['order_id' => $order_id, 'price' => $price, 'new_balance' => $balance - $price];
            break;
        }
        if (!$result['failed_precondition']) {
            if (function_exists('security_log')) security_log('checkout_commit_error', 'high', ['http_code' => $result['http_code'], 'uid' => $uid]);
            http_response_code(500);
            echo json_encode(['error' => 'Gagal memproses pembelian, coba lagi.']);
            exit;
        }
        // Someone else claimed this stock item / balance changed since we read it - retry with fresh reads.
        $last_error = 'Stok baru saja diambil pembeli lain, coba lagi.';
    }

    if (!$order) {
        http_response_code(409);
        echo json_encode(['error' => $last_error]);
        exit;
    }

    echo json_encode(['ok' => true] + $order);
} catch (Exception $e) {
    if (function_exists('security_log')) security_log('checkout_exception', 'high', ['message' => $e->getMessage()]);
    http_response_code(500);
    echo json_encode(['error' => 'Terjadi kesalahan server.']);
}
