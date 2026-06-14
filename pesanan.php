<?php
$page_title = "Riwayat Pesanan";
$current_page = "pesanan";
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
      <h1 class="text-xl font-bold text-gray-800">Riwayat Pesanan</h1>
    </header>
    <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
      <!-- Search & Filter -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-5 flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke-width="2"/><path d="m21 21-4.35-4.35" stroke-linecap="round" stroke-width="2"/></svg>
          <input type="text" id="search" placeholder="Cari invoice atau nama produk..." class="input-field pl-10 text-sm" oninput="renderOrders()">
        </div>
        <div class="flex gap-2 tab-scroll">
          <button class="tab-btn active" onclick="setFilter('semua',this)">Semua</button>
          <button class="tab-btn" onclick="setFilter('selesai',this)">Selesai</button>
          <button class="tab-btn" onclick="setFilter('pending',this)">Pending</button>
          <button class="tab-btn" onclick="setFilter('expired',this)">Expired</button>
          <button class="tab-btn" onclick="setFilter('batal',this)">Batal</button>
        </div>
      </div>

      <div id="orders-list" class="space-y-3">
        <div class="text-center py-12 text-gray-400">
          <div class="spinner-dark mx-auto mb-3"></div>
          <p class="text-sm">Memuat pesanan...</p>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- Order Detail Modal -->
<div id="modal-order" class="modal-overlay hidden" onclick="if(event.target===this)this.classList.add('hidden')">
  <div class="modal-box">
    <div class="flex justify-between items-center mb-4">
      <h3 class="font-bold text-gray-800 text-lg">Detail Pesanan</h3>
      <button onclick="document.getElementById('modal-order').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div id="order-detail-content"></div>
  </div>
</div>

<?php require_once 'backend/includes/footer.php'; ?>
<script>
let allOrders = [];
let currentFilter = 'semua';

auth.onAuthStateChanged(async user => {
  if (!user) { window.location.href = 'login.php'; return; }
  try {
    const snap = await db.collection('orders').where('user_id','==',user.uid).orderBy('created_at','desc').get();
    allOrders = snap.docs.map(d => ({ id: d.id, ...d.data() }));
    renderOrders();
  } catch(e) {
    document.getElementById('orders-list').innerHTML = '<p class="text-center text-gray-400 py-8">Gagal memuat pesanan</p>';
  }
});

function setFilter(f, btn) {
  currentFilter = f;
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  renderOrders();
}

function renderOrders() {
  const q = document.getElementById('search').value.toLowerCase();
  let filtered = allOrders.filter(o => {
    const matchF = currentFilter === 'semua' || o.status === currentFilter;
    const matchQ = !q || (o.invoice||'').toLowerCase().includes(q) || (o.product_name||'').toLowerCase().includes(q);
    return matchF && matchQ;
  });

  if (filtered.length === 0) {
    document.getElementById('orders-list').innerHTML = `
      <div class="text-center py-14 bg-white rounded-2xl border border-gray-100">
        <svg class="w-14 h-14 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        <p class="text-gray-500 font-medium">Belum ada pesanan</p>
        <p class="text-gray-400 text-sm mt-1">Pesanan yang sudah kamu buat akan muncul di sini.</p>
        <a href="toko.php" class="inline-block mt-4 bg-primary text-white text-sm font-semibold px-5 py-2.5 rounded-xl hover:bg-primary-light transition-colors">Belanja Sekarang</a>
      </div>`;
    return;
  }

  const statusMap = {
    selesai: ['bg-green-100 text-green-700','Selesai'], pending: ['bg-yellow-100 text-yellow-700','Pending'],
    expired: ['bg-gray-100 text-gray-500','Expired'], batal: ['bg-red-100 text-red-600','Batal'],
  };

  document.getElementById('orders-list').innerHTML = filtered.map(o => {
    const [sc,sl] = statusMap[o.status] || ['bg-gray-100 text-gray-500',o.status];
    const d = o.created_at ? new Date(o.created_at.seconds*1000).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'}) : '-';
    return `<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 cursor-pointer hover:shadow-md transition-shadow" onclick="showDetail('${o.id}')">
      <div class="flex items-start justify-between mb-3">
        <div>
          <p class="text-xs text-gray-400 font-medium">${o.invoice||'-'}</p>
          <p class="font-bold text-gray-800 mt-0.5">${o.product_name||''}</p>
          <p class="text-sm text-gray-500">${o.variant_name||''}</p>
        </div>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-lg ${sc} flex-shrink-0">${sl}</span>
      </div>
      <div class="flex items-center justify-between pt-3 border-t border-gray-50">
        <span class="text-xs text-gray-400">${d}</span>
        <span class="font-bold text-gray-800">Rp ${(o.price||0).toLocaleString('id-ID')}</span>
      </div>
    </div>`;
  }).join('');
}

function showDetail(id) {
  const o = allOrders.find(x => x.id === id);
  if (!o) return;
  const statusMap = {selesai:['bg-green-100 text-green-700','Selesai'],pending:['bg-yellow-100 text-yellow-700','Pending'],expired:['bg-gray-100 text-gray-500','Expired'],batal:['bg-red-100 text-red-600','Batal']};
  const [sc,sl] = statusMap[o.status] || ['bg-gray-100 text-gray-500',o.status];
  const d = o.created_at ? new Date(o.created_at.seconds*1000).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'}) : '-';
  document.getElementById('order-detail-content').innerHTML = `
    <div class="space-y-3">
      <div class="bg-gray-50 rounded-xl p-4 space-y-2.5">
        <div class="flex justify-between text-sm"><span class="text-gray-500">Invoice</span><span class="font-semibold text-gray-800">${o.invoice||'-'}</span></div>
        <div class="flex justify-between text-sm"><span class="text-gray-500">Produk</span><span class="font-semibold text-gray-800">${o.product_name||'-'}</span></div>
        <div class="flex justify-between text-sm"><span class="text-gray-500">Varian</span><span class="font-semibold text-gray-800">${o.variant_name||'-'}</span></div>
        <div class="flex justify-between text-sm"><span class="text-gray-500">Harga</span><span class="font-bold text-primary">Rp ${(o.price||0).toLocaleString('id-ID')}</span></div>
        <div class="flex justify-between text-sm"><span class="text-gray-500">Tanggal</span><span class="font-semibold text-gray-800">${d}</span></div>
        <div class="flex justify-between text-sm"><span class="text-gray-500">Status</span><span class="font-semibold px-2 py-0.5 rounded-lg text-xs ${sc}">${sl}</span></div>
      </div>
      ${o.status==='selesai' && o.stock_content ? `
      <div class="bg-primary/5 border border-primary/20 rounded-xl p-4">
        <div class="flex items-center gap-2 mb-2">
          <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
          <p class="text-sm font-bold text-primary">Akun / Informasi</p>
        </div>
        <pre class="text-sm text-gray-700 whitespace-pre-wrap bg-white rounded-lg p-3 border border-primary/10">${o.stock_content}</pre>
        <button onclick="navigator.clipboard.writeText('${o.stock_content.replace(/'/g,"\\'")}').then(()=>showToast('Disalin!','success'))" class="mt-2 text-xs text-primary font-semibold hover:underline flex items-center gap-1">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
          Salin
        </button>
      </div>` : ''}
    </div>`;
  document.getElementById('modal-order').classList.remove('hidden');
}
</script>
