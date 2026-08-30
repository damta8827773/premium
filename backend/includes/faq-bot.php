<?php
/**
 * Keyword-matched FAQ auto-reply for live chat - NOT a real LLM, deliberately,
 * so it costs nothing and needs no API key. Same approach as this project's
 * other app (perpustakaan-digital's chatBot.ts): a fixed list of entries,
 * first keyword match wins. If nothing matches, send a generic
 * acknowledgement and hand off to a human admin rather than guessing - this
 * bot never invents an answer to something it doesn't recognize.
 *
 * Each entry carries its own `handoff` flag rather than checking the message
 * against a second, separately-maintained list of "escalate" keywords -
 * that split invited exactly the bug it looks like: a keyword list added to
 * an entry without also updating the other list, so the reply text says
 * "I'll forward this to admin" while the flag stays false.
 */

function faq_bot_entries() {
    return [
        ['keywords' => ['halo', 'hai', 'hallo', 'permisi', 'min', 'kak', 'tanya dong', 'assalamualaikum'],
         'answer' => 'Halo! Saya asisten otomatis Premium Store. Saya bisa bantu jawab pertanyaan umum seputar daftar akun, top up saldo, cara beli produk, voucher, dan garansi. Kalau butuh bantuan lebih lanjut, admin akan segera membalas.',
         'handoff' => false],

        ['keywords' => ['daftar', 'cara daftar', 'buat akun', 'registrasi', 'sign up', 'cara register'],
         'answer' => 'Cara daftar: klik "Daftar Gratis" di halaman utama, isi nama, nomor telepon, email, username, dan password (atau langsung pakai "Masuk dengan Google"). Akun langsung aktif setelah daftar, tidak perlu verifikasi email.',
         'handoff' => false],

        ['keywords' => ['lupa password', 'lupa sandi', 'reset password', 'reset sandi', 'tidak bisa masuk', 'gagal masuk', 'gagal login', 'ganti password'],
         'answer' => 'Untuk lupa password: klik "Lupa password?" di halaman masuk, masukkan email terdaftar, lalu cek email (termasuk folder spam) untuk link reset. Kalau akun kamu daftar pakai Google, gunakan tombol "Masuk dengan Google" saja, tidak perlu password.',
         'handoff' => false],

        // Checked before the generic top-up entry below, so "deposit belum
        // masuk" (a specific complaint) isn't swallowed by the bare
        // "deposit" keyword there first - order matters here. Balance not
        // arriving is account-specific, so this always hands off even
        // though the bot recognizes the topic.
        ['keywords' => ['berapa lama', 'kapan masuk', 'belum masuk', 'deposit pending'],
         'answer' => 'Deposit via QRIS otomatis biasanya masuk dalam hitungan detik setelah pembayaran berhasil. Deposit manual (upload bukti) perlu dikonfirmasi admin, biasanya diproses dalam beberapa jam. Saya teruskan ke admin untuk mengecek status deposit Anda.',
         'handoff' => true],

        ['keywords' => ['top up', 'topup', 'deposit', 'isi saldo', 'cara deposit', 'tambah saldo', 'cara isi saldo'],
         'answer' => 'Cara top up saldo: buka menu "Deposit Saldo", pilih nominal, lalu pilih metode: QRIS otomatis via Midtrans (saldo langsung masuk) atau QRIS manual (upload bukti transfer, dikonfirmasi admin, ada batas waktu 1 jam).',
         'handoff' => false],

        ['keywords' => ['metode bayar', 'metode pembayaran', 'pembayaran', 'qris', 'transfer bank', 'midtrans', 'bayar pakai apa', 'cara bayar'],
         'answer' => 'Metode pembayaran yang tersedia: QRIS otomatis (Midtrans - langsung terkonfirmasi) dan QRIS manual (scan lalu upload bukti, dikonfirmasi admin). Semua bisa dipilih di halaman Deposit Saldo.',
         'handoff' => false],

        ['keywords' => ['cara beli', 'cara order', 'cara checkout', 'mau beli', 'cara pesan', 'cara membeli'],
         'answer' => 'Cara beli produk: buka menu "Toko", pilih produk, pilih varian/durasi, lalu klik Beli. Saldo kamu akan terpotong otomatis dan akun langsung aktif - tidak perlu konfirmasi manual, asalkan saldo mencukupi.',
         'handoff' => false],

        // Where-are-my-credentials is account-specific -> hand off.
        ['keywords' => ['dimana lihat akun', 'kredensial', 'hasil pembelian', 'email password akun', 'akun mana', 'setelah beli lihat dimana'],
         'answer' => 'Detail akun (email/password/info login) dari pembelian yang sudah "Selesai" bisa dilihat di menu "Riwayat Pesanan". Kalau Anda tidak menemukannya di sana, saya teruskan ke admin untuk membantu mengecek.',
         'handoff' => true],

        ['keywords' => ['stok', 'stock', 'ready', 'tersedia', 'ada stok', 'habis'],
         'answer' => 'Cek ketersediaan stok produk real-time di menu "Cek Stock" pada sidebar. Kalau stok habis untuk suatu varian, biasanya akan tersedia lagi setelah admin menambah stok.',
         'handoff' => false],

        ['keywords' => ['voucher', 'redeem', 'kode voucher', 'kode promo', 'cara pakai voucher'],
         'answer' => 'Cara pakai voucher: buka menu "Redeem Voucher", masukkan kode voucher kamu, lalu klik Redeem. Saldo bonus akan langsung masuk kalau kodenya valid dan belum pernah dipakai sebelumnya.',
         'handoff' => false],

        // Checked before the generic "garansi" entry below, so a specific
        // complaint ("klaim garansi", "akun bermasalah") always wins over
        // the broader activation FAQ that bare "garansi" would otherwise
        // match first - order matters here. Always hands off: an actual
        // warranty claim needs a human to review the account.
        ['keywords' => ['klaim garansi', 'cara klaim', 'akun bermasalah', 'akun error', 'tidak bisa login akun', 'akun ke-logout', 'akun kena limit'],
         'answer' => 'Kalau akun yang kamu beli bermasalah, buka menu "Claim Garansi", pilih akun yang bermasalah, dan jelaskan kendalanya. Saya teruskan ke admin agar bisa langsung ditindaklanjuti.',
         'handoff' => true],

        ['keywords' => ['aktivasi garansi', 'cara aktivasi', 'garansi'],
         'answer' => 'Untuk mengaktifkan garansi produk yang sudah dibeli, buka menu "Aktivasi Garansi" dan pilih pesanan yang ingin diaktifkan. Kalau akun yang dibeli bermasalah, ajukan lewat menu "Claim Garansi".',
         'handoff' => false],

        ['keywords' => ['reseller', 'harga reseller', 'token reseller', 'jadi reseller', 'daftar reseller'],
         'answer' => 'Untuk jadi reseller dan dapat harga khusus, kamu perlu token reseller saat mendaftar. Hubungi admin lewat WhatsApp untuk info lebih lanjut soal token reseller.',
         'handoff' => false],

        ['keywords' => ['referral', 'ajak teman', 'kode referral', 'bonus referral', 'undang teman'],
         'answer' => 'Program referral: bagikan link referral kamu (lihat di menu "Profil") ke teman. Begitu mereka selesai daftar pakai link/kode kamu, kalian berdua dapat bonus saldo.',
         'handoff' => false],

        ['keywords' => ['riwayat pesanan', 'history pesanan', 'pesanan saya', 'status pesanan'],
         'answer' => 'Lihat semua riwayat pembelian kamu (termasuk status: selesai/pending/expired/batal) di menu "Riwayat Pesanan". Klik salah satu pesanan untuk lihat detail lengkap dan timeline statusnya.',
         'handoff' => false],

        ['keywords' => ['riwayat saldo', 'mutasi saldo', 'history saldo', 'cek saldo'],
         'answer' => 'Lihat semua riwayat mutasi saldo (deposit, pembelian, voucher, referral) di menu "Riwayat Saldo".',
         'handoff' => false],

        ['keywords' => ['notifikasi', 'lonceng', 'pemberitahuan'],
         'answer' => 'Ikon lonceng di pojok kiri atas akan menyala kalau ada update baru: pesanan selesai, deposit disetujui, voucher berhasil, atau balasan admin di chat.',
         'handoff' => false],

        ['keywords' => ['biaya', 'gratis', 'berbayar', 'harga', 'berapa harga'],
         'answer' => 'Daftar akun gratis, tidak ada biaya pendaftaran. Harga produk bervariasi tergantung produk dan durasi, bisa dilihat lengkap di menu "Toko".',
         'handoff' => false],

        // Explicit complaint -> always hand off.
        ['keywords' => ['error', 'eror', 'bug', 'gangguan', 'rusak', 'tidak berfungsi', 'gagal terus', 'loading terus', 'komplain'],
         'answer' => 'Mohon maaf atas kendalanya. Saya teruskan ke admin agar dapat segera memeriksa masalah ini.',
         'handoff' => true],

        ['keywords' => ['hubungi admin', 'mau bicara admin', 'bicara sama admin', 'ngomong sama admin', 'chat admin', 'minta admin', 'customer service', 'bicara sama manusia', 'operator'],
         'answer' => 'Baik, saya akan meneruskan percakapan ini kepada admin agar dapat membantu lebih lanjut.',
         'handoff' => true],

        ['keywords' => ['terima kasih', 'makasih', 'thanks', 'oke', 'baik', 'sip', 'siap'],
         'answer' => 'Sama-sama! Kalau ada pertanyaan lain seputar Premium Store, silakan tanyakan lagi ya.',
         'handoff' => false],
    ];
}

const FAQ_BOT_GENERIC_ACK = 'Terima kasih, pesan Anda sudah kami catat. Untuk pertanyaan ini saya perlu meneruskannya ke admin agar bisa dibantu lebih lanjut.';

/**
 * Whole-word/phrase match, not raw substring - "min" (informal for "admin",
 * used as a greeting) must not match inside the word "admin" itself, which
 * a plain strpos() would do since "min" is literally the tail of "admin".
 */
function faq_bot_contains_keyword($haystack, $keyword) {
    return preg_match('/(?<![\p{L}\p{N}])' . preg_quote($keyword, '/') . '(?![\p{L}\p{N}])/u', $haystack) === 1;
}

/**
 * Returns ['reply' => string, 'handoff' => bool]. Never returns null - if
 * nothing matches, sends the generic acknowledgement and hands off, same
 * philosophy as chatBot.ts: don't guess, don't stay silent.
 */
function faq_bot_reply($message) {
    $clean = mb_strtolower(trim($message));

    foreach (faq_bot_entries() as $entry) {
        foreach ($entry['keywords'] as $kw) {
            if (faq_bot_contains_keyword($clean, $kw)) {
                return ['reply' => $entry['answer'], 'handoff' => $entry['handoff']];
            }
        }
    }

    return ['reply' => FAQ_BOT_GENERIC_ACK, 'handoff' => true];
}
