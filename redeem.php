<?php
$page_title = "Redeem Voucher";
$current_page = "redeem";
$base_path = "";
require_once 'includes/head.php';
?>
<div class="flex h-screen overflow-hidden">
  <?php require_once 'includes/buyer-sidebar.php'; ?>
  <div class="flex-1 flex flex-col overflow-hidden">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-3 flex-shrink-0">
      <button class="lg:hidden text-gray-500" onclick="toggleSidebar()"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
      <h1 class="text-xl font-bold text-gray-800">Redeem Voucher</h1>
    </header>
    <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
      <div class="max-w-xl mx-auto space-y-5">
        <div class="balance-card">
          <p class="text-white/60 text-xs mb-1">SALDO KAMU</p>
          <p id="user-balance" class="text-3xl font-bold text-white">Rp 0</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
          <h2 class="font-bold text-gray-800 mb-1">Masukkan Kode Voucher</h2>
          <p class="text-sm text-gray-400 mb-4">Masukkan kode voucher yang kamu punya untuk mendapatkan saldo gratis.</p>
          <div class="flex gap-3">
            <input type="text" id="voucher-input" placeholder="Contoh: NAISEY2026" class="input-field uppercase tracking-widest font-bold flex-1" oninput="this.value=this.value.toUpperCase()">
            <button onclick="redeemVoucher()" id="btn-redeem" class="btn-primary px-5 flex-shrink-0">Redeem</button>
          </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
          <h3 class="font-bold text-gray-800 mb-4">Riwayat Redeem</h3>
          <div id="redeem-history">
            <div class="text-center py-8">
              <svg class="w-12 h-12 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
              <p class="text-gray-400 text-sm">Belum pernah redeem voucher</p>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
<?php require_once 'includes/footer.php'; ?>
<script>
let currentUser = null;
auth.onAuthStateChanged(async user => {
  if (!user) { window.location.href = 'login.php'; return; }
  currentUser = user;
  const snap = await db.collection('users').doc(user.uid).get();
  document.getElementById('user-balance').textContent = 'Rp ' + (snap.data()?.balance||0).toLocaleString('id-ID');
  loadHistory();
});

async function loadHistory() {
  const snap = await db.collection('balance_history').where('user_id','==',currentUser.uid).where('type','==','voucher').orderBy('created_at','desc').get();
  if (snap.empty) return;
  document.getElementById('redeem-history').innerHTML = `<div class="space-y-2">${snap.docs.map(d => {
    const h = d.data();
    const dt = h.created_at ? new Date(h.created_at.seconds*1000).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'}) : '-';
    return `<div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
      <div><p class="text-sm font-semibold text-gray-800">🎟️ ${h.description||'Voucher'}</p><p class="text-xs text-gray-400">${dt}</p></div>
      <span class="font-bold text-green-600">+Rp ${h.amount.toLocaleString('id-ID')}</span>
    </div>`;
  }).join('')}</div>`;
}

async function redeemVoucher() {
  const code = document.getElementById('voucher-input').value.trim().toUpperCase();
  if (!code) { showToast('Masukkan kode voucher','error'); return; }
  const btn = document.getElementById('btn-redeem');
  btn.disabled = true; btn.innerHTML = '<span class="spinner"></span>';

  try {
    const vSnap = await db.collection('vouchers').doc(code).get();
    if (!vSnap.exists) throw new Error('Kode voucher tidak valid');
    const v = vSnap.data();
    if (!v.is_active) throw new Error('Voucher sudah tidak aktif');
    if (v.used_count >= v.max_uses) throw new Error('Voucher sudah habis digunakan');

    // Check if user already used this voucher
    const usedSnap = await db.collection('voucher_uses').where('user_id','==',currentUser.uid).where('voucher_code','==',code).get();
    if (!usedSnap.empty) throw new Error('Kamu sudah pernah menggunakan voucher ini');

    await db.runTransaction(async t => {
      const vRef = db.collection('vouchers').doc(code);
      const uRef = db.collection('users').doc(currentUser.uid);
      t.update(vRef, { used_count: firebase.firestore.FieldValue.increment(1) });
      t.update(uRef, { balance: firebase.firestore.FieldValue.increment(v.amount) });
      t.set(db.collection('voucher_uses').doc(), { user_id: currentUser.uid, voucher_code: code, amount: v.amount, created_at: firebase.firestore.FieldValue.serverTimestamp() });
      t.set(db.collection('balance_history').doc(), { user_id: currentUser.uid, type: 'voucher', amount: v.amount, description: 'Redeem Voucher: ' + code, created_at: firebase.firestore.FieldValue.serverTimestamp() });
    });

    showToast('Voucher berhasil! Saldo +Rp ' + v.amount.toLocaleString('id-ID'),'success');
    document.getElementById('voucher-input').value = '';
    const snap2 = await db.collection('users').doc(currentUser.uid).get();
    document.getElementById('user-balance').textContent = 'Rp ' + (snap2.data()?.balance||0).toLocaleString('id-ID');
    loadHistory();
  } catch(e) {
    showToast(e.message || 'Gagal redeem voucher','error');
  }
  btn.disabled = false; btn.textContent = 'Redeem';
}
</script>
