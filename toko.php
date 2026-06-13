<?php
$page_title = "Toko";
$current_page = "toko";
$base_path = "";
require_once 'includes/head.php';
?>

<div class="flex h-screen overflow-hidden">
  <?php require_once 'includes/buyer-sidebar.php'; ?>

  <div class="flex-1 flex flex-col overflow-hidden">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between flex-shrink-0">
      <div class="flex items-center gap-3">
        <button class="lg:hidden text-gray-500" onclick="toggleSidebar()">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <div>
          <h1 class="text-xl font-bold text-gray-800">Toko</h1>
          <p class="text-xs text-gray-400">Jelajahi aplikasi premium</p>
        </div>
      </div>
      <a href="deposit.php" class="bg-primary text-white text-sm font-bold px-4 py-2 rounded-xl flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="M2 10h20"/></svg>
        <span id="topbar-balance">Rp 0</span>
      </a>
    </header>

    <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
      <!-- Search -->
      <div class="relative mb-5">
        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35"/></svg>
        <input type="text" id="search" placeholder="Cari produk ..." class="w-full border border-gray-200 rounded-xl pl-11 pr-4 py-3 bg-white text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20" oninput="filterProducts()">
      </div>

      <!-- Category Tabs -->
      <div class="tab-scroll flex gap-2 mb-5 pb-1">
        <button class="tab-btn active" onclick="setCategory('semua',this)">Semua</button>
        <button class="tab-btn" onclick="setCategory('ai',this)">Ai</button>
        <button class="tab-btn" onclick="setCategory('editing',this)">Editing</button>
        <button class="tab-btn" onclick="setCategory('edu_needs',this)">Edu Needs</button>
        <button class="tab-btn" onclick="setCategory('music',this)">Music</button>
        <button class="tab-btn" onclick="setCategory('sosmed_needs',this)">Sosmed Needs</button>
        <button class="tab-btn" onclick="setCategory('streaming',this)">Streaming</button>
      </div>

      <!-- Products Grid -->
      <div id="products-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        <!-- Loading skeleton -->
        <?php for($i=0;$i<10;$i++): ?>
        <div class="bg-white rounded-2xl p-3 animate-pulse border border-gray-100">
          <div class="bg-gray-200 rounded-xl h-28 mb-3"></div>
          <div class="bg-gray-200 h-4 rounded mb-2"></div>
          <div class="bg-gray-200 h-3 rounded w-2/3"></div>
        </div>
        <?php endfor; ?>
      </div>

      <div id="no-products" class="hidden text-center py-16 text-gray-400">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        <p class="font-medium text-gray-500">Produk tidak ditemukan</p>
      </div>
    </main>
  </div>
</div>

<!-- Product Detail Modal -->
<div id="modal-product" class="modal-overlay hidden" onclick="if(event.target===this)closeModal()">
  <div class="modal-box max-w-lg">
    <div class="flex items-start justify-between mb-4">
      <div class="flex items-center gap-3">
        <div id="modal-img-wrap" class="w-14 h-14 bg-gray-100 rounded-xl overflow-hidden flex items-center justify-center flex-shrink-0"></div>
        <div>
          <div id="modal-badge" class="text-xs font-bold text-black bg-gold px-2 py-0.5 rounded mb-1 inline-block"></div>
          <h2 id="modal-name" class="text-lg font-bold text-gray-800"></h2>
        </div>
      </div>
      <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 p-1">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <div id="modal-desc" class="text-sm text-gray-500 mb-4"></div>

    <p class="text-sm font-semibold text-gray-700 mb-3">Pilih Varian:</p>
    <div id="modal-variants" class="space-y-2 mb-5"></div>

    <div id="modal-selected" class="bg-gray-50 rounded-xl p-4 mb-4 hidden">
      <div class="flex justify-between items-center">
        <span class="text-sm text-gray-600">Saldo Kamu:</span>
        <span id="modal-balance-info" class="text-sm font-bold text-primary">Rp 0</span>
      </div>
      <div class="flex justify-between items-center mt-1">
        <span class="text-sm text-gray-600">Harga:</span>
        <span id="modal-price-info" class="text-sm font-bold text-gray-800">Rp 0</span>
      </div>
      <div class="h-px bg-gray-200 my-2"></div>
      <div class="flex justify-between items-center">
        <span class="text-sm font-semibold text-gray-700">Sisa Saldo:</span>
        <span id="modal-after-balance" class="text-sm font-bold text-gray-800">Rp 0</span>
      </div>
    </div>

    <button id="btn-beli" onclick="processBuy()" class="btn-gold w-full hidden">Beli Sekarang</button>
    <div id="no-stock-msg" class="hidden text-center text-sm text-red-500 font-medium py-2">Stok habis</div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
<script>
let allProducts = [];
let selectedVariant = null;
let selectedProduct = null;
let currentCategory = 'semua';
let currentUser = null;
let userBalance = 0;
let isReseller = false;

auth.onAuthStateChanged(async user => {
  if (!user) { window.location.href = 'login.php'; return; }
  currentUser = user;

  const snap = await db.collection('users').doc(user.uid).get();
  const data = snap.data() || {};
  userBalance = data.balance || 0;
  isReseller = data.is_reseller || false;
  document.getElementById('topbar-balance').textContent = 'Rp ' + userBalance.toLocaleString('id-ID');

  loadProducts();
});

async function loadProducts() {
  try {
    const snap = await db.collection('products').where('is_active','==',true).orderBy('name').get();
    allProducts = [];
    for (const doc of snap.docs) {
      const p = { id: doc.id, ...doc.data() };
      // Get variants
      const vSnap = await db.collection('products').doc(doc.id).collection('variants').where('is_active','==',true).get();
      p.variants = vSnap.docs.map(v => ({ id: v.id, ...v.data() }));
      allProducts.push(p);
    }
    renderProducts();
  } catch(e) {
    console.error(e);
    document.getElementById('products-grid').innerHTML = '<p class="col-span-full text-center text-gray-400 py-8">Gagal memuat produk</p>';
  }
}

function filterProducts() {
  renderProducts();
}
function setCategory(cat, btn) {
  currentCategory = cat;
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  renderProducts();
}

function renderProducts() {
  const q = document.getElementById('search').value.toLowerCase();
  let filtered = allProducts.filter(p => {
    const matchCat = currentCategory === 'semua' || p.category === currentCategory;
    const matchQ = !q || p.name.toLowerCase().includes(q);
    return matchCat && matchQ;
  });

  const grid = document.getElementById('products-grid');
  const noEl = document.getElementById('no-products');

  if (filtered.length === 0) {
    grid.innerHTML = '';
    noEl.classList.remove('hidden');
    return;
  }
  noEl.classList.add('hidden');

  const logoMap = {
    'alight motion': 'AlightMotion_logo.png',
    'canva': 'Canva_logo.png',
    'capcut': 'CapCut_logo.png',
    'netflix': 'Netflix_logo.png',
    'spotify': 'Spotify_logo.png',
    'youtube': 'YouTube_logo.png',
  };

  grid.innerHTML = filtered.map(p => {
    const minPrice = p.variants.length > 0 ? Math.min(...p.variants.map(v => isReseller ? (v.reseller_price||v.price) : v.price)) : 0;
    const maxPrice = p.variants.length > 0 ? Math.max(...p.variants.map(v => isReseller ? (v.reseller_price||v.price) : v.price)) : 0;
    const totalStock = p.variants.reduce((s,v) => s+(v.stock||0), 0);
    const priceText = minPrice === maxPrice ? 'Rp '+minPrice.toLocaleString('id-ID') : 'Rp '+minPrice.toLocaleString('id-ID')+' - Rp '+maxPrice.toLocaleString('id-ID');

    // find logo
    let imgHtml = '<div class="w-full h-full bg-gradient-to-br from-primary/10 to-primary/20 flex items-center justify-center"><span class="text-primary font-bold text-2xl">'+p.name.charAt(0)+'</span></div>';
    const pNameLower = p.name.toLowerCase();
    for (const [key, file] of Object.entries(logoMap)) {
      if (pNameLower.includes(key)) {
        imgHtml = `<img src="image/${file}" alt="${p.name}" class="w-full h-full object-contain p-3">`;
        break;
      }
    }
    if (p.image) imgHtml = `<img src="${p.image}" alt="${p.name}" class="w-full h-full object-contain p-3" onerror="this.parentElement.innerHTML='<span class=text-primary font-bold text-xl>${p.name.charAt(0)}</span>'">`;

    return `<div class="product-card bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm cursor-pointer" onclick="openProduct('${p.id}')">
      ${p.badge ? `<div class="bg-gold px-2 py-1"><p class="text-black text-xs font-bold uppercase tracking-wide truncate">${p.badge}</p></div>` : ''}
      <div class="h-28 bg-gray-50 flex items-center justify-center overflow-hidden">${imgHtml}</div>
      <div class="p-3">
        <p class="text-sm font-bold text-gray-800 truncate">${p.name}</p>
        <p class="text-xs font-semibold text-primary mt-0.5">${priceText}</p>
        <p class="text-xs text-gray-400 mt-0.5">Stok: ${totalStock}</p>
      </div>
    </div>`;
  }).join('');
}

function openProduct(id) {
  const p = allProducts.find(x => x.id === id);
  if (!p) return;
  selectedProduct = p;
  selectedVariant = null;

  document.getElementById('modal-name').textContent = p.name;
  document.getElementById('modal-badge').textContent = p.badge || '';
  document.getElementById('modal-badge').style.display = p.badge ? 'inline-block' : 'none';
  document.getElementById('modal-desc').textContent = p.description || '';

  // Logo
  const logoMap = { 'alight motion':'AlightMotion_logo.png','canva':'Canva_logo.png','capcut':'CapCut_logo.png','netflix':'Netflix_logo.png','spotify':'Spotify_logo.png','youtube':'YouTube_logo.png' };
  const pNameLower = p.name.toLowerCase();
  let imgHtml = '<span class="text-2xl font-bold text-primary">'+p.name.charAt(0)+'</span>';
  for (const [key,file] of Object.entries(logoMap)) {
    if (pNameLower.includes(key)) { imgHtml=`<img src="image/${file}" class="w-full h-full object-contain p-1">`; break; }
  }
  if (p.image) imgHtml=`<img src="${p.image}" class="w-full h-full object-contain p-1" onerror="this.outerHTML='${p.name.charAt(0)}'">`;
  document.getElementById('modal-img-wrap').innerHTML = imgHtml;

  // Variants
  const varHtml = p.variants.map(v => {
    const price = isReseller ? (v.reseller_price||v.price) : v.price;
    const noStock = (v.stock||0) <= 0;
    return `<label class="flex items-center justify-between p-3.5 rounded-xl border-2 cursor-pointer transition-all hover:border-primary ${noStock?'opacity-50 cursor-not-allowed':''}">
      <div class="flex items-center gap-3">
        <input type="radio" name="variant" value="${v.id}" data-price="${price}" data-stock="${v.stock||0}" ${noStock?'disabled':''} onchange="selectVariant(this,${price},${v.stock||0})" class="accent-primary w-4 h-4">
        <div>
          <p class="text-sm font-semibold text-gray-800">${v.name}</p>
          <p class="text-xs text-gray-400">Stok: ${v.stock||0}</p>
        </div>
      </div>
      <span class="text-sm font-bold text-primary">Rp ${price.toLocaleString('id-ID')}</span>
    </label>`;
  }).join('');
  document.getElementById('modal-variants').innerHTML = varHtml || '<p class="text-gray-400 text-sm">Tidak ada varian tersedia</p>';
  document.getElementById('modal-selected').classList.add('hidden');
  document.getElementById('btn-beli').classList.add('hidden');
  document.getElementById('no-stock-msg').classList.add('hidden');
  document.getElementById('modal-product').classList.remove('hidden');
}

function selectVariant(radio, price, stock) {
  document.querySelectorAll('[name=variant]').forEach(r => r.closest('label').classList.remove('border-primary','bg-primary/5'));
  radio.closest('label').classList.add('border-primary','bg-primary/5');

  const v = selectedProduct.variants.find(x => x.id === radio.value);
  selectedVariant = { ...v, price };

  const info = document.getElementById('modal-selected');
  const btnBeli = document.getElementById('btn-beli');
  const noStockMsg = document.getElementById('no-stock-msg');

  document.getElementById('modal-balance-info').textContent = 'Rp ' + userBalance.toLocaleString('id-ID');
  document.getElementById('modal-price-info').textContent = 'Rp ' + price.toLocaleString('id-ID');
  const after = userBalance - price;
  document.getElementById('modal-after-balance').textContent = 'Rp ' + after.toLocaleString('id-ID');
  document.getElementById('modal-after-balance').className = 'text-sm font-bold ' + (after < 0 ? 'text-red-500' : 'text-gray-800');
  info.classList.remove('hidden');

  if (stock <= 0) {
    btnBeli.classList.add('hidden');
    noStockMsg.classList.remove('hidden');
  } else {
    btnBeli.classList.remove('hidden');
    noStockMsg.classList.add('hidden');
  }
}

async function processBuy() {
  if (!selectedVariant || !selectedProduct) return;
  if (userBalance < selectedVariant.price) { showToast('Saldo tidak cukup. Silakan deposit dulu.','error'); return; }

  const btn = document.getElementById('btn-beli');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner"></span> Memproses...';

  try {
    // Get a stock item
    const stockSnap = await db.collection('stock_items')
      .where('variant_id','==',selectedVariant.id)
      .where('is_used','==',false)
      .limit(1).get();

    if (stockSnap.empty) { showToast('Stok habis!','error'); btn.disabled=false; btn.innerHTML='Beli Sekarang'; return; }

    const stockDoc = stockSnap.docs[0];
    const invoice = generateInvoice();

    // Transaction: deduct balance, create order, mark stock as used
    await db.runTransaction(async t => {
      const userRef = db.collection('users').doc(currentUser.uid);
      const userSnap = await t.get(userRef);
      const currentBalance = userSnap.data().balance || 0;
      if (currentBalance < selectedVariant.price) throw new Error('Saldo tidak cukup');

      const orderRef = db.collection('orders').doc();
      const stockRef = db.collection('stock_items').doc(stockDoc.id);

      const variantRef = db.collection('products').doc(selectedProduct.id).collection('variants').doc(selectedVariant.id);
      const variantSnap = await t.get(variantRef);
      const currStock = variantSnap.data().stock || 0;

      t.update(userRef, { balance: currentBalance - selectedVariant.price });
      t.set(orderRef, {
        id: orderRef.id, invoice, user_id: currentUser.uid,
        product_id: selectedProduct.id, product_name: selectedProduct.name,
        variant_id: selectedVariant.id, variant_name: selectedVariant.name,
        price: selectedVariant.price, status: 'selesai',
        stock_item_id: stockDoc.id, stock_content: stockDoc.data().content,
        created_at: firebase.firestore.FieldValue.serverTimestamp(),
        completed_at: firebase.firestore.FieldValue.serverTimestamp()
      });
      t.update(stockRef, { is_used: true, order_id: orderRef.id });
      t.update(variantRef, { stock: Math.max(0, currStock - 1) });
      t.set(db.collection('balance_history').doc(), {
        user_id: currentUser.uid, type: 'pembelian',
        amount: -selectedVariant.price,
        description: `Pembelian ${selectedProduct.name} - ${selectedVariant.name}`,
        created_at: firebase.firestore.FieldValue.serverTimestamp()
      });
    });

    userBalance -= selectedVariant.price;
    document.getElementById('topbar-balance').textContent = 'Rp ' + userBalance.toLocaleString('id-ID');

    closeModal();
    showToast('Pembelian berhasil! Cek di Riwayat Pesanan.','success');
  } catch(e) {
    showToast(e.message || 'Gagal memproses pembelian.','error');
    btn.disabled=false; btn.innerHTML='Beli Sekarang';
  }
}

function closeModal() {
  document.getElementById('modal-product').classList.add('hidden');
  selectedVariant = null; selectedProduct = null;
}

function generateInvoice() {
  const d = new Date();
  const pad = n => String(n).padStart(2,'0');
  return 'INV'+d.getFullYear()+pad(d.getMonth()+1)+pad(d.getDate())+pad(d.getHours())+pad(d.getMinutes())+pad(d.getSeconds())+Math.floor(Math.random()*1000);
}
</script>
