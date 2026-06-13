<?php
$page_title = "Kelola Produk";
$current_page = "admin-produk";
$base_path = "../";
require_once '../includes/head.php';
?>
<div class="flex h-screen overflow-hidden">
  <?php require_once '../includes/admin-sidebar.php'; ?>
  <div class="flex-1 flex flex-col overflow-hidden">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between flex-shrink-0">
      <div class="flex items-center gap-3">
        <button class="lg:hidden text-gray-500" onclick="toggleSidebar()"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
        <h1 class="text-xl font-bold text-gray-800">Kelola Produk</h1>
      </div>
      <button onclick="openAddProduct()" class="btn-primary text-sm py-2.5 px-4">+ Tambah Produk</button>
    </header>
    <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
      <div id="products-list"><div class="text-center py-10"><div class="spinner-dark mx-auto mb-2"></div><p class="text-sm text-gray-400">Memuat...</p></div></div>
    </main>
  </div>
</div>

<!-- Add/Edit Product Modal -->
<div id="modal-product" class="modal-overlay hidden" onclick="if(event.target===this)this.classList.add('hidden')">
  <div class="modal-box max-w-lg">
    <div class="flex justify-between items-center mb-5">
      <h3 id="modal-product-title" class="font-bold text-gray-800 text-lg">Tambah Produk</h3>
      <button onclick="document.getElementById('modal-product').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <input type="hidden" id="product-id">
    <div class="space-y-4">
      <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Produk *</label><input type="text" id="prod-name" class="input-field text-sm" placeholder="cth: Canva Pro"></div>
      <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Kategori *</label>
        <select id="prod-category" class="input-field text-sm">
          <option value="ai">AI</option><option value="editing">Editing</option><option value="edu_needs">Edu Needs</option>
          <option value="music">Music</option><option value="sosmed_needs">Sosmed Needs</option><option value="streaming">Streaming</option>
        </select>
      </div>
      <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Badge (opsional)</label><input type="text" id="prod-badge" class="input-field text-sm" placeholder="cth: BEST SELLER"></div>
      <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi</label><textarea id="prod-desc" rows="3" class="input-field text-sm resize-none" placeholder="Deskripsi produk..."></textarea></div>
      <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">URL Gambar (opsional)</label><input type="url" id="prod-image" class="input-field text-sm" placeholder="https://..."></div>

      <!-- Variants -->
      <div>
        <div class="flex items-center justify-between mb-2">
          <label class="text-sm font-semibold text-gray-700">Varian Produk</label>
          <button onclick="addVariantRow()" type="button" class="text-xs text-primary font-semibold hover:underline">+ Tambah Varian</button>
        </div>
        <div id="variants-container" class="space-y-2"></div>
      </div>

      <div class="flex items-center gap-2 pt-2">
        <input type="checkbox" id="prod-active" checked class="w-4 h-4 accent-primary">
        <label for="prod-active" class="text-sm font-medium text-gray-700">Produk Aktif</label>
      </div>
      <button onclick="saveProduct()" id="btn-save-product" class="btn-primary w-full">Simpan Produk</button>
    </div>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>
<script>
let allProducts = [];
let editingProductId = null;

auth.onAuthStateChanged(async user => {
  if (!user) { window.location.href = '../login.php'; return; }
  const snap = await db.collection('users').doc(user.uid).get();
  if (!snap.exists || snap.data().role !== 'admin') { window.location.href = '../dashboard.php'; return; }
  loadProducts();
});

async function loadProducts() {
  const snap = await db.collection('products').orderBy('name').get();
  allProducts = [];
  for (const doc of snap.docs) {
    const p = {id:doc.id,...doc.data()};
    const vSnap = await db.collection('products').doc(doc.id).collection('variants').get();
    p.variants = vSnap.docs.map(v=>({id:v.id,...v.data()}));
    allProducts.push(p);
  }
  renderProducts();
}

function renderProducts() {
  if (!allProducts.length) { document.getElementById('products-list').innerHTML='<p class="text-center text-gray-400 py-10">Belum ada produk</p>'; return; }
  document.getElementById('products-list').innerHTML = `<div class="space-y-3">${allProducts.map(p => `
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
      <div class="flex items-start justify-between">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0 overflow-hidden">
            ${p.image?`<img src="${p.image}" class="w-full h-full object-contain p-1" onerror="this.outerHTML='<span class=font-bold text-gray-400>${p.name.charAt(0)}</span>'">`:`<span class="font-bold text-gray-400 text-lg">${p.name.charAt(0)}</span>`}
          </div>
          <div>
            <div class="flex items-center gap-2 mb-0.5">
              <p class="font-bold text-gray-800">${p.name}</p>
              ${p.badge?`<span class="text-xs bg-gold text-black font-bold px-1.5 py-0.5 rounded">${p.badge}</span>`:''}
              ${!p.is_active?`<span class="text-xs bg-red-100 text-red-600 font-semibold px-1.5 py-0.5 rounded">Nonaktif</span>`:''}
            </div>
            <p class="text-xs text-gray-400">${p.category||''} · ${p.variants.length} varian</p>
          </div>
        </div>
        <div class="flex gap-2">
          <button onclick="editProduct('${p.id}')" class="text-xs bg-primary text-white font-semibold px-3 py-1.5 rounded-lg hover:bg-primary-light">Edit</button>
          <button onclick="deleteProduct('${p.id}')" class="text-xs bg-red-500 text-white font-semibold px-3 py-1.5 rounded-lg hover:bg-red-600">Hapus</button>
        </div>
      </div>
      ${p.variants.length?`<div class="mt-3 grid grid-cols-2 gap-2">${p.variants.map(v=>`<div class="bg-gray-50 rounded-lg px-3 py-2 text-xs"><p class="font-semibold text-gray-700">${v.name}</p><p class="text-gray-400">Rp ${(v.price||0).toLocaleString('id-ID')} · Stok: ${v.stock||0}</p></div>`).join('')}</div>`:''}
    </div>`).join('')}</div>`;
}

function openAddProduct() {
  editingProductId = null;
  document.getElementById('modal-product-title').textContent = 'Tambah Produk';
  document.getElementById('product-id').value='';
  document.getElementById('prod-name').value='';
  document.getElementById('prod-category').value='ai';
  document.getElementById('prod-badge').value='';
  document.getElementById('prod-desc').value='';
  document.getElementById('prod-image').value='';
  document.getElementById('prod-active').checked=true;
  document.getElementById('variants-container').innerHTML='';
  addVariantRow();
  document.getElementById('modal-product').classList.remove('hidden');
}

function editProduct(id) {
  const p = allProducts.find(x=>x.id===id);
  if (!p) return;
  editingProductId = id;
  document.getElementById('modal-product-title').textContent = 'Edit Produk';
  document.getElementById('product-id').value=id;
  document.getElementById('prod-name').value=p.name||'';
  document.getElementById('prod-category').value=p.category||'ai';
  document.getElementById('prod-badge').value=p.badge||'';
  document.getElementById('prod-desc').value=p.description||'';
  document.getElementById('prod-image').value=p.image||'';
  document.getElementById('prod-active').checked=p.is_active!==false;
  const vc = document.getElementById('variants-container'); vc.innerHTML='';
  (p.variants||[]).forEach(v => addVariantRow(v));
  if (!p.variants?.length) addVariantRow();
  document.getElementById('modal-product').classList.remove('hidden');
}

let variantIndex = 0;
function addVariantRow(v={}) {
  const idx = variantIndex++;
  const div = document.createElement('div');
  div.className='grid grid-cols-4 gap-2 items-center';
  div.innerHTML=`
    <input type="text" placeholder="Nama varian" value="${v.name||''}" class="input-field text-xs col-span-1" data-field="name" data-idx="${idx}">
    <input type="number" placeholder="Harga" value="${v.price||''}" class="input-field text-xs" data-field="price" data-idx="${idx}">
    <input type="number" placeholder="Harga Reseller" value="${v.reseller_price||''}" class="input-field text-xs" data-field="reseller_price" data-idx="${idx}">
    <div class="flex gap-1">
      <input type="number" placeholder="Stok" value="${v.stock||0}" class="input-field text-xs flex-1" data-field="stock" data-idx="${idx}">
      <button type="button" onclick="this.closest('.grid').remove()" class="text-red-400 hover:text-red-600 px-1">×</button>
    </div>`;
  if (v.id) div.dataset.variantId = v.id;
  document.getElementById('variants-container').appendChild(div);
}

async function saveProduct() {
  const name = document.getElementById('prod-name').value.trim();
  if (!name) { showToast('Nama produk wajib diisi','error'); return; }
  const btn = document.getElementById('btn-save-product');
  btn.disabled=true; btn.innerHTML='<span class="spinner"></span> Menyimpan...';
  try {
    const productData = {
      name, category: document.getElementById('prod-category').value,
      badge: document.getElementById('prod-badge').value.trim().toUpperCase() || null,
      description: document.getElementById('prod-desc').value.trim(),
      image: document.getElementById('prod-image').value.trim() || null,
      is_active: document.getElementById('prod-active').checked,
      slug: name.toLowerCase().replace(/\s+/g,'-'),
      updated_at: firebase.firestore.FieldValue.serverTimestamp()
    };

    // Collect variants
    const variantRows = document.getElementById('variants-container').querySelectorAll('.grid');
    const variants = [];
    variantRows.forEach(row => {
      const name2 = row.querySelector('[data-field="name"]').value.trim();
      const price = parseInt(row.querySelector('[data-field="price"]').value)||0;
      const reseller_price = parseInt(row.querySelector('[data-field="reseller_price"]').value)||price;
      const stock = parseInt(row.querySelector('[data-field="stock"]').value)||0;
      if (name2) variants.push({ id: row.dataset.variantId||null, name:name2, price, reseller_price, stock, is_active:true });
    });

    let productId = editingProductId;
    if (!productId) {
      productData.created_at = firebase.firestore.FieldValue.serverTimestamp();
      const ref = await db.collection('products').add(productData);
      productId = ref.id;
    } else {
      await db.collection('products').doc(productId).update(productData);
    }

    // Save variants
    for (const v of variants) {
      if (v.id) {
        await db.collection('products').doc(productId).collection('variants').doc(v.id).update({ name:v.name, price:v.price, reseller_price:v.reseller_price, stock:v.stock, is_active:true });
      } else {
        await db.collection('products').doc(productId).collection('variants').add({ name:v.name, price:v.price, reseller_price:v.reseller_price, stock:v.stock, is_active:true });
      }
    }

    showToast('Produk berhasil disimpan!','success');
    document.getElementById('modal-product').classList.add('hidden');
    loadProducts();
  } catch(e) { showToast('Gagal: '+e.message,'error'); }
  btn.disabled=false; btn.textContent='Simpan Produk';
}

async function deleteProduct(id) {
  if (!confirm('Hapus produk ini? Semua varian juga akan dihapus.')) return;
  try {
    const vSnap = await db.collection('products').doc(id).collection('variants').get();
    const batch = db.batch();
    vSnap.docs.forEach(d => batch.delete(d.ref));
    batch.delete(db.collection('products').doc(id));
    await batch.commit();
    showToast('Produk dihapus','success');
    loadProducts();
  } catch(e) { showToast('Gagal: '+e.message,'error'); }
}
</script>
