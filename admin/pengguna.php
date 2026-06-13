<?php
$page_title = "Kelola Pengguna";
$current_page = "admin-pengguna";
$base_path = "../";
require_once '../includes/head.php';
?>
<div class="flex h-screen overflow-hidden">
  <?php require_once '../includes/admin-sidebar.php'; ?>
  <div class="flex-1 flex flex-col overflow-hidden">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-3 flex-shrink-0">
      <button class="lg:hidden text-gray-500" onclick="toggleSidebar()"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
      <h1 class="text-xl font-bold text-gray-800">Kelola Pengguna</h1>
    </header>
    <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
      <div class="relative mb-5">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke-width="2"/><path d="m21 21-4.35-4.35" stroke-linecap="round" stroke-width="2"/></svg>
        <input type="text" id="search" placeholder="Cari nama, email, username..." class="input-field pl-10 text-sm bg-white" oninput="renderUsers()">
      </div>
      <div id="users-list"><div class="text-center py-10"><div class="spinner-dark mx-auto mb-2"></div><p class="text-sm text-gray-400">Memuat...</p></div></div>
    </main>
  </div>
</div>

<div id="modal-user" class="modal-overlay hidden" onclick="if(event.target===this)this.classList.add('hidden')">
  <div class="modal-box">
    <div class="flex justify-between items-center mb-4">
      <h3 class="font-bold text-gray-800">Detail & Edit Pengguna</h3>
      <button onclick="document.getElementById('modal-user').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div id="user-detail"></div>
  </div>
</div>
<?php require_once '../includes/footer.php'; ?>
<script>
let allUsers=[];
auth.onAuthStateChanged(async user=>{
  if(!user){window.location.href='../login.php';return;}
  const s=await db.collection('users').doc(user.uid).get();
  if(!s.exists||s.data().role!=='admin'){window.location.href='../dashboard.php';return;}
  const snap=await db.collection('users').orderBy('created_at','desc').get();
  allUsers=snap.docs.map(d=>({id:d.id,...d.data()}));
  renderUsers();
});
function renderUsers(){
  const q=document.getElementById('search').value.toLowerCase();
  const filtered=q?allUsers.filter(u=>(u.name||'').toLowerCase().includes(q)||(u.email||'').toLowerCase().includes(q)||(u.username||'').toLowerCase().includes(q)):allUsers;
  if(!filtered.length){document.getElementById('users-list').innerHTML='<p class="text-center text-gray-400 py-8">Tidak ada pengguna</p>';return;}
  document.getElementById('users-list').innerHTML=`<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
      <thead><tr class="bg-gray-50 border-b text-left"><th class="px-4 py-3 text-xs font-semibold text-gray-600">Nama</th><th class="px-4 py-3 text-xs font-semibold text-gray-600">Email</th><th class="px-4 py-3 text-xs font-semibold text-gray-600">Saldo</th><th class="px-4 py-3 text-xs font-semibold text-gray-600">Role</th><th class="px-4 py-3 text-xs font-semibold text-gray-600">Aksi</th></tr></thead>
      <tbody class="divide-y divide-gray-50">${filtered.map(u=>`<tr class="hover:bg-gray-50">
        <td class="px-4 py-3"><p class="font-semibold text-gray-800">${u.name||'-'}</p><p class="text-xs text-gray-400">@${u.username||'-'}</p></td>
        <td class="px-4 py-3 text-xs text-gray-500">${u.email||'-'}</td>
        <td class="px-4 py-3 font-bold text-gray-800">Rp ${(u.balance||0).toLocaleString('id-ID')}</td>
        <td class="px-4 py-3"><span class="text-xs font-semibold px-2 py-0.5 rounded-lg ${u.role==='admin'?'bg-yellow-100 text-yellow-700':u.is_reseller?'bg-blue-100 text-blue-600':'bg-gray-100 text-gray-600'}">${u.role==='admin'?'Admin':u.is_reseller?'Reseller':'Buyer'}</span></td>
        <td class="px-4 py-3"><button onclick="showUser('${u.id}')" class="text-xs bg-primary text-white font-semibold px-3 py-1.5 rounded-lg hover:bg-primary-light">Detail</button></td>
      </tr>`).join('')}</tbody>
    </table></div></div>`;
}
function showUser(id){
  const u=allUsers.find(x=>x.id===id);if(!u)return;
  const d=u.created_at?new Date(u.created_at.seconds*1000).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'}):'-';
  document.getElementById('user-detail').innerHTML=`<div class="space-y-4">
    <div class="bg-gray-50 rounded-xl p-4 space-y-2 text-sm">
      <div class="flex justify-between"><span class="text-gray-500">UID</span><span class="font-mono text-xs text-gray-700">${u.id}</span></div>
      <div class="flex justify-between"><span class="text-gray-500">Nama</span><span class="font-semibold">${u.name||'-'}</span></div>
      <div class="flex justify-between"><span class="text-gray-500">Email</span><span class="font-semibold">${u.email||'-'}</span></div>
      <div class="flex justify-between"><span class="text-gray-500">Username</span><span class="font-semibold">@${u.username||'-'}</span></div>
      <div class="flex justify-between"><span class="text-gray-500">Telepon</span><span class="font-semibold">${u.phone||'-'}</span></div>
      <div class="flex justify-between"><span class="text-gray-500">Saldo</span><span class="font-bold text-green-600">Rp ${(u.balance||0).toLocaleString('id-ID')}</span></div>
      <div class="flex justify-between"><span class="text-gray-500">Role</span><span class="font-semibold">${u.role||'buyer'}</span></div>
      <div class="flex justify-between"><span class="text-gray-500">Daftar</span><span class="font-semibold">${d}</span></div>
    </div>
    <div class="flex gap-3">
      ${u.role!=='admin'?`<button onclick="setAdmin('${u.id}',true)" class="flex-1 text-xs bg-yellow-400 text-black font-bold py-2.5 rounded-xl hover:bg-yellow-500">Jadikan Admin</button>`:`<button onclick="setAdmin('${u.id}',false)" class="flex-1 text-xs bg-gray-200 text-gray-700 font-bold py-2.5 rounded-xl hover:bg-gray-300">Hapus Admin</button>`}
      ${!u.is_reseller?`<button onclick="setReseller('${u.id}',true)" class="flex-1 text-xs bg-blue-500 text-white font-bold py-2.5 rounded-xl hover:bg-blue-600">Jadikan Reseller</button>`:`<button onclick="setReseller('${u.id}',false)" class="flex-1 text-xs bg-gray-200 text-gray-700 font-bold py-2.5 rounded-xl hover:bg-gray-300">Hapus Reseller</button>`}
    </div>
  </div>`;
  document.getElementById('modal-user').classList.remove('hidden');
}
async function setAdmin(id,isAdmin){
  await db.collection('users').doc(id).update({role:isAdmin?'admin':'buyer'});
  showToast('Role diperbarui','success');
  const snap=await db.collection('users').orderBy('created_at','desc').get();
  allUsers=snap.docs.map(d=>({id:d.id,...d.data()}));
  document.getElementById('modal-user').classList.add('hidden');
  renderUsers();
}
async function setReseller(id,isRes){
  await db.collection('users').doc(id).update({is_reseller:isRes});
  showToast('Status reseller diperbarui','success');
  const u=allUsers.find(x=>x.id===id);if(u)u.is_reseller=isRes;
  document.getElementById('modal-user').classList.add('hidden');
  renderUsers();
}
</script>
