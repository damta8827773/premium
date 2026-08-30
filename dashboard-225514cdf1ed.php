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
    <main class="flex-1 overflow-y-auto p-6 bg-app">
      <!-- Stats -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card-premium p-5">
          <div class="flex items-center justify-between mb-3">
            <div class="stat-card-icon" style="--sc-a:#dcfce7;--sc-b:#bbf7d0;--sc-glow:rgba(34,197,94,0.08)">
              <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
          </div>
          <p class="text-gray-400 text-xs font-medium">Saldo</p>
          <p id="stat-balance" class="text-2xl font-bold text-gray-800 mt-0.5">Rp 0</p>
        </div>
        <div class="card-premium p-5">
          <div class="flex items-center justify-between mb-3">
            <div class="stat-card-icon" style="--sc-a:#dbeafe;--sc-b:#bfdbfe;--sc-glow:rgba(59,130,246,0.08)">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
          </div>
          <p class="text-gray-400 text-xs font-medium">Total Pesanan</p>
          <p id="stat-orders" class="text-2xl font-bold text-gray-800 mt-0.5">0</p>
        </div>
        <div class="card-premium p-5">
          <div class="flex items-center justify-between mb-3">
            <div class="stat-card-icon" style="--sc-a:#fef9c3;--sc-b:#fde68a;--sc-glow:rgba(234,179,8,0.1)">
              <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
          </div>
          <p class="text-gray-400 text-xs font-medium">Garansi Aktif</p>
          <p id="stat-warranty" class="text-2xl font-bold text-gray-800 mt-0.5">0</p>
        </div>
        <div class="card-premium p-5">
          <div class="flex items-center justify-between mb-3">
            <div class="stat-card-icon" style="--sc-a:#f3e8ff;--sc-b:#e9d5ff;--sc-glow:rgba(168,85,247,0.08)">
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
          <div class="card-premium p-5 mb-5">
            <h2 class="text-base font-bold text-gray-800 mb-4">Aksi Cepat</h2>
            <div class="grid grid-cols-2 gap-3">
              <a href="toko/e3514627bb16" class="flex flex-col items-center gap-2 p-3 rounded-xl border-2 border-gray-100 hover:border-primary hover:bg-primary/5 transition-all text-center">
                <div class="w-9 h-9 bg-primary/10 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-700">Toko</span>
              </a>
              <a href="deposit/8baa164a7f30" class="flex flex-col items-center gap-2 p-3 rounded-xl border-2 border-gray-100 hover:border-gold hover:bg-gold/5 transition-all text-center">
                <div class="w-9 h-9 bg-gold/10 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-700">Deposit</span>
              </a>
              <a href="pesanan/1088155b6b28" class="flex flex-col items-center gap-2 p-3 rounded-xl border-2 border-gray-100 hover:border-blue-400 hover:bg-blue-50 transition-all text-center">
                <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-700">Pesanan</span>
              </a>
              <a href="garansi/5fe39fc756f0" class="flex flex-col items-center gap-2 p-3 rounded-xl border-2 border-gray-100 hover:border-green-400 hover:bg-green-50 transition-all text-center">
                <div class="w-9 h-9 bg-green-50 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-700">Garansi</span>
              </a>
            </div>
          </div>

          <!-- Balance Card -->
          <div class="balance-card">
            <div class="shimmer"></div>
            <p class="text-white/60 text-xs font-medium mb-1 tracking-wide relative">SALDO KAMU</p>
            <p id="balance-card-amount" class="text-3xl font-bold text-white relative">Rp 0</p>
            <a href="deposit/8baa164a7f30" class="btn-gold relative mt-4 !px-4 !py-2 !rounded-lg text-xs">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
              Deposit Saldo
            </a>
          </div>
        </div>

        <!-- Recent Orders -->
        <div class="lg:col-span-2">
          <div class="card-premium">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
              <h2 class="text-base font-bold text-gray-800">Pesanan Terbaru</h2>
              <a href="pesanan/1088155b6b28" class="text-primary text-sm font-semibold hover:underline">Lihat Semua</a>
            </div>
            <div id="recent-orders" class="p-5">
              <div class="text-center py-8 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <p class="text-sm">Belum ada pesanan</p>
                <a href="toko/e3514627bb16" class="inline-block mt-3 text-primary font-semibold text-sm hover:underline">Belanja Sekarang</a>
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
// Animate a stat number counting up from 0 (purely cosmetic, uses the real final value)
function countUp(el, end, prefix = '') {
  if (!el || !isFinite(end)) return;
  const start = 0;
  const duration = 700;
  const startTime = performance.now();
  function tick(now) {
    const p = Math.min(1, (now - startTime) / duration);
    const eased = 1 - Math.pow(1 - p, 3);
    const val = Math.round(start + (end - start) * eased);
    el.textContent = prefix + val.toLocaleString('id-ID');
    if (p < 1) requestAnimationFrame(tick);
  }
  requestAnimationFrame(tick);
}

// Auth check
auth.onAuthStateChanged(async user => {
  if (!user) { window.location.href = 'login.php'; return; }

  try {
    const snap = await db.collection('users').doc(user.uid).get();
    const data = snap.data() || {};
    const balance = data.balance || 0;

    countUp(document.getElementById('stat-balance'), balance, 'Rp ');
    document.getElementById('header-balance-text').textContent = 'Rp ' + balance.toLocaleString('id-ID');
    document.getElementById('balance-card-amount').textContent = 'Rp ' + balance.toLocaleString('id-ID');

    // Load orders count
    const ordersSnap = await db.collection('orders').where('user_id', '==', user.uid).get();
    countUp(document.getElementById('stat-orders'), ordersSnap.size);

    let pending = 0;
    const rows = [];
    ordersSnap.forEach(doc => {
      const d = doc.data();
      if (d.status === 'pending') pending++;
      rows.push(d);
    });
    countUp(document.getElementById('stat-pending'), pending);

    // Warranty count
    const wSnap = await db.collection('warranties').where('user_id', '==', user.uid).where('status', '==', 'aktif').get();
    countUp(document.getElementById('stat-warranty'), wSnap.size);

    // Recent orders
    rows.sort((a,b) => (b.created_at?.seconds||0) - (a.created_at?.seconds||0));
    const recent = rows.slice(0, 5);
    if (recent.length > 0) {
      const html = recent.map(o => {
        const d = o.created_at ? new Date(o.created_at.seconds*1000).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}) : '-';
        return `<div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0 hover:bg-gray-50/60 -mx-2 px-2 rounded-lg transition-colors">
          <div class="min-w-0">
            <p class="text-sm font-semibold text-gray-800 truncate">${o.product_name||''}</p>
            <p class="text-xs text-gray-400 mt-0.5">${o.invoice||''} · ${d}</p>
          </div>
          <div class="flex items-center gap-3 flex-shrink-0 ml-3">
            <span class="text-sm font-bold text-gray-700">Rp ${(o.price||0).toLocaleString('id-ID')}</span>
            ${statusBadge(o.status)}
          </div>
        </div>`;
      }).join('');
      document.getElementById('recent-orders').innerHTML = html;
    }
  } catch(e) { console.error(e); }
});
</script>
