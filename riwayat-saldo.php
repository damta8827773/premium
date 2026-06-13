<?php
$page_title = "Riwayat Saldo";
$current_page = "riwayat-saldo";
$base_path = "";
require_once 'includes/head.php';
?>
<div class="flex h-screen overflow-hidden">
  <?php require_once 'includes/buyer-sidebar.php'; ?>
  <div class="flex-1 flex flex-col overflow-hidden">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-3 flex-shrink-0">
      <button class="lg:hidden text-gray-500" onclick="toggleSidebar()"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
      <h1 class="text-xl font-bold text-gray-800">Riwayat Saldo</h1>
    </header>
    <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
      <div class="max-w-2xl mx-auto space-y-5">
        <!-- Balance Card -->
        <div class="balance-card">
          <p class="text-white/60 text-xs font-medium mb-1">SALDO SAAT INI</p>
          <p id="user-balance" class="text-3xl font-bold text-gold">Rp 0</p>
          <div class="grid grid-cols-3 gap-4 mt-4">
            <div><p class="text-white/50 text-xs">TOTAL TRANSAKSI</p><p id="stat-total" class="text-white font-bold text-lg">0</p></div>
            <div><p class="text-white/50 text-xs">TOTAL MASUK</p><p id="stat-in" class="text-green-400 font-bold text-lg">+Rp 0</p></div>
            <div><p class="text-white/50 text-xs">TOTAL KELUAR</p><p id="stat-out" class="text-red-400 font-bold text-lg">-Rp 0</p></div>
          </div>
        </div>

        <!-- Filter tabs -->
        <div class="tab-scroll flex gap-2 pb-1">
          <button class="tab-btn active" onclick="setFilter('semua',this)">Semua</button>
          <button class="tab-btn" onclick="setFilter('deposit',this)">Deposit</button>
          <button class="tab-btn" onclick="setFilter('topup',this)">Top Up</button>
          <button class="tab-btn" onclick="setFilter('voucher',this)">Voucher</button>
          <button class="tab-btn" onclick="setFilter('pembelian',this)">Pembelian</button>
          <button class="tab-btn" onclick="setFilter('refund',this)">Refund</button>
          <button class="tab-btn" onclick="setFilter('adjustment',this)">Adjustment</button>
        </div>

        <div id="history-list"><div class="text-center py-10"><div class="spinner-dark mx-auto mb-2"></div><p class="text-sm text-gray-400">Memuat riwayat...</p></div></div>
      </div>
    </main>
  </div>
</div>
<?php require_once 'includes/footer.php'; ?>
<script>
let allHistory = [];
let currentFilter = 'semua';

auth.onAuthStateChanged(async user => {
  if (!user) { window.location.href = 'login.php'; return; }
  const snap = await db.collection('users').doc(user.uid).get();
  const balance = snap.data()?.balance || 0;
  document.getElementById('user-balance').textContent = 'Rp ' + balance.toLocaleString('id-ID');

  const hSnap = await db.collection('balance_history').where('user_id','==',user.uid).orderBy('created_at','desc').limit(100).get();
  allHistory = hSnap.docs.map(d => ({id:d.id,...d.data()}));

  let totalIn=0, totalOut=0;
  allHistory.forEach(h => { if(h.amount>0) totalIn+=h.amount; else totalOut+=Math.abs(h.amount); });
  document.getElementById('stat-total').textContent = allHistory.length;
  document.getElementById('stat-in').textContent = '+Rp '+totalIn.toLocaleString('id-ID');
  document.getElementById('stat-out').textContent = '-Rp '+totalOut.toLocaleString('id-ID');
  renderHistory();
});

function setFilter(f, btn) {
  currentFilter = f;
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  renderHistory();
}

function renderHistory() {
  const filtered = currentFilter === 'semua' ? allHistory : allHistory.filter(h => h.type === currentFilter);
  if (!filtered.length) {
    document.getElementById('history-list').innerHTML = `<div class="text-center py-14 bg-white rounded-2xl border border-gray-100">
      <svg class="w-14 h-14 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
      <p class="text-gray-500 font-medium">Belum Ada Transaksi</p>
      <p class="text-gray-400 text-sm mt-1">Mulai deposit saldo untuk belanja lebih mudah</p>
      <a href="deposit.php" class="inline-block mt-4 bg-primary text-white text-sm font-semibold px-5 py-2.5 rounded-xl">+ Deposit Sekarang</a>
    </div>`;
    return;
  }
  const iconMap = { deposit:'💰', topup:'⬆️', voucher:'🎟️', pembelian:'🛒', refund:'↩️', adjustment:'⚙️' };
  document.getElementById('history-list').innerHTML = `<div class="bg-white rounded-2xl border border-gray-100 shadow-sm divide-y divide-gray-50">
    ${filtered.map(h => {
      const isIn = h.amount > 0;
      const d = h.created_at ? new Date(h.created_at.seconds*1000).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}) : '-';
      return `<div class="flex items-center gap-4 p-4">
        <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-lg flex-shrink-0">${iconMap[h.type]||'💳'}</div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-semibold text-gray-800 truncate">${h.description||h.type}</p>
          <p class="text-xs text-gray-400 mt-0.5">${d}</p>
        </div>
        <span class="font-bold text-base flex-shrink-0 ${isIn?'text-green-600':'text-red-500'}">${isIn?'+':''} Rp ${Math.abs(h.amount).toLocaleString('id-ID')}</span>
      </div>`;
    }).join('')}
  </div>`;
}
</script>
