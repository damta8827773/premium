<?php
$page_title = "Deposit & Pembayaran";
$current_page = "admin-pembayaran";
$base_path = "../";
require_once '../includes/head.php';
?>
<div class="flex h-screen overflow-hidden">
  <?php require_once '../includes/admin-sidebar.php'; ?>
  <div class="flex-1 flex flex-col overflow-hidden">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-3 flex-shrink-0">
      <button class="lg:hidden text-gray-500" onclick="toggleSidebar()"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
      <h1 class="text-xl font-bold text-gray-800">Deposit & Pembayaran</h1>
    </header>
    <main class="flex-1 overflow-y-auto p-6 bg-gray-50">

      <!-- Manual Top Up Section -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-5">
        <h2 class="font-bold text-gray-800 mb-4">Top Up Manual Saldo Pengguna</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email / UID Pengguna</label>
            <input type="text" id="topup-uid" placeholder="Email atau UID" class="input-field text-sm">
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jumlah (Rp)</label>
            <input type="number" id="topup-amount" placeholder="50000" class="input-field text-sm" min="1">
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan</label>
            <input type="text" id="topup-note" placeholder="Keterangan top up" class="input-field text-sm">
          </div>
        </div>
        <button onclick="doManualTopup()" id="btn-topup" class="btn-primary mt-3 text-sm py-2.5 px-5">Top Up Saldo</button>
      </div>

      <!-- Filter Tabs -->
      <div class="flex gap-2 mb-4 tab-scroll pb-1">
        <button class="tab-btn active" onclick="setFilter('semua',this)">Semua</button>
        <button class="tab-btn" onclick="setFilter('pending',this)">Pending <span id="cnt-pending" class="ml-1 text-xs font-bold text-red-500"></span></button>
        <button class="tab-btn" onclick="setFilter('success',this)">Sukses</button>
        <button class="tab-btn" onclick="setFilter('failed',this)">Gagal</button>
        <button class="tab-btn" onclick="setFilter('manual',this)" style="display:none">Manual</button>
      </div>
      <div class="flex gap-2 mb-5">
        <button class="tab-btn active" onclick="setMethodFilter('semua',this)">Semua Metode</button>
        <button class="tab-btn" onclick="setMethodFilter('manual',this)">Manual</button>
        <button class="tab-btn" onclick="setMethodFilter('midtrans',this)">Midtrans</button>
      </div>

      <div id="deposits-list"><div class="text-center py-10"><div class="spinner-dark mx-auto mb-2"></div><p class="text-sm text-gray-400">Memuat...</p></div></div>
    </main>
  </div>
</div>

<!-- Detail Modal -->
<div id="modal-dep" class="modal-overlay hidden" onclick="if(event.target===this)this.classList.add('hidden')">
  <div class="modal-box">
    <div class="flex justify-between items-center mb-4">
      <h3 class="font-bold text-gray-800">Detail Deposit</h3>
      <button onclick="document.getElementById('modal-dep').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div id="dep-detail"></div>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>
<script>
let allDeposits = [];
let statusFilter = 'semua', methodFilter = 'semua';

auth.onAuthStateChanged(async user => {
  if (!user) { window.location.href = '../login.php'; return; }
  const snap = await db.collection('users').doc(user.uid).get();
  if (!snap.exists || snap.data().role !== 'admin') { window.location.href = '../dashboard.php'; return; }
  loadDeposits();
});

async function loadDeposits() {
  const snap = await db.collection('deposits').orderBy('created_at','desc').limit(200).get();
  allDeposits = snap.docs.map(d=>({id:d.id,...d.data()}));
  const pendingCount = allDeposits.filter(d=>d.status==='pending'&&d.method==='manual').length;
  document.getElementById('cnt-pending').textContent = pendingCount > 0 ? '('+pendingCount+')' : '';
  renderDeposits();
}

function setFilter(f, btn) { statusFilter=f; document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active')); btn.classList.add('active'); renderDeposits(); }
function setMethodFilter(f, btn) { methodFilter=f; btn.parentElement.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active')); btn.classList.add('active'); renderDeposits(); }

function renderDeposits() {
  const filtered = allDeposits.filter(d => {
    const mS = statusFilter==='semua' || d.status===statusFilter;
    const mM = methodFilter==='semua' || d.method===methodFilter;
    return mS && mM;
  });
  if(!filtered.length) { document.getElementById('deposits-list').innerHTML='<div class="text-center py-10 bg-white rounded-2xl border border-gray-100"><p class="text-gray-400">Tidak ada data</p></div>'; return; }

  const statusMap = { pending:['bg-yellow-100 text-yellow-700','Pending'], success:['bg-green-100 text-green-700','Sukses'], failed:['bg-red-100 text-red-600','Gagal'], expired:['bg-gray-100 text-gray-500','Expired'] };
  document.getElementById('deposits-list').innerHTML = `<div class="bg-white rounded-2xl border border-gray-100 shadow-sm divide-y divide-gray-50">${filtered.map(dep => {
    const [sc,sl] = statusMap[dep.status] || ['bg-gray-100 text-gray-500', dep.status];
    const d = dep.created_at ? new Date(dep.created_at.seconds*1000).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}) : '-';
    const isManualPending = dep.method==='manual' && dep.status==='pending';
    return `<div class="p-4 flex items-center justify-between gap-3">
      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 mb-0.5">
          <span class="text-xs font-semibold ${dep.method==='midtrans'?'text-blue-600':'text-orange-600'} bg-opacity-10 bg-blue-100 px-2 py-0.5 rounded">${dep.method==='midtrans'?'Midtrans':'Manual'}</span>
          <span class="text-xs font-semibold px-2 py-0.5 rounded-lg ${sc}">${sl}</span>
        </div>
        <p class="font-bold text-gray-800">Rp ${(dep.amount||0).toLocaleString('id-ID')}</p>
        <p class="text-xs text-gray-400">${dep.user_id?.slice(0,12)||'-'} · ${d}</p>
      </div>
      <div class="flex gap-2 flex-shrink-0">
        ${dep.proof_image?`<button onclick="showDepDetail('${dep.id}')" class="text-xs bg-gray-100 text-gray-700 font-semibold px-3 py-1.5 rounded-lg hover:bg-gray-200">Bukti</button>`:''}
        ${isManualPending?`<button onclick="approveDeposit('${dep.id}','${dep.user_id}',${dep.amount})" class="text-xs bg-green-500 text-white font-semibold px-3 py-1.5 rounded-lg hover:bg-green-600">Approve</button>
        <button onclick="rejectDeposit('${dep.id}')" class="text-xs bg-red-500 text-white font-semibold px-3 py-1.5 rounded-lg hover:bg-red-600">Tolak</button>`:''}
      </div>
    </div>`;
  }).join('')}</div>`;
}

function showDepDetail(id) {
  const dep = allDeposits.find(x=>x.id===id);
  if (!dep) return;
  document.getElementById('dep-detail').innerHTML = `
    <div class="space-y-3">
      <div class="bg-gray-50 rounded-xl p-4 space-y-2 text-sm">
        <div class="flex justify-between"><span class="text-gray-500">User ID</span><span class="font-semibold">${dep.user_id||'-'}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Jumlah</span><span class="font-bold text-green-600">Rp ${(dep.amount||0).toLocaleString('id-ID')}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Metode</span><span class="font-semibold">${dep.method||'-'}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Status</span><span class="font-semibold">${dep.status||'-'}</span></div>
        ${dep.note?`<div class="flex justify-between"><span class="text-gray-500">Catatan</span><span class="font-semibold">${dep.note}</span></div>`:''}
      </div>
      ${dep.proof_image?`<img src="${dep.proof_image}" class="w-full rounded-xl border" alt="Bukti">`:''}
      ${dep.status==='pending'&&dep.method==='manual'?`<div class="flex gap-3">
        <button onclick="approveDeposit('${dep.id}','${dep.user_id}',${dep.amount});document.getElementById('modal-dep').classList.add('hidden')" class="btn-primary flex-1 text-sm py-2.5">✓ Approve</button>
        <button onclick="rejectDeposit('${dep.id}');document.getElementById('modal-dep').classList.add('hidden')" class="flex-1 text-sm py-2.5 bg-red-500 text-white font-semibold rounded-xl hover:bg-red-600">✕ Tolak</button>
      </div>`:''}
    </div>`;
  document.getElementById('modal-dep').classList.remove('hidden');
}

async function approveDeposit(depId, userId, amount) {
  if (!confirm('Approve deposit Rp '+amount.toLocaleString('id-ID')+'?')) return;
  try {
    await db.runTransaction(async t => {
      const depRef = db.collection('deposits').doc(depId);
      const userRef = db.collection('users').doc(userId);
      t.update(depRef, { status:'success', completed_at: firebase.firestore.FieldValue.serverTimestamp() });
      t.update(userRef, { balance: firebase.firestore.FieldValue.increment(amount) });
      t.set(db.collection('balance_history').doc(), { user_id:userId, type:'deposit', amount, description:'Deposit Manual - Approved by Admin', created_at: firebase.firestore.FieldValue.serverTimestamp() });
    });
    showToast('Deposit berhasil di-approve!','success');
    loadDeposits();
  } catch(e) { showToast('Gagal: '+e.message,'error'); }
}

async function rejectDeposit(depId) {
  if (!confirm('Tolak deposit ini?')) return;
  await db.collection('deposits').doc(depId).update({ status:'failed', updated_at: firebase.firestore.FieldValue.serverTimestamp() });
  showToast('Deposit ditolak.','info');
  loadDeposits();
}

async function doManualTopup() {
  const emailOrUid = document.getElementById('topup-uid').value.trim();
  const amount = parseInt(document.getElementById('topup-amount').value);
  const note = document.getElementById('topup-note').value.trim();
  if (!emailOrUid || !amount || amount < 1) { showToast('Isi semua field dengan benar','error'); return; }
  const btn = document.getElementById('btn-topup');
  btn.disabled=true; btn.innerHTML='<span class="spinner"></span> Memproses...';
  try {
    // Find user by email or uid
    let userId = null;
    const snap = await db.collection('users').where('email','==',emailOrUid).limit(1).get();
    if (!snap.empty) userId = snap.docs[0].id;
    else {
      const snap2 = await db.collection('users').doc(emailOrUid).get();
      if (snap2.exists) userId = emailOrUid;
    }
    if (!userId) throw new Error('Pengguna tidak ditemukan');
    await db.collection('users').doc(userId).update({ balance: firebase.firestore.FieldValue.increment(amount) });
    await db.collection('balance_history').add({ user_id:userId, type:'topup', amount, description:note||'Top Up Manual by Admin', created_at: firebase.firestore.FieldValue.serverTimestamp() });
    showToast('Top up Rp '+amount.toLocaleString('id-ID')+' berhasil!','success');
    document.getElementById('topup-uid').value='';
    document.getElementById('topup-amount').value='';
    document.getElementById('topup-note').value='';
  } catch(e) { showToast('Gagal: '+e.message,'error'); }
  btn.disabled=false; btn.textContent='Top Up Saldo';
}
</script>
