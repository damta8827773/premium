<?php
$page_title = "Cek Stock";
$current_page = "stok";
$base_path = "";
require_once 'includes/head.php';
?>
<div class="flex h-screen overflow-hidden">
  <?php require_once 'includes/buyer-sidebar.php'; ?>
  <div class="flex-1 flex flex-col overflow-hidden">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-3 flex-shrink-0">
      <button class="lg:hidden text-gray-500" onclick="toggleSidebar()">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>
      <div>
        <h1 class="text-xl font-bold text-gray-800">Cek Stock</h1>
        <p class="text-xs text-gray-400">Lihat ketersediaan stock semua produk</p>
      </div>
    </header>
    <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
      <!-- Filter -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-5">
        <select id="filter-product" onchange="filterStock()" class="input-field max-w-xs text-sm">
          <option value="">Semua Aplikasi</option>
        </select>
      </div>
      <div id="stock-list" class="space-y-4">
        <?php for($i=0;$i<4;$i++): ?>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 animate-pulse">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 bg-gray-200 rounded-lg"></div>
            <div class="flex-1"><div class="h-4 bg-gray-200 rounded w-1/3 mb-1"></div><div class="h-3 bg-gray-200 rounded w-1/4"></div></div>
          </div>
          <div class="space-y-2">
            <div class="h-8 bg-gray-100 rounded-lg"></div>
            <div class="h-8 bg-gray-100 rounded-lg"></div>
          </div>
        </div>
        <?php endfor; ?>
      </div>
    </main>
  </div>
</div>
<?php require_once 'includes/footer.php'; ?>
<script>
let allStockData = [];

auth.onAuthStateChanged(user => {
  if (!user) { window.location.href = 'login.php'; return; }
  loadStock();
});

async function loadStock() {
  try {
    const snap = await db.collection('products').where('is_active','==',true).orderBy('name').get();
    allStockData = [];
    const filterEl = document.getElementById('filter-product');

    for (const doc of snap.docs) {
      const p = { id: doc.id, ...doc.data() };
      const vSnap = await db.collection('products').doc(doc.id).collection('variants').where('is_active','==',true).get();
      p.variants = [];
      for (const v of vSnap.docs) {
        const vd = { id: v.id, ...v.data() };
        const stockSnap = await db.collection('stock_items').where('variant_id','==',v.id).where('is_used','==',false).get();
        vd.available_stock = stockSnap.size;
        p.variants.push(vd);
      }
      allStockData.push(p);
      // Add to filter
      const opt = document.createElement('option');
      opt.value = p.id; opt.textContent = p.name;
      filterEl.appendChild(opt);
    }
    renderStock();
  } catch(e) {
    document.getElementById('stock-list').innerHTML = '<p class="text-center text-gray-400 py-8">Gagal memuat data</p>';
  }
}

function filterStock() {
  renderStock();
}

function renderStock() {
  const filterId = document.getElementById('filter-product').value;
  const data = filterId ? allStockData.filter(p => p.id === filterId) : allStockData;

  if (data.length === 0) {
    document.getElementById('stock-list').innerHTML = '<p class="text-center text-gray-400 py-8">Tidak ada data</p>';
    return;
  }

  const logoMap = {'alight motion':'AlightMotion_logo.png','canva':'Canva_logo.png','capcut':'CapCut_logo.png','netflix':'Netflix_logo.png','spotify':'Spotify_logo.png','youtube':'YouTube_logo.png'};

  document.getElementById('stock-list').innerHTML = data.map(p => {
    const totalReady = p.variants.reduce((s,v) => s+(v.available_stock||0), 0);
    const statusColor = totalReady > 20 ? 'text-green-600 bg-green-100' : totalReady > 5 ? 'text-yellow-600 bg-yellow-100' : 'text-red-600 bg-red-100';

    let imgHtml = `<div class="w-9 h-9 bg-primary/10 rounded-lg flex items-center justify-center"><span class="text-primary font-bold text-sm">${p.name.charAt(0)}</span></div>`;
    const pNameLower = p.name.toLowerCase();
    for (const [key,file] of Object.entries(logoMap)) {
      if (pNameLower.includes(key)) { imgHtml=`<img src="image/${file}" class="w-9 h-9 object-contain rounded-lg bg-gray-50 p-1">`; break; }
    }

    const variantsHtml = p.variants.map(v => {
      const maxStock = Math.max(v.stock||0, v.available_stock||0, 50);
      const pct = Math.min(100, Math.round(((v.available_stock||0)/Math.max(maxStock,1))*100));
      const barColor = pct > 40 ? '#22c55e' : pct > 15 ? '#f59e0b' : '#ef4444';
      return `<div class="bg-gray-50 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-semibold text-gray-700">${v.name}</span>
          <span class="text-sm font-bold ${v.available_stock > 0 ? 'text-green-600' : 'text-red-500'}">${v.available_stock||0} READY</span>
        </div>
        <div class="stock-bar"><div class="stock-bar-fill" style="width:${pct}%;background:${barColor}"></div></div>
        <p class="text-xs text-gray-400 text-right mt-1">${pct}%</p>
      </div>`;
    }).join('');

    return `<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 bg-primary text-white">
        <div class="flex items-center gap-3">${imgHtml.replace('bg-primary/10','bg-white/20')}<div><p class="font-bold">${p.name.toUpperCase()}</p><p class="text-white/60 text-xs">${p.variants.length} variasi</p></div></div>
        <span class="text-sm font-bold bg-green-500/20 text-green-300 px-3 py-1 rounded-lg">${totalReady} ready</span>
      </div>
      <div class="p-4 space-y-3">${variantsHtml || '<p class="text-gray-400 text-sm text-center py-2">Tidak ada varian</p>'}</div>
    </div>`;
  }).join('');
}
</script>
