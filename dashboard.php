<?php
$page_title = "Dashboard";
$current_page = "dashboard";
$base_path = "";
require_once 'backend/includes/head.php';
?>

<div class="flex h-screen overflow-hidden">
  <?php require_once 'backend/includes/buyer-sidebar.php'; ?>

  <!-- Main -->
  <div class="flex-1 flex flex-col overflow-hidden">
    <!-- Top Bar -->
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between flex-shrink-0">
      <div class="flex items-center gap-3">
        <button class="lg:hidden text-gray-500 hover:text-gray-700" onclick="toggleSidebar()">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <div>
          <h1 class="text-xl font-bold text-gray-800">Dashboard</h1>
          <p class="text-xs text-gray-400">Selamat datang kembali!</p>
        </div>
      </div>
      <div class="flex items-center gap-3">
        <div id="header-balance" class="bg-primary text-white text-sm font-bold px-4 py-2 rounded-xl flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="M2 10h20"/></svg>
          <span id="header-balance-text">Rp 0</span>
        </div>
      </div>
    </header>

    <!-- Content -->
    <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
      <!-- Stats -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
          <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
          </div>
          <p class="text-gray-400 text-xs font-medium">Saldo</p>
          <p id="stat-balance" class="text-2xl font-bold text-gray-800 mt-0.5">Rp 0</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
          <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
          </div>
          <p class="text-gray-400 text-xs font-medium">Total Pesanan</p>
          <p id="stat-orders" class="text-2xl font-bold text-gray-800 mt-0.5">0</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
          <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
          </div>
          <p class="text-gray-400 text-xs font-medium">Garansi Aktif</p>
          <p id="stat-warranty" class="text-2xl font-bold text-gray-800 mt-0.5">0</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
          <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
          </div>
          <p class="text-gray-400 text-xs font-medium">Pesanan Pending</p>
          <p id="stat-pending" class="text-2xl font-bold text-gray-800 mt-0.5">0</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Quick Actions -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm mb-5">
            <h2 class="text-base font-bold text-gray-800 mb-4">Aksi Cepat</h2>
            <div class="grid grid-cols-2 gap-3">
              <a href="toko.php" class="flex flex-col items-center gap-2 p-3 rounded-xl border-2 border-gray-100 hover:border-primary hover:bg-primary/5 transition-all text-center">
                <div class="w-9 h-9 bg-primary/10 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-700">Toko</span>
              </a>
              <a href="deposit.php" class="flex flex-col items-center gap-2 p-3 rounded-xl border-2 border-gray-100 hover:border-gold hover:bg-gold/5 transition-all text-center">
                <div class="w-9 h-9 bg-gold/10 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-700">Deposit</span>
              </a>
              <a href="pesanan.php" class="flex flex-col items-center gap-2 p-3 rounded-xl border-2 border-gray-100 hover:border-blue-400 hover:bg-blue-50 transition-all text-center">
                <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-700">Pesanan</span>
              </a>
              <a href="garansi.php" class="flex flex-col items-center gap-2 p-3 rounded-xl border-2 border-gray-100 hover:border-green-400 hover:bg-green-50 transition-all text-center">
                <div class="w-9 h-9 bg-green-50 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-700">Garansi</span>
              </a>
            </div>
          </div>

          <!-- Balance Card -->
          <div class="balance-card rounded-2xl">
            <p class="text-white/60 text-xs font-medium mb-1">SALDO KAMU</p>
            <p id="balance-card-amount" class="text-3xl font-bold text-white">Rp 0</p>
            <a href="deposit.php" class="inline-block mt-4 bg-gold text-black text-xs font-bold px-4 py-2 rounded-lg hover:bg-yellow-300 transition-colors">+ Deposit Saldo</a>
          </div>
        </div>

        <!-- Recent Orders -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
              <h2 class="text-base font-bold text-gray-800">Pesanan Terbaru</h2>
              <a href="pesanan.php" class="text-primary text-sm font-semibold hover:underline">Lihat Semua</a>
            </div>
            <div id="recent-orders" class="p-5">
              <div class="text-center py-8 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <p class="text-sm">Belum ada pesanan</p>
                <a href="toko.php" class="inline-block mt-3 text-primary font-semibold text-sm hover:underline">Belanja Sekarang</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<?php require_once 'backend/includes/footer.php'; ?>
<script>
// Auth check
auth.onAuthStateChanged(async user => {
  if (!user) { window.location.href = 'login.php'; return; }

  try {
    const snap = await db.collection('users').doc(user.uid).get();
    const data = snap.data() || {};
    const balance = data.balance || 0;

    document.getElementById('stat-balance').textContent = 'Rp ' + balance.toLocaleString('id-ID');
    document.getElementById('header-balance-text').textContent = 'Rp ' + balance.toLocaleString('id-ID');
    document.getElementById('balance-card-amount').textContent = 'Rp ' + balance.toLocaleString('id-ID');

    // Load orders count
    const ordersSnap = await db.collection('orders').where('user_id', '==', user.uid).get();
    document.getElementById('stat-orders').textContent = ordersSnap.size;

    let pending = 0;
    const rows = [];
    ordersSnap.forEach(doc => {
      const d = doc.data();
      if (d.status === 'pending') pending++;
      rows.push(d);
    });
    document.getElementById('stat-pending').textContent = pending;

    // Warranty count
    const wSnap = await db.collection('warranties').where('user_id', '==', user.uid).where('status', '==', 'aktif').get();
    document.getElementById('stat-warranty').textContent = wSnap.size;

    // Recent orders
    rows.sort((a,b) => (b.created_at?.seconds||0) - (a.created_at?.seconds||0));
    const recent = rows.slice(0, 5);
    if (recent.length > 0) {
      const statusMap = {
        selesai: ['bg-green-100 text-green-700','Selesai'],
        pending: ['bg-yellow-100 text-yellow-700','Pending'],
        expired: ['bg-gray-100 text-gray-500','Expired'],
        batal:   ['bg-red-100 text-red-600','Batal'],
      };
      const html = recent.map(o => {
        const [sc, sl] = statusMap[o.status] || ['bg-gray-100 text-gray-500', o.status];
        const d = o.created_at ? new Date(o.created_at.seconds*1000).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}) : '-';
        return `<div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
          <div class="min-w-0">
            <p class="text-sm font-semibold text-gray-800 truncate">${o.product_name||''}</p>
            <p class="text-xs text-gray-400 mt-0.5">${o.invoice||''} · ${d}</p>
          </div>
          <div class="flex items-center gap-3 flex-shrink-0 ml-3">
            <span class="text-sm font-bold text-gray-700">Rp ${(o.price||0).toLocaleString('id-ID')}</span>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-lg ${sc}">${sl}</span>
          </div>
        </div>`;
      }).join('');
      document.getElementById('recent-orders').innerHTML = html;
    }
  } catch(e) { console.error(e); }
});
</script>
