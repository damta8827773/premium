<?php
$page_title = "Kelola Pengumuman";
$current_page = "admin-pengumuman";
$base_path = "../";
require_once '../includes/head.php';
?>
<div class="flex h-screen overflow-hidden">
  <?php require_once '../includes/admin-sidebar.php'; ?>
  <div class="flex-1 flex flex-col overflow-hidden">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between flex-shrink-0">
      <div class="flex items-center gap-3">
        <button class="lg:hidden text-gray-500" onclick="toggleSidebar()"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
        <h1 class="text-xl font-bold text-gray-800">Kelola Pengumuman</h1>
      </div>
      <button onclick="openAddAnn()" class="btn-primary text-sm py-2.5 px-4">+ Tambah Pengumuman</button>
    </header>
    <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
      <div id="ann-list"><div class="text-center py-10"><div class="spinner-dark mx-auto mb-2"></div></div></div>
    </main>
  </div>
</div>

<div id="modal-ann" class="modal-overlay hidden" onclick="if(event.target===this)this.classList.add('hidden')">
  <div class="modal-box">
    <div class="flex justify-between items-center mb-4">
      <h3 id="modal-ann-title" class="font-bold text-gray-800 text-lg">Tambah Pengumuman</h3>
      <button onclick="document.getElementById('modal-ann').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <input type="hidden" id="ann-id">
    <div class="space-y-4">
      <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Judul</label><input type="text" id="ann-title" class="input-field text-sm" placeholder="Judul pengumuman"></div>
      <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Konten</label><textarea id="ann-content" rows="8" class="input-field text-sm resize-none" placeholder="Isi pengumuman..."></textarea></div>
      <button onclick="saveAnn()" id="btn-save-ann" class="btn-primary w-full">Simpan</button>
    </div>
  </div>
</div>
<?php require_once '../includes/footer.php'; ?>
<script>
let anns=[];
auth.onAuthStateChanged(async user=>{
  if(!user){window.location.href='../login.php';return;}
  const s=await db.collection('users').doc(user.uid).get();
  if(!s.exists||s.data().role!=='admin'){window.location.href='../dashboard.php';return;}
  loadAnns();
});
async function loadAnns(){
  const snap=await db.collection('announcements').orderBy('created_at','desc').get();
  anns=snap.docs.map(d=>({id:d.id,...d.data()}));
  renderAnns();
}
function renderAnns(){
  if(!anns.length){document.getElementById('ann-list').innerHTML='<p class="text-center text-gray-400 py-10">Belum ada pengumuman</p>';return;}
  document.getElementById('ann-list').innerHTML=`<div class="space-y-3">${anns.map(a=>{
    const d=a.created_at?new Date(a.created_at.seconds*1000).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'}):'-';
    const preview=(a.content||'').substring(0,120);
    return`<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 announcement-card">
      <div class="flex items-start justify-between mb-2">
        <h3 class="font-bold text-gray-800">${a.title||'Tanpa Judul'}</h3>
        <div class="flex gap-2 flex-shrink-0 ml-3">
          <button onclick="editAnn('${a.id}')" class="text-xs bg-primary text-white font-semibold px-3 py-1.5 rounded-lg hover:bg-primary-light">Edit</button>
          <button onclick="deleteAnn('${a.id}')" class="text-xs bg-red-500 text-white font-semibold px-3 py-1.5 rounded-lg hover:bg-red-600">Hapus</button>
        </div>
      </div>
      <p class="text-xs text-gray-400 mb-2">${d}</p>
      <p class="text-sm text-gray-600 whitespace-pre-wrap">${preview}${(a.content||'').length>120?'...':''}</p>
    </div>`;
  }).join('')}</div>`;
}
function openAddAnn(){
  document.getElementById('ann-id').value='';
  document.getElementById('ann-title').value='';
  document.getElementById('ann-content').value='';
  document.getElementById('modal-ann-title').textContent='Tambah Pengumuman';
  document.getElementById('modal-ann').classList.remove('hidden');
}
function editAnn(id){
  const a=anns.find(x=>x.id===id);if(!a)return;
  document.getElementById('ann-id').value=id;
  document.getElementById('ann-title').value=a.title||'';
  document.getElementById('ann-content').value=a.content||'';
  document.getElementById('modal-ann-title').textContent='Edit Pengumuman';
  document.getElementById('modal-ann').classList.remove('hidden');
}
async function saveAnn(){
  const title=document.getElementById('ann-title').value.trim();
  const content=document.getElementById('ann-content').value.trim();
  const id=document.getElementById('ann-id').value;
  if(!title||!content){showToast('Judul dan konten wajib diisi','error');return;}
  const btn=document.getElementById('btn-save-ann');btn.disabled=true;btn.innerHTML='<span class="spinner"></span>';
  try{
    if(id) await db.collection('announcements').doc(id).update({title,content,updated_at:firebase.firestore.FieldValue.serverTimestamp()});
    else await db.collection('announcements').add({title,content,created_at:firebase.firestore.FieldValue.serverTimestamp()});
    showToast('Pengumuman disimpan!','success');
    document.getElementById('modal-ann').classList.add('hidden');
    loadAnns();
  }catch(e){showToast('Gagal: '+e.message,'error');}
  btn.disabled=false;btn.textContent='Simpan';
}
async function deleteAnn(id){
  if(!confirm('Hapus pengumuman ini?'))return;
  await db.collection('announcements').doc(id).delete();
  showToast('Dihapus','success');loadAnns();
}
</script>
