<?php
$page_title = "Admin Dashboard";
$current_page = "admin-dashboard";
$base_path = "../";
require_once '../backend/includes/head.php';
?>
<div class="flex h-screen overflow-hidden">
  <?php require_once '../backend/includes/admin-sidebar.php'; ?>
  <div class="flex-1 flex flex-col overflow-hidden">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between flex-shrink-0">
      <div class="flex items-center gap-3">
        <button class="lg:hidden text-gray-500" onclick="toggleSidebar()"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
        <h1 class="text-xl font-bold text-gray-800">Dashboard Admin</h1>
      </div>
      <span class="bg-yellow-400 text-black text-xs font-bold px-3 py-1.5 rounded-lg">ADMIN MODE</span>
    </header>
    <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
      <!-- Stats -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
          <p class="text-gray-400 text-xs font-medium mb-1">Total Pengguna</p>
          <p id="stat-users" class="text-2xl font-bold text-gray-800">-</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
          <p class="text-gray-400 text-xs font-medium mb-1">Pesanan Hari Ini</p>
          <p id="stat-orders-today" class="text-2xl font-bold text-gray-800">-</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
          <p class="text-gray-400 text-xs font-medium mb-1">Deposit Pending</p>
          <p id="stat-pending-dep" class="text-2xl font-bold text-red-500">-</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
          <p class="text-gray-400 text-xs font-medium mb-1">Pendapatan Hari Ini</p>
          <p id="stat-revenue" class="text-2xl font-bold text-green-600">-</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pending Deposits -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
          <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h2 class="font-bold text-gray-800">Deposit Pending (Manual)</h2>
            <a href="pembayaran.php" class="text-primary text-sm font-semibold hover:underline">Lihat Semua</a>
          </div>
          <div id="pending-deposits" class="p-5 space-y-3">
            <div class="text-center py-6"><div class="spinner-dark mx-auto mb-2"></div><p class="text-sm text-gray-400">Memuat...</p></div>
          </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
          <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h2 class="font-bold text-gray-800">Pesanan Terbaru</h2>
            <a href="pesanan.php" class="text-primary text-sm font-semibold hover:underline">Lihat Semua</a>
          </div>
          <div id="recent-orders" class="p-5">
            <div class="text-center py-6"><div class="spinner-dark mx-auto mb-2"></div></div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6">
        <a href="produk.php" class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm text-center hover:shadow-md transition-shadow">
          <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center mx-auto mb-2"><svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></div>
          <p class="text-sm font-semibold text-gray-700">Kelola Produk</p>
        </a>
        <a href="stok.php" class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm text-center hover:shadow-md transition-shadow">
          <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mx-auto mb-2"><svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg></div>
          <p class="text-sm font-semibold text-gray-700">Tambah Stok</p>
        </a>
        <a href="pembayaran.php" class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm text-center hover:shadow-md transition-shadow">
          <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center mx-auto mb-2"><svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></div>
          <p class="text-sm font-semibold text-gray-700">Approve Deposit</p>
        </a>
        <a href="pengumuman.php" class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm text-center hover:shadow-md transition-shadow">
          <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-2"><svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg></div>
          <p class="text-sm font-semibold text-gray-700">Pengumuman</p>
        </a>
      </div>
    </main>
  </div>
</div>
<?php require_once '../backend/includes/footer.php'; ?>
<script>
auth.onAuthStateChanged(async user => {
  if (!user) { window.location.href = '../login.php'; return; }
  const snap = await db.collection('users').doc(user.uid).get();
  if (!snap.exists || snap.data().role !== 'admin') { window.location.href = '../dashboard.php'; return; }
  document.getElementById('admin-name').textContent = snap.data().name || 'Admin';
  loadStats();
});

async function loadStats() {
  try {
    const usersSnap = await db.collection('users').get();
    document.getElementById('stat-users').textContent = usersSnap.size;

    const today = new Date(); today.setHours(0,0,0,0);
    const ordersSnap = await db.collection('orders').where('created_at','>=',firebase.firestore.Timestamp.fromDate(today)).get();
    document.getElementById('stat-orders-today').textContent = ordersSnap.size;
    let rev = 0; ordersSnap.forEach(d => { if(d.data().status==='selesai') rev += d.data().price||0; });
    document.getElementById('stat-revenue').textContent = 'Rp '+rev.toLocaleString('id-ID');

    const pendingSnap = await db.collection('deposits').where('status','==','pending').where('method','==','manual').get();
    document.getElementById('stat-pending-dep').textContent = pendingSnap.size;

    // Pending deposits list
    const pending = pendingSnap.docs.map(d=>({id:d.id,...d.data()})).slice(0,5);
    if(pending.length===0) { document.getElementById('pending-deposits').innerHTML='<p class="text-center text-gray-400 text-sm py-4">Tidak ada deposit pending</p>'; }
    else {
      document.getElementById('pending-deposits').innerHTML = pending.map(dep => {
        const d = dep.created_at ? new Date(dep.created_at.seconds*1000).toLocaleDateString('id-ID',{day:'2-digit',month:'short',hour:'2-digit',minute:'2-digit'}) : '-';
        return `<div class="flex items-center justify-between p-3 bg-yellow-50 rounded-xl border border-yellow-100">
          <div><p class="text-sm font-bold text-gray-800">Rp ${(dep.amount||0).toLocaleString('id-ID')}</p><p class="text-xs text-gray-400">${dep.user_id.slice(0,8)} · ${d}</p></div>
          <a href="pembayaran.php" class="text-xs bg-primary text-white font-semibold px-3 py-1.5 rounded-lg hover:bg-primary-light">Review</a>
        </div>`;
      }).join('');
    }

    // Recent orders
    const recentSnap = await db.collection('orders').orderBy('created_at','desc').limit(8).get();
    const orders = recentSnap.docs.map(d=>({id:d.id,...d.data()}));
    document.getElementById('recent-orders').innerHTML = orders.length===0 ? '<p class="text-center text-gray-400 text-sm py-4">Belum ada pesanan</p>' :
      orders.map(o => {
        const st = {selesai:'bg-green-100 text-green-700',pending:'bg-yellow-100 text-yellow-700',expired:'bg-gray-100 text-gray-500',batal:'bg-red-100 text-red-600'};
        const [sc] = (st[o.status]||'bg-gray-100 text-gray-500').split(' ');
        const d = o.created_at ? new Date(o.created_at.seconds*1000).toLocaleDateString('id-ID',{day:'2-digit',month:'short'}) : '-';
        return `<div class="flex items-center justify-between py-2.5 border-b border-gray-50 last:border-0">
          <div class="min-w-0"><p class="text-sm font-semibold text-gray-800 truncate">${o.product_name||''}</p><p class="text-xs text-gray-400">${d}</p></div>
          <div class="flex items-center gap-2 flex-shrink-0 ml-2">
            <span class="text-xs font-bold text-gray-700">Rp ${(o.price||0).toLocaleString('id-ID')}</span>
            <span class="text-xs font-semibold px-2 py-0.5 rounded-lg ${st[o.status]||'bg-gray-100 text-gray-500'}">${o.status||''}</span>
          </div>
        </div>`;
      }).join('');
  } catch(e) { console.error(e); }
}
</script>
