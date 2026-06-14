<?php
$page_title = "Kelola Stok";
$current_page = "admin-stok";
$base_path = "../";
require_once '../backend/includes/head.php';
?>
<div class="flex h-screen overflow-hidden">
  <?php require_once '../backend/includes/admin-sidebar.php'; ?>
  <div class="flex-1 flex flex-col overflow-hidden">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-3 flex-shrink-0">
      <button class="lg:hidden text-gray-500" onclick="toggleSidebar()"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
      <h1 class="text-xl font-bold text-gray-800">Kelola Stok</h1>
    </header>
    <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
      <!-- Add Stock -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-5">
        <h2 class="font-bold text-gray-800 mb-4">Tambah Stok Item</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Produk</label>
            <select id="stok-product" class="input-field text-sm" onchange="loadVariants()"><option value="">Pilih Produk...</option></select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Varian</label>
            <select id="stok-variant" class="input-field text-sm"><option value="">Pilih Varian...</option></select>
          </div>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Konten Stok (1 item per baris)</label>
          <p class="text-xs text-gray-400 mb-2">Format bebas, bisa email:password, link akun, dsb. Setiap baris = 1 item stok.</p>
          <textarea id="stok-content" rows="8" class="input-field text-sm font-mono resize-none" placeholder="email@example.com:password123&#10;email2@example.com:password456&#10;..."></textarea>
          <p class="text-xs text-gray-400 mt-1">Jumlah item: <span id="stok-count" class="font-bold text-primary">0</span></p>
        </div>
        <button onclick="addStock()" id="btn-add-stock" class="btn-primary mt-4 text-sm py-2.5 px-5">Tambah Stok</button>
      </div>

      <!-- Stock Overview -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h2 class="font-bold text-gray-800 mb-4">Ringkasan Stok</h2>
        <div id="stock-overview"><div class="text-center py-8"><div class="spinner-dark mx-auto mb-2"></div></div></div>
      </div>
    </main>
  </div>
</div>
<?php require_once '../backend/includes/footer.php'; ?>
<script>
let products = [];
auth.onAuthStateChanged(async user => {
  if (!user) { window.location.href = '../login.php'; return; }
  const snap = await db.collection('users').doc(user.uid).get();
  if (!snap.exists || snap.data().role !== 'admin') { window.location.href = '../dashboard.php'; return; }
  loadProductsList();
  loadStockOverview();
});

document.getElementById('stok-content').addEventListener('input', function() {
  const lines = this.value.split('\n').filter(l=>l.trim()).length;
  document.getElementById('stok-count').textContent = lines;
});

async function loadProductsList() {
  const snap = await db.collection('products').where('is_active','==',true).orderBy('name').get();
  products = snap.docs.map(d=>({id:d.id,...d.data()}));
  const sel = document.getElementById('stok-product');
  sel.innerHTML = '<option value="">Pilih Produk...</option>';
  products.forEach(p => { const o=document.createElement('option'); o.value=p.id; o.textContent=p.name; sel.appendChild(o); });
}

async function loadVariants() {
  const productId = document.getElementById('stok-product').value;
  const sel = document.getElementById('stok-variant');
  sel.innerHTML = '<option value="">Pilih Varian...</option>';
  if (!productId) return;
  const snap = await db.collection('products').doc(productId).collection('variants').where('is_active','==',true).get();
  snap.docs.forEach(d => { const o=document.createElement('option'); o.value=d.id; o.textContent=d.data().name; sel.appendChild(o); });
}

async function addStock() {
  const productId = document.getElementById('stok-product').value;
  const variantId = document.getElementById('stok-variant').value;
  const content = document.getElementById('stok-content').value.trim();
  if (!productId||!variantId||!content) { showToast('Lengkapi semua field','error'); return; }
  const btn = document.getElementById('btn-add-stock');
  btn.disabled=true; btn.innerHTML='<span class="spinner"></span> Menambahkan...';
  try {
    const items = content.split('\n').filter(l=>l.trim());
    const batch = db.batch();
    items.forEach(item => {
      const ref = db.collection('stock_items').doc();
      batch.set(ref, { product_id:productId, variant_id:variantId, content:item.trim(), is_used:false, order_id:null, created_at:firebase.firestore.FieldValue.serverTimestamp() });
    });
    // Update variant stock count
    const variantRef = db.collection('products').doc(productId).collection('variants').doc(variantId);
    await batch.commit();
    await variantRef.update({ stock: firebase.firestore.FieldValue.increment(items.length) });
    showToast(items.length+' item stok berhasil ditambahkan!','success');
    document.getElementById('stok-content').value='';
    document.getElementById('stok-count').textContent='0';
    loadStockOverview();
  } catch(e) { showToast('Gagal: '+e.message,'error'); }
  btn.disabled=false; btn.textContent='Tambah Stok';
}

async function loadStockOverview() {
  const prodSnap = await db.collection('products').where('is_active','==',true).orderBy('name').get();
  const rows = [];
  for (const doc of prodSnap.docs) {
    const p = {id:doc.id,...doc.data()};
    const vSnap = await db.collection('products').doc(doc.id).collection('variants').get();
    const varRows = [];
    for (const v of vSnap.docs) {
      const vd = {id:v.id,...v.data()};
      const stSnap = await db.collection('stock_items').where('variant_id','==',v.id).where('is_used','==',false).get();
      vd.available = stSnap.size;
      varRows.push(vd);
    }
    p.variants = varRows;
    rows.push(p);
  }
  if (!rows.length) { document.getElementById('stock-overview').innerHTML='<p class="text-center text-gray-400 py-4">Belum ada produk</p>'; return; }
  document.getElementById('stock-overview').innerHTML = `<div class="space-y-3">${rows.map(p=>`
    <div class="border border-gray-100 rounded-xl overflow-hidden">
      <div class="bg-primary/5 px-4 py-3 flex items-center justify-between">
        <p class="font-bold text-primary">${p.name}</p>
        <span class="text-xs text-gray-400">${p.variants.length} varian</span>
      </div>
      <div class="divide-y divide-gray-50">${p.variants.map(v=>{
        const pct = Math.min(100,Math.round((v.available/Math.max(v.stock||1,1))*100));
        const color = v.available>20?'#22c55e':v.available>5?'#f59e0b':'#ef4444';
        return `<div class="px-4 py-3">
          <div class="flex justify-between items-center mb-1.5">
            <span class="text-sm text-gray-700">${v.name}</span>
            <span class="text-sm font-bold ${v.available>0?'text-green-600':'text-red-500'}">${v.available} ready</span>
          </div>
          <div class="stock-bar"><div class="stock-bar-fill" style="width:${pct}%;background:${color}"></div></div>
        </div>`;}).join('')}
      </div>
    </div>`).join('')}</div>`;
}
</script>
