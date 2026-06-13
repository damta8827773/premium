<?php
$page_title = "Garansi & Klaim";
$current_page = "admin-garansi";
$base_path = "../";
require_once '../includes/head.php';
?>
<div class="flex h-screen overflow-hidden">
  <?php require_once '../includes/admin-sidebar.php'; ?>
  <div class="flex-1 flex flex-col overflow-hidden">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-3 flex-shrink-0">
      <button class="lg:hidden text-gray-500" onclick="toggleSidebar()"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
      <h1 class="text-xl font-bold text-gray-800">Garansi & Klaim</h1>
    </header>
    <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
      <!-- Tabs -->
      <div class="flex gap-3 mb-5">
        <button class="tab-btn active" onclick="showTab('warranties',this)">Aktivasi Garansi</button>
        <button class="tab-btn" onclick="showTab('claims',this)">Claim Garansi</button>
      </div>

      <!-- Warranties -->
      <div id="tab-warranties">
        <div id="warranties-list"><div class="text-center py-10"><div class="spinner-dark mx-auto mb-2"></div></div></div>
      </div>

      <!-- Claims -->
      <div id="tab-claims" class="hidden">
        <div id="claims-list"><div class="text-center py-10"><div class="spinner-dark mx-auto mb-2"></div></div></div>
      </div>
    </main>
  </div>
</div>

<div id="modal-claim" class="modal-overlay hidden" onclick="if(event.target===this)this.classList.add('hidden')">
  <div class="modal-box">
    <div class="flex justify-between items-center mb-4">
      <h3 class="font-bold text-gray-800">Proses Klaim</h3>
      <button onclick="document.getElementById('modal-claim').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div id="claim-detail-content"></div>
  </div>
</div>
<?php require_once '../includes/footer.php'; ?>
<script>
let warranties=[], claims=[];
auth.onAuthStateChanged(async user=>{
  if(!user){window.location.href='../login.php';return;}
  const s=await db.collection('users').doc(user.uid).get();
  if(!s.exists||s.data().role!=='admin'){window.location.href='../dashboard.php';return;}
  loadWarranties(); loadClaims();
});
function showTab(tab,btn){
  document.getElementById('tab-warranties').classList.toggle('hidden',tab!=='warranties');
  document.getElementById('tab-claims').classList.toggle('hidden',tab!=='claims');
  document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active');
}
async function loadWarranties(){
  const snap=await db.collection('warranties').orderBy('created_at','desc').get();
  warranties=snap.docs.map(d=>({id:d.id,...d.data()}));
  const stMap={menunggu:'bg-yellow-100 text-yellow-700',aktif:'bg-green-100 text-green-700',expired:'bg-gray-100 text-gray-500'};
  document.getElementById('warranties-list').innerHTML=!warranties.length?'<p class="text-center text-gray-400 py-8">Belum ada garansi</p>':
    `<div class="space-y-3">${warranties.map(w=>{
      const d=w.created_at?new Date(w.created_at.seconds*1000).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}):'-';
      const [sc]=(stMap[w.status]||'bg-gray-100 text-gray-500').split(' ');
      return`<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center justify-between">
        <div><p class="font-semibold text-gray-800 text-sm">${w.product_name||''} - ${w.variant_name||''}</p><p class="text-xs text-gray-400">${w.user_id?.slice(0,10)}... · ${d}</p></div>
        <div class="flex items-center gap-3">
          ${w.proof_image?`<a href="${w.proof_image}" target="_blank" class="text-xs text-primary font-semibold hover:underline">Lihat Bukti</a>`:''}
          <span class="text-xs font-semibold px-2 py-0.5 rounded-lg ${stMap[w.status]||'bg-gray-100 text-gray-500'}">${w.status||''}</span>
          ${w.status==='menunggu'?`<button onclick="activateWarranty('${w.id}')" class="text-xs bg-green-500 text-white font-semibold px-3 py-1.5 rounded-lg hover:bg-green-600">Aktifkan</button>`:''}
        </div>
      </div>`;
    }).join('')}</div>`;
}
async function activateWarranty(id){
  const expires=new Date(); expires.setMonth(expires.getMonth()+1);
  await db.collection('warranties').doc(id).update({status:'aktif',activated_at:firebase.firestore.FieldValue.serverTimestamp(),expires_at:firebase.firestore.Timestamp.fromDate(expires)});
  showToast('Garansi diaktifkan!','success'); loadWarranties();
}
async function loadClaims(){
  const snap=await db.collection('claims').orderBy('created_at','desc').get();
  claims=snap.docs.map(d=>({id:d.id,...d.data()}));
  const stMap={menunggu:'bg-yellow-100 text-yellow-700',diproses:'bg-blue-100 text-blue-700',selesai:'bg-green-100 text-green-700',ditolak:'bg-red-100 text-red-600'};
  const typeMap={error:'Error',canva:'Canva',renewal:'Renewal'};
  document.getElementById('claims-list').innerHTML=!claims.length?'<p class="text-center text-gray-400 py-8">Belum ada klaim</p>':
    `<div class="space-y-3">${claims.map(c=>{
      const d=c.created_at?new Date(c.created_at.seconds*1000).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}):'-';
      return`<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <div class="flex items-start justify-between mb-2">
          <div><span class="text-xs bg-gray-100 text-gray-600 font-semibold px-2 py-0.5 rounded mr-2">${typeMap[c.type]||c.type}</span><span class="text-xs font-semibold px-2 py-0.5 rounded-lg ${stMap[c.status]||'bg-gray-100 text-gray-500'}">${c.status||''}</span></div>
          <span class="text-xs text-gray-400">${d}</span>
        </div>
        <p class="text-sm text-gray-700 mb-3">${c.description||''}</p>
        ${c.images?.length?`<div class="flex gap-2 mb-3">${c.images.map(img=>`<a href="${img}" target="_blank"><img src="${img}" class="h-16 w-16 object-cover rounded-lg border"></a>`).join('')}</div>`:''}
        ${c.admin_note?`<div class="bg-blue-50 rounded-lg p-3 text-xs text-blue-700 mb-3"><strong>Catatan:</strong> ${c.admin_note}</div>`:''}
        ${c.status!=='selesai'&&c.status!=='ditolak'?`<button onclick="processClaim('${c.id}')" class="text-xs bg-primary text-white font-semibold px-3 py-1.5 rounded-lg hover:bg-primary-light">Proses Klaim</button>`:''}
      </div>`;
    }).join('')}</div>`;
}
function processClaim(id){
  const c=claims.find(x=>x.id===id);if(!c)return;
  document.getElementById('claim-detail-content').innerHTML=`<div class="space-y-4">
    <div class="bg-gray-50 rounded-xl p-3 text-sm text-gray-700"><p>${c.description||''}</p></div>
    ${c.images?.length?`<div class="flex gap-2">${c.images.map(img=>`<a href="${img}" target="_blank"><img src="${img}" class="h-20 rounded-xl border"></a>`).join('')}</div>`:''}
    <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Status</label>
      <select id="claim-status" class="input-field text-sm">
        <option value="diproses">Diproses</option><option value="selesai">Selesai</option><option value="ditolak">Ditolak</option>
      </select>
    </div>
    <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Catatan Admin (opsional)</label><textarea id="claim-admin-note" rows="3" class="input-field text-sm resize-none" placeholder="Catatan untuk pembeli...">${c.admin_note||''}</textarea></div>
    <div class="flex gap-3">
      <button onclick="updateClaim('${id}')" class="btn-primary flex-1 text-sm py-2.5">Simpan</button>
      <button onclick="document.getElementById('modal-claim').classList.add('hidden')" class="flex-1 text-sm py-2.5 border-2 border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50">Batal</button>
    </div>
  </div>`;
  document.getElementById('modal-claim').classList.remove('hidden');
}
async function updateClaim(id){
  const status=document.getElementById('claim-status').value;
  const note=document.getElementById('claim-admin-note').value.trim();
  await db.collection('claims').doc(id).update({status,admin_note:note||null,updated_at:firebase.firestore.FieldValue.serverTimestamp()});
  showToast('Klaim diperbarui!','success');
  document.getElementById('modal-claim').classList.add('hidden');
  loadClaims();
}
</script>
