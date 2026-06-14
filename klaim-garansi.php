<?php
$page_title = "Claim Garansi";
$current_page = "klaim";
$base_path = "";
require_once 'backend/includes/head.php';
?>
<div class="flex h-screen overflow-hidden">
  <?php require_once 'backend/includes/buyer-sidebar.php'; ?>
  <div class="flex-1 flex flex-col overflow-hidden">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-3 flex-shrink-0">
      <button class="lg:hidden text-gray-500" onclick="toggleSidebar()"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
      <h1 class="text-xl font-bold text-gray-800">Claim Garansi</h1>
    </header>
    <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
      <div class="bg-primary text-white rounded-2xl p-5 flex gap-3 mb-5">
        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        <div><p class="font-bold mb-0.5">Claim Garansi</p><p class="text-white/80 text-sm">Laporkan masalah pada akun yang kamu beli. Tim kami akan segera memproses laporan kamu.</p></div>
      </div>

      <button onclick="document.getElementById('modal-report').classList.remove('hidden')" class="btn-gold mb-5">+ Buat Report</button>

      <!-- Filters -->
      <div class="flex flex-wrap gap-2 mb-5">
        <div class="tab-scroll flex gap-2 pb-1">
          <button class="tab-btn active" onclick="setTypeFilter('semua',this)">Semua</button>
          <button class="tab-btn" onclick="setTypeFilter('error',this)">Error</button>
          <button class="tab-btn" onclick="setTypeFilter('canva',this)">Canva</button>
          <button class="tab-btn" onclick="setTypeFilter('renewal',this)">Renewal</button>
        </div>
      </div>
      <div class="flex gap-2 mb-5 tab-scroll">
        <button class="tab-btn active" onclick="setStatusFilter('semua',this,'status')">Semua Status</button>
        <button class="tab-btn" onclick="setStatusFilter('menunggu',this,'status')">Menunggu</button>
        <button class="tab-btn" onclick="setStatusFilter('diproses',this,'status')">Diproses</button>
        <button class="tab-btn" onclick="setStatusFilter('selesai',this,'status')">Selesai</button>
        <button class="tab-btn" onclick="setStatusFilter('ditolak',this,'status')">Ditolak</button>
      </div>

      <div id="claims-list"><div class="text-center py-12"><div class="spinner-dark mx-auto mb-2"></div><p class="text-sm text-gray-400">Memuat...</p></div></div>
    </main>
  </div>
</div>

<!-- Buat Report Modal -->
<div id="modal-report" class="modal-overlay hidden" onclick="if(event.target===this)this.classList.add('hidden')">
  <div class="modal-box">
    <div class="flex justify-between items-center mb-4">
      <h3 class="font-bold text-gray-800">Buat Report Garansi</h3>
      <button onclick="document.getElementById('modal-report').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="space-y-4">
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Garansi / Pesanan</label>
        <select id="claim-warranty" class="input-field text-sm"><option value="">Pilih garansi aktif...</option></select>
      </div>
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tipe Klaim</label>
        <select id="claim-type" class="input-field text-sm">
          <option value="error">Error (Akun Bermasalah)</option>
          <option value="canva">Canva (Kicked / Masalah Canva)</option>
          <option value="renewal">Renewal (Perpanjangan)</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi Masalah</label>
        <textarea id="claim-desc" rows="3" placeholder="Jelaskan masalah yang kamu alami..." class="input-field text-sm resize-none"></textarea>
      </div>
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Screenshot Bukti (opsional)</label>
        <input type="file" id="claim-image" accept="image/*" class="input-field text-sm">
      </div>
      <button onclick="submitClaim()" id="btn-claim" class="btn-primary w-full">Kirim Report</button>
    </div>
  </div>
</div>

<?php require_once 'backend/includes/footer.php'; ?>
<script>
let allClaims = [];
let allWarranties = [];
let typeFilter = 'semua', statusFilter = 'semua';
let currentUser = null;

auth.onAuthStateChanged(async user => {
  if (!user) { window.location.href = 'login.php'; return; }
  currentUser = user;
  // Load active warranties for select
  const wSnap = await db.collection('warranties').where('user_id','==',user.uid).where('status','==','aktif').get();
  allWarranties = wSnap.docs.map(d => ({id:d.id,...d.data()}));
  const sel = document.getElementById('claim-warranty');
  allWarranties.forEach(w => { const o = document.createElement('option'); o.value=w.id; o.textContent=w.product_name+' - '+w.variant_name; sel.appendChild(o); });
  loadClaims();
});

async function loadClaims() {
  const snap = await db.collection('claims').where('user_id','==',currentUser.uid).orderBy('created_at','desc').get();
  allClaims = snap.docs.map(d => ({id:d.id,...d.data()}));
  renderClaims();
}
function setTypeFilter(f, btn) { typeFilter=f; document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active')); btn.classList.add('active'); renderClaims(); }
function setStatusFilter(f, btn) { statusFilter=f; document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active')); btn.classList.add('active'); renderClaims(); }

function renderClaims() {
  const filtered = allClaims.filter(c => (typeFilter==='semua'||c.type===typeFilter) && (statusFilter==='semua'||c.status===statusFilter));
  if (!filtered.length) {
    document.getElementById('claims-list').innerHTML = `<div class="text-center py-14 bg-white rounded-2xl border border-gray-100">
      <svg class="w-14 h-14 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
      <p class="text-gray-500 font-medium">Belum ada report.</p>
      <p class="text-gray-400 text-sm mt-1">Klik "Buat Report" untuk melaporkan masalah.</p>
    </div>`;
    return;
  }
  const statusMap = { menunggu:['bg-yellow-100 text-yellow-700','Menunggu'], diproses:['bg-blue-100 text-blue-700','Diproses'], selesai:['bg-green-100 text-green-700','Selesai'], ditolak:['bg-red-100 text-red-600','Ditolak'] };
  const typeMap = { error:'Error', canva:'Canva', renewal:'Renewal' };
  document.getElementById('claims-list').innerHTML = `<div class="space-y-3">${filtered.map(c => {
    const [sc,sl] = statusMap[c.status] || ['bg-gray-100 text-gray-500',c.status];
    const d = c.created_at ? new Date(c.created_at.seconds*1000).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'}) : '-';
    return `<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
      <div class="flex justify-between items-start mb-2">
        <div><span class="text-xs bg-gray-100 text-gray-600 font-semibold px-2 py-0.5 rounded mr-2">${typeMap[c.type]||c.type}</span><span class="text-xs font-semibold px-2 py-0.5 rounded-lg ${sc}">${sl}</span></div>
        <span class="text-xs text-gray-400">${d}</span>
      </div>
      <p class="text-sm text-gray-700 mt-2">${c.description||'-'}</p>
      ${c.admin_note ? `<div class="mt-3 bg-blue-50 border border-blue-100 rounded-lg p-3 text-xs text-blue-700"><strong>Catatan Admin:</strong> ${c.admin_note}</div>` : ''}
    </div>`;
  }).join('')}</div>`;
}

async function submitClaim() {
  const warrantyId = document.getElementById('claim-warranty').value;
  const type = document.getElementById('claim-type').value;
  const desc = document.getElementById('claim-desc').value.trim();
  if (!warrantyId) { showToast('Pilih garansi terlebih dahulu','error'); return; }
  if (!desc) { showToast('Isi deskripsi masalah','error'); return; }
  const btn = document.getElementById('btn-claim');
  btn.disabled=true; btn.innerHTML='<span class="spinner"></span> Mengirim...';
  try {
    let imageUrl = null;
    const imageFile = document.getElementById('claim-image').files[0];
    if (imageFile) {
      const ref = storage.ref('claim_images/'+currentUser.uid+'_'+Date.now());
      await ref.put(imageFile);
      imageUrl = await ref.getDownloadURL();
    }
    await db.collection('claims').add({ user_id: currentUser.uid, warranty_id: warrantyId, type, description: desc, images: imageUrl ? [imageUrl] : [], status: 'menunggu', admin_note: null, created_at: firebase.firestore.FieldValue.serverTimestamp(), updated_at: firebase.firestore.FieldValue.serverTimestamp() });
    showToast('Report berhasil dikirim!','success');
    document.getElementById('modal-report').classList.add('hidden');
    document.getElementById('claim-desc').value='';
    loadClaims();
  } catch(e) { showToast('Gagal mengirim: '+e.message,'error'); }
  btn.disabled=false; btn.textContent='Kirim Report';
}
</script>
