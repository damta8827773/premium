<?php
$page_title = "Aktivasi Garansi";
$current_page = "garansi";
$base_path = "";
require_once 'backend/includes/head.php';
?>
<div class="flex h-screen overflow-hidden">
  <?php require_once 'backend/includes/buyer-sidebar.php'; ?>
  <div class="flex-1 flex flex-col overflow-hidden">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-3 flex-shrink-0">
      <button class="lg:hidden text-gray-500" onclick="toggleSidebar()"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
      <h1 class="text-xl font-bold text-gray-800">Aktivasi Garansi</h1>
    </header>
    <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
      <!-- Info Banner -->
      <div class="bg-primary text-white rounded-2xl p-5 flex gap-3 mb-5">
        <svg class="w-6 h-6 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        <div>
          <p class="font-bold mb-1">Aktivasi Garansi</p>
          <p class="text-white/80 text-sm">Upload bukti screenshot login dalam 1×24 jam setelah pembelian untuk mengaktifkan garansi akun. Garansi yang tidak diaktifkan akan hangus otomatis.</p>
        </div>
      </div>

      <!-- Filter Tabs -->
      <div class="flex gap-2 mb-5 tab-scroll pb-1">
        <button class="tab-btn active" onclick="setFilter('semua',this)">Semua <span id="cnt-semua" class="ml-1 text-xs">0</span></button>
        <button class="tab-btn" onclick="setFilter('menunggu',this)">Menunggu <span id="cnt-menunggu" class="ml-1 text-xs">0</span></button>
        <button class="tab-btn" onclick="setFilter('aktif',this)">Aktif <span id="cnt-aktif" class="ml-1 text-xs">0</span></button>
        <button class="tab-btn" onclick="setFilter('expired',this)">Expired <span id="cnt-expired" class="ml-1 text-xs">0</span></button>
      </div>

      <div id="warranty-list">
        <div class="text-center py-12"><div class="spinner-dark mx-auto mb-2"></div><p class="text-sm text-gray-400">Memuat...</p></div>
      </div>
    </main>
  </div>
</div>

<!-- Aktivasi Modal -->
<div id="modal-activate" class="modal-overlay hidden" onclick="if(event.target===this)this.classList.add('hidden')">
  <div class="modal-box">
    <div class="flex justify-between items-center mb-4">
      <h3 class="font-bold text-gray-800">Aktivasi Garansi</h3>
      <button onclick="document.getElementById('modal-activate').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <p class="text-sm text-gray-500 mb-4">Upload screenshot bukti login untuk pesanan ini:</p>
    <div id="activate-order-info" class="bg-gray-50 rounded-xl p-3 mb-4 text-sm text-gray-700"></div>
    <label class="upload-area block cursor-pointer mb-3" for="warranty-proof">
      <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
      <p class="text-sm text-gray-400">Klik untuk upload screenshot</p>
    </label>
    <input type="file" id="warranty-proof" accept="image/*" class="hidden" onchange="previewWarranty(this)">
    <img id="warranty-preview" class="hidden rounded-xl max-h-40 mx-auto mb-3 border">
    <input type="hidden" id="activate-order-id">
    <button onclick="submitActivation()" id="btn-activate" class="btn-primary w-full">Aktifkan Garansi</button>
  </div>
</div>

<?php require_once 'backend/includes/footer.php'; ?>
<script>
let allWarranties = [];
let currentFilter = 'semua';
let currentUser = null;
let warrantyFile = null;

auth.onAuthStateChanged(async user => {
  if (!user) { window.location.href = 'login.php'; return; }
  currentUser = user;
  loadWarranties();
});

async function loadWarranties() {
  const snap = await db.collection('warranties').where('user_id','==',currentUser.uid).orderBy('created_at','desc').get();
  allWarranties = snap.docs.map(d => ({id:d.id,...d.data()}));
  const counts = {semua:allWarranties.length,menunggu:0,aktif:0,expired:0};
  allWarranties.forEach(w => { if(counts[w.status]!==undefined) counts[w.status]++; });
  Object.entries(counts).forEach(([k,v]) => { const el = document.getElementById('cnt-'+k); if(el) el.textContent=v; });
  renderWarranties();
}

function setFilter(f, btn) {
  currentFilter = f;
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  renderWarranties();
}

function renderWarranties() {
  const filtered = currentFilter === 'semua' ? allWarranties : allWarranties.filter(w => w.status === currentFilter);
  if (!filtered.length) {
    document.getElementById('warranty-list').innerHTML = `<div class="text-center py-14 bg-white rounded-2xl border border-gray-100">
      <svg class="w-14 h-14 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
      <p class="text-gray-500 font-medium">Belum ada garansi yang tersedia.</p>
      <p class="text-gray-400 text-sm mt-1">Garansi akan muncul otomatis setelah kamu melakukan pembelian.</p>
    </div>`;
    return;
  }
  const statusMap = { menunggu:['bg-yellow-100 text-yellow-700','Menunggu'], aktif:['bg-green-100 text-green-700','Aktif'], expired:['bg-gray-100 text-gray-500','Expired'] };
  document.getElementById('warranty-list').innerHTML = `<div class="space-y-3">${filtered.map(w => {
    const [sc,sl] = statusMap[w.status] || ['bg-gray-100 text-gray-500',w.status];
    const d = w.created_at ? new Date(w.created_at.seconds*1000).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'}) : '-';
    const exp = w.expires_at ? new Date(w.expires_at.seconds*1000).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'}) : '-';
    return `<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
      <div class="flex items-start justify-between mb-3">
        <div><p class="font-bold text-gray-800">${w.product_name||''}</p><p class="text-sm text-gray-400 mt-0.5">${w.variant_name||''}</p></div>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-lg ${sc}">${sl}</span>
      </div>
      <div class="text-xs text-gray-400 space-y-1">
        <p>Tanggal Pembelian: ${d}</p>
        ${w.status==='aktif' ? `<p>Expired: ${exp}</p>` : ''}
      </div>
      ${w.status==='menunggu' ? `<button onclick="openActivate('${w.order_id}','${w.product_name} - ${w.variant_name}')" class="mt-3 btn-primary text-sm py-2 px-4 w-full">Upload Bukti Login</button>` : ''}
      ${w.proof_image ? `<a href="${w.proof_image}" target="_blank" class="inline-block mt-2 text-xs text-primary hover:underline">Lihat bukti →</a>` : ''}
    </div>`;
  }).join('')}</div>`;
}

function openActivate(orderId, info) {
  document.getElementById('activate-order-id').value = orderId;
  document.getElementById('activate-order-info').textContent = info;
  document.getElementById('warranty-preview').classList.add('hidden');
  warrantyFile = null;
  document.getElementById('modal-activate').classList.remove('hidden');
}
function previewWarranty(input) {
  warrantyFile = input.files[0];
  if (!warrantyFile) return;
  const reader = new FileReader();
  reader.onload = e => { const img = document.getElementById('warranty-preview'); img.src=e.target.result; img.classList.remove('hidden'); };
  reader.readAsDataURL(warrantyFile);
}
async function submitActivation() {
  if (!warrantyFile) { showToast('Upload screenshot terlebih dahulu','error'); return; }
  const orderId = document.getElementById('activate-order-id').value;
  const btn = document.getElementById('btn-activate');
  btn.disabled=true; btn.innerHTML='<span class="spinner"></span> Mengupload...';
  try {
    const ref = storage.ref('warranty_proofs/'+orderId+'_'+Date.now());
    await ref.put(warrantyFile);
    const url = await ref.getDownloadURL();
    // Find warranty doc by order_id
    const wSnap = await db.collection('warranties').where('order_id','==',orderId).where('user_id','==',currentUser.uid).get();
    if (!wSnap.empty) {
      await wSnap.docs[0].ref.update({ proof_image: url, status: 'menunggu', updated_at: firebase.firestore.FieldValue.serverTimestamp() });
    }
    showToast('Bukti berhasil dikirim! Admin akan mengaktifkan garansi Anda.','success');
    document.getElementById('modal-activate').classList.add('hidden');
    loadWarranties();
  } catch(e) { showToast('Gagal upload: '+e.message,'error'); }
  btn.disabled=false; btn.textContent='Aktifkan Garansi';
}
</script>
