<?php
$page_title = "Kelola Pesanan";
$current_page = "admin-pesanan";
$base_path = "../";
require_once '../includes/head.php';
?>
<div class="flex h-screen overflow-hidden">
  <?php require_once '../includes/admin-sidebar.php'; ?>
  <div class="flex-1 flex flex-col overflow-hidden">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-3 flex-shrink-0">
      <button class="lg:hidden text-gray-500" onclick="toggleSidebar()"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
      <h1 class="text-xl font-bold text-gray-800">Kelola Pesanan</h1>
    </header>
    <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
      <div class="flex flex-col sm:flex-row gap-3 mb-5">
        <div class="relative flex-1">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke-width="2"/><path d="m21 21-4.35-4.35" stroke-linecap="round" stroke-width="2"/></svg>
          <input type="text" id="search" placeholder="Cari invoice, produk..." class="input-field pl-10 text-sm" oninput="renderOrders()">
        </div>
        <div class="flex gap-2 tab-scroll">
          <button class="tab-btn active" onclick="setFilter('semua',this)">Semua</button>
          <button class="tab-btn" onclick="setFilter('selesai',this)">Selesai</button>
          <button class="tab-btn" onclick="setFilter('pending',this)">Pending</button>
          <button class="tab-btn" onclick="setFilter('batal',this)">Batal</button>
        </div>
      </div>
      <div id="orders-table"><div class="text-center py-10"><div class="spinner-dark mx-auto mb-2"></div><p class="text-sm text-gray-400">Memuat...</p></div></div>
    </main>
  </div>
</div>
<?php require_once '../includes/footer.php'; ?>
<script>
let allOrders=[], filter='semua';
auth.onAuthStateChanged(async user => {
  if (!user) { window.location.href='../login.php'; return; }
  const snap = await db.collection('users').doc(user.uid).get();
  if (!snap.exists||snap.data().role!=='admin') { window.location.href='../dashboard.php'; return; }
  const ordSnap = await db.collection('orders').orderBy('created_at','desc').limit(500).get();
  allOrders = ordSnap.docs.map(d=>({id:d.id,...d.data()}));
  renderOrders();
});
function setFilter(f,btn){filter=f;document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));btn.classList.add('active');renderOrders();}
function renderOrders(){
  const q=document.getElementById('search').value.toLowerCase();
  const filtered=allOrders.filter(o=>{
    const mF=filter==='semua'||o.status===filter;
    const mQ=!q||(o.invoice||'').toLowerCase().includes(q)||(o.product_name||'').toLowerCase().includes(q)||(o.user_id||'').toLowerCase().includes(q);
    return mF&&mQ;
  });
  if(!filtered.length){document.getElementById('orders-table').innerHTML='<p class="text-center text-gray-400 py-8">Tidak ada data</p>';return;}
  const stMap={selesai:'bg-green-100 text-green-700',pending:'bg-yellow-100 text-yellow-700',expired:'bg-gray-100 text-gray-500',batal:'bg-red-100 text-red-600'};
  document.getElementById('orders-table').innerHTML=`<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead><tr class="bg-gray-50 border-b border-gray-100 text-left">
        <th class="px-4 py-3 font-semibold text-gray-600 text-xs">Invoice</th>
        <th class="px-4 py-3 font-semibold text-gray-600 text-xs">Produk</th>
        <th class="px-4 py-3 font-semibold text-gray-600 text-xs">User</th>
        <th class="px-4 py-3 font-semibold text-gray-600 text-xs">Harga</th>
        <th class="px-4 py-3 font-semibold text-gray-600 text-xs">Tanggal</th>
        <th class="px-4 py-3 font-semibold text-gray-600 text-xs">Status</th>
      </tr></thead>
      <tbody class="divide-y divide-gray-50">
      ${filtered.map(o=>{
        const d=o.created_at?new Date(o.created_at.seconds*1000).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'2-digit'}):'-';
        return`<tr class="hover:bg-gray-50">
          <td class="px-4 py-3 font-mono text-xs text-gray-500">${o.invoice||'-'}</td>
          <td class="px-4 py-3"><p class="font-semibold text-gray-800">${o.product_name||''}</p><p class="text-xs text-gray-400">${o.variant_name||''}</p></td>
          <td class="px-4 py-3 text-xs text-gray-500 font-mono">${(o.user_id||'').slice(0,8)}...</td>
          <td class="px-4 py-3 font-bold text-gray-800">Rp ${(o.price||0).toLocaleString('id-ID')}</td>
          <td class="px-4 py-3 text-xs text-gray-500">${d}</td>
          <td class="px-4 py-3"><span class="text-xs font-semibold px-2 py-0.5 rounded-lg ${stMap[o.status]||'bg-gray-100 text-gray-500'}">${o.status||''}</span></td>
        </tr>`;
      }).join('')}
      </tbody>
    </table></div></div>`;
}
</script>
