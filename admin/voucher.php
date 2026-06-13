<?php
$page_title = "Kelola Voucher";
$current_page = "admin-voucher";
$base_path = "../";
require_once '../includes/head.php';
?>
<div class="flex h-screen overflow-hidden">
  <?php require_once '../includes/admin-sidebar.php'; ?>
  <div class="flex-1 flex flex-col overflow-hidden">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between flex-shrink-0">
      <div class="flex items-center gap-3">
        <button class="lg:hidden text-gray-500" onclick="toggleSidebar()"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
        <h1 class="text-xl font-bold text-gray-800">Kelola Voucher</h1>
      </div>
      <button onclick="openAdd()" class="btn-primary text-sm py-2.5 px-4">+ Buat Voucher</button>
    </header>
    <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
      <div id="voucher-list"><div class="text-center py-10"><div class="spinner-dark mx-auto mb-2"></div></div></div>
    </main>
  </div>
</div>
<div id="modal-v" class="modal-overlay hidden" onclick="if(event.target===this)this.classList.add('hidden')">
  <div class="modal-box">
    <div class="flex justify-between items-center mb-4">
      <h3 class="font-bold text-gray-800 text-lg">Buat Voucher</h3>
      <button onclick="document.getElementById('modal-v').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="space-y-4">
      <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Kode Voucher</label>
        <div class="flex gap-2"><input type="text" id="v-code" class="input-field text-sm uppercase tracking-widest font-bold flex-1" placeholder="NAISEY2026" oninput="this.value=this.value.toUpperCase()"><button onclick="generateCode()" class="btn-outline text-xs px-3 whitespace-nowrap">Generate</button></div>
      </div>
      <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Nilai Saldo (Rp)</label><input type="number" id="v-amount" class="input-field text-sm" placeholder="10000" min="1000"></div>
      <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Maks. Penggunaan</label><input type="number" id="v-max" class="input-field text-sm" placeholder="100" min="1"></div>
      <button onclick="saveVoucher()" id="btn-save-v" class="btn-primary w-full">Buat Voucher</button>
    </div>
  </div>
</div>
<?php require_once '../includes/footer.php'; ?>
<script>
let vouchers=[];
auth.onAuthStateChanged(async user=>{
  if(!user){window.location.href='../login.php';return;}
  const s=await db.collection('users').doc(user.uid).get();
  if(!s.exists||s.data().role!=='admin'){window.location.href='../dashboard.php';return;}
  loadVouchers();
});
async function loadVouchers(){
  const snap=await db.collection('vouchers').orderBy('created_at','desc').get();
  vouchers=snap.docs.map(d=>({id:d.id,...d.data()}));
  renderVouchers();
}
function renderVouchers(){
  if(!vouchers.length){document.getElementById('voucher-list').innerHTML='<p class="text-center text-gray-400 py-10">Belum ada voucher</p>';return;}
  document.getElementById('voucher-list').innerHTML=`<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm"><thead><tr class="bg-gray-50 border-b text-left"><th class="px-4 py-3 text-xs font-semibold text-gray-600">Kode</th><th class="px-4 py-3 text-xs font-semibold text-gray-600">Nilai</th><th class="px-4 py-3 text-xs font-semibold text-gray-600">Terpakai</th><th class="px-4 py-3 text-xs font-semibold text-gray-600">Status</th><th class="px-4 py-3 text-xs font-semibold text-gray-600">Aksi</th></tr></thead>
    <tbody class="divide-y divide-gray-50">${vouchers.map(v=>`<tr class="hover:bg-gray-50">
      <td class="px-4 py-3 font-mono font-bold text-primary">${v.id}</td>
      <td class="px-4 py-3 font-bold">Rp ${(v.amount||0).toLocaleString('id-ID')}</td>
      <td class="px-4 py-3">${v.used_count||0} / ${v.max_uses||0}</td>
      <td class="px-4 py-3"><span class="text-xs font-semibold px-2 py-0.5 rounded-lg ${v.is_active?'bg-green-100 text-green-700':'bg-red-100 text-red-600'}">${v.is_active?'Aktif':'Nonaktif'}</span></td>
      <td class="px-4 py-3"><div class="flex gap-2">
        <button onclick="toggleVoucher('${v.id}',${v.is_active})" class="text-xs font-semibold px-3 py-1.5 rounded-lg ${v.is_active?'bg-gray-200 text-gray-700 hover:bg-gray-300':'bg-green-500 text-white hover:bg-green-600'}">${v.is_active?'Nonaktifkan':'Aktifkan'}</button>
        <button onclick="deleteVoucher('${v.id}')" class="text-xs bg-red-500 text-white font-semibold px-3 py-1.5 rounded-lg hover:bg-red-600">Hapus</button>
      </div></td>
    </tr>`).join('')}</tbody></table></div>`;
}
function openAdd(){document.getElementById('v-code').value='';document.getElementById('v-amount').value='';document.getElementById('v-max').value='';document.getElementById('modal-v').classList.remove('hidden');}
function generateCode(){document.getElementById('v-code').value='VOUCHER'+Math.random().toString(36).substring(2,8).toUpperCase();}
async function saveVoucher(){
  const code=document.getElementById('v-code').value.trim().toUpperCase();
  const amount=parseInt(document.getElementById('v-amount').value)||0;
  const max=parseInt(document.getElementById('v-max').value)||0;
  if(!code||amount<1000||max<1){showToast('Lengkapi semua field','error');return;}
  const btn=document.getElementById('btn-save-v');btn.disabled=true;btn.innerHTML='<span class="spinner"></span>';
  try{
    await db.collection('vouchers').doc(code).set({amount,max_uses:max,used_count:0,is_active:true,created_at:firebase.firestore.FieldValue.serverTimestamp()});
    showToast('Voucher '+code+' berhasil dibuat!','success');
    document.getElementById('modal-v').classList.add('hidden');
    loadVouchers();
  }catch(e){showToast('Gagal: '+e.message,'error');}
  btn.disabled=false;btn.textContent='Buat Voucher';
}
async function toggleVoucher(id,isActive){
  await db.collection('vouchers').doc(id).update({is_active:!isActive});
  showToast('Status diperbarui','success');loadVouchers();
}
async function deleteVoucher(id){
  if(!confirm('Hapus voucher '+id+'?'))return;
  await db.collection('vouchers').doc(id).delete();
  showToast('Dihapus','success');loadVouchers();
}
</script>
