<?php
require_once 'backend/config/app.php';
$page_title = "Deposit Saldo";
$current_page = "deposit";
$base_path = "";
require_once 'backend/includes/head.php';
?>
<div class="flex h-screen overflow-hidden">
  <?php require_once 'backend/includes/buyer-sidebar.php'; ?>
  <div class="flex-1 flex flex-col overflow-hidden">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-3 flex-shrink-0">
      <button class="lg:hidden text-gray-500" onclick="toggleSidebar()">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>
      <h1 class="text-xl font-bold text-gray-800">Deposit Saldo</h1>
    </header>
    <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
      <div class="max-w-2xl mx-auto space-y-5">

        <!-- Balance Card -->
        <div class="balance-card">
          <p class="text-white/60 text-xs font-medium mb-1">SALDO KAMU</p>
          <p id="user-balance" class="text-3xl font-bold text-white">Rp 0</p>
          <a href="riwayat-saldo.php" class="inline-block mt-3 text-white/60 text-xs hover:text-white transition-colors">Lihat riwayat →</a>
        </div>

        <!-- Info -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-4 flex gap-3">
          <svg class="w-5 h-5 text-yellow-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <p class="text-sm text-yellow-700">Deposit saldo dilakukan melalui <strong>pembayaran QRIS</strong> (otomatis via Midtrans) atau <strong>transfer manual</strong> dengan konfirmasi admin. Tidak ada biaya tambahan.</p>
        </div>

        <!-- Method Selector -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
          <p class="font-bold text-gray-800 mb-4">Pilih Metode Pembayaran</p>
          <div class="grid grid-cols-2 gap-3">
            <label class="method-option cursor-pointer">
              <input type="radio" name="method" value="midtrans" checked class="hidden" onchange="selectMethod('midtrans')">
              <div class="method-box border-2 border-primary bg-primary/5 rounded-xl p-4 text-center transition-all">
                <svg class="w-8 h-8 mx-auto mb-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <p class="text-sm font-bold text-primary">Otomatis (QRIS)</p>
                <p class="text-xs text-gray-400 mt-0.5">Midtrans · Saldo langsung masuk</p>
              </div>
            </label>
            <label class="method-option cursor-pointer">
              <input type="radio" name="method" value="manual" class="hidden" onchange="selectMethod('manual')">
              <div class="method-box border-2 border-gray-200 rounded-xl p-4 text-center transition-all">
                <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <p class="text-sm font-bold text-gray-600">Manual (QRIS)</p>
                <p class="text-xs text-gray-400 mt-0.5">Upload bukti · Konfirmasi admin</p>
              </div>
            </label>
          </div>
        </div>

        <!-- Amount -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
          <p class="font-bold text-gray-800 mb-4">Pilih Nominal</p>
          <div class="grid grid-cols-3 gap-2 mb-4">
            <?php foreach([10000,25000,50000,100000,200000,500000] as $amt): ?>
            <button onclick="selectAmount(<?=$amt?>)" class="amount-btn text-sm"><?= 'Rp '.number_format($amt,0,',','.') ?></button>
            <?php endforeach; ?>
          </div>
          <p class="text-sm font-semibold text-gray-700 mb-2">Atau Masukkan Nominal</p>
          <div class="relative">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
            <input type="number" id="custom-amount" placeholder="Masukkan nominal" class="input-field pl-10 text-sm" oninput="setCustomAmount(this.value)" min="1000">
          </div>
          <p class="text-xs text-gray-400 mt-1.5">Minimum Rp 1.000</p>
        </div>

        <!-- Manual upload (hidden by default) -->
        <div id="manual-section" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hidden">
          <p class="font-bold text-gray-800 mb-3">QRIS untuk Pembayaran Manual</p>
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="text-center">
              <img src="frontend/image/qris 1.png" alt="QRIS 1" class="w-full max-w-[160px] mx-auto rounded-xl border border-gray-200">
              <p class="text-xs text-gray-400 mt-2">QRIS 1</p>
            </div>
            <div class="text-center">
              <img src="frontend/image/qris 2.png" alt="QRIS 2" class="w-full max-w-[160px] mx-auto rounded-xl border border-gray-200">
              <p class="text-xs text-gray-400 mt-2">QRIS 2</p>
            </div>
          </div>
          <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-3 text-xs text-yellow-700 mb-4">
            Scan QRIS di atas sesuai nominal, lalu upload bukti pembayaran (screenshot) di bawah ini.
          </div>
          <p class="text-sm font-semibold text-gray-700 mb-2">Upload Bukti Pembayaran</p>
          <label class="upload-area block cursor-pointer" for="proof-input">
            <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="text-sm text-gray-400 font-medium">Klik atau drag foto di sini</p>
            <p class="text-xs text-gray-400 mt-1">PNG, JPG maksimal 5MB</p>
          </label>
          <input type="file" id="proof-input" accept="image/*" class="hidden" onchange="previewProof(this)">
          <img id="proof-preview" class="hidden mt-3 rounded-xl max-h-48 mx-auto border" alt="Preview">
          <input type="text" id="manual-note" placeholder="Catatan (opsional)" class="input-field text-sm mt-3">
        </div>

        <!-- Submit -->
        <button id="btn-deposit" onclick="processDeposit()" class="btn-gold w-full text-base" disabled>
          Lanjut ke Pembayaran
        </button>
      </div>
    </main>
  </div>
</div>

<!-- Midtrans Snap -->
<script src="https://app.midtrans.com/snap/snap.js" data-client-key="<?= htmlspecialchars(MIDTRANS_CLIENT_KEY) ?>"></script>

<?php require_once 'backend/includes/footer.php'; ?>
<script>
let selectedAmount = 0;
let selectedMethod = 'midtrans';
let currentUser = null;
let proofFile = null;

auth.onAuthStateChanged(async user => {
  if (!user) { window.location.href = 'login.php'; return; }
  currentUser = user;
  const snap = await db.collection('users').doc(user.uid).get();
  const balance = snap.data()?.balance || 0;
  document.getElementById('user-balance').textContent = 'Rp ' + balance.toLocaleString('id-ID');
});

function selectMethod(method) {
  selectedMethod = method;
  document.querySelectorAll('.method-box').forEach(b => {
    b.className = b.className.replace('border-primary bg-primary/5','border-gray-200');
  });
  event.currentTarget.closest('label').querySelector('.method-box').className =
    event.currentTarget.closest('label').querySelector('.method-box').className.replace('border-gray-200','border-primary bg-primary/5');
  document.getElementById('manual-section').classList.toggle('hidden', method !== 'manual');
  updateBtn();
}

function selectAmount(amt) {
  selectedAmount = amt;
  document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('selected'));
  event.currentTarget.classList.add('selected');
  document.getElementById('custom-amount').value = '';
  updateBtn();
}
function setCustomAmount(val) {
  selectedAmount = parseInt(val) || 0;
  document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('selected'));
  updateBtn();
}
function updateBtn() {
  const btn = document.getElementById('btn-deposit');
  btn.disabled = selectedAmount < 1000;
  btn.textContent = selectedMethod === 'manual' ? 'Kirim Bukti Pembayaran' : 'Lanjut ke Pembayaran';
}

function previewProof(input) {
  proofFile = input.files[0];
  if (!proofFile) return;
  const reader = new FileReader();
  reader.onload = e => {
    const img = document.getElementById('proof-preview');
    img.src = e.target.result;
    img.classList.remove('hidden');
  };
  reader.readAsDataURL(proofFile);
}

async function processDeposit() {
  if (selectedAmount < 1000) { showToast('Nominal minimal Rp 1.000','error'); return; }
  const btn = document.getElementById('btn-deposit');
  btn.disabled = true; btn.innerHTML = '<span class="spinner"></span> Memproses...';

  if (selectedMethod === 'midtrans') {
    await payMidtrans();
  } else {
    await payManual();
  }
  btn.disabled = false; btn.textContent = selectedMethod === 'manual' ? 'Kirim Bukti Pembayaran' : 'Lanjut ke Pembayaran';
}

async function payMidtrans() {
  try {
    const orderId = 'DEP-' + currentUser.uid.slice(0,8) + '-' + Date.now();
    const res = await fetch('backend/api/midtrans-create.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({ order_id: orderId, amount: selectedAmount, user_id: currentUser.uid, email: currentUser.email, name: currentUser.displayName || 'User' })
    });
    const data = await res.json();
    if (!data.token) throw new Error(data.error || 'Gagal membuat transaksi');

    // Save pending deposit to Firestore
    await db.collection('deposits').doc(orderId).set({
      id: orderId, user_id: currentUser.uid, amount: selectedAmount,
      method: 'midtrans', status: 'pending', midtrans_order_id: orderId,
      snap_token: data.token, created_at: firebase.firestore.FieldValue.serverTimestamp()
    });

    snap.pay(data.token, {
      onSuccess: async result => {
        await db.collection('deposits').doc(orderId).update({ status: 'success', completed_at: firebase.firestore.FieldValue.serverTimestamp() });
        await db.collection('users').doc(currentUser.uid).update({ balance: firebase.firestore.FieldValue.increment(selectedAmount) });
        await db.collection('balance_history').add({ user_id: currentUser.uid, type: 'deposit', amount: selectedAmount, description: 'Deposit via Midtrans', created_at: firebase.firestore.FieldValue.serverTimestamp() });
        showToast('Deposit berhasil! Saldo sudah masuk.','success');
        setTimeout(() => location.reload(), 2000);
      },
      onPending: () => showToast('Pembayaran pending. Saldo akan masuk setelah dikonfirmasi.','info'),
      onError: () => showToast('Pembayaran gagal.','error'),
      onClose: () => showToast('Transaksi dibatalkan.','info')
    });
  } catch(e) {
    showToast(e.message || 'Gagal memproses pembayaran','error');
  }
}

async function payManual() {
  if (!proofFile) { showToast('Upload bukti pembayaran terlebih dahulu','error'); return; }
  try {
    const orderId = 'MAN-' + currentUser.uid.slice(0,8) + '-' + Date.now();
    // Upload proof to Firebase Storage
    const storageRef = storage.ref('deposit_proofs/' + orderId + '_' + proofFile.name);
    await storageRef.put(proofFile);
    const proofUrl = await storageRef.getDownloadURL();

    await db.collection('deposits').doc(orderId).set({
      id: orderId, user_id: currentUser.uid, amount: selectedAmount,
      method: 'manual', status: 'pending', proof_image: proofUrl,
      note: document.getElementById('manual-note').value || '',
      created_at: firebase.firestore.FieldValue.serverTimestamp()
    });

    showToast('Bukti pembayaran terkirim! Menunggu konfirmasi admin.','success');
    setTimeout(() => window.location.href = 'riwayat-saldo.php', 2000);
  } catch(e) {
    showToast('Gagal mengirim bukti: ' + (e.message || 'Coba lagi'),'error');
  }
}
</script>
