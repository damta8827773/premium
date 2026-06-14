<?php
$page_title = "Pengumuman";
$current_page = "pengumuman";
$base_path = "";
require_once 'backend/includes/head.php';
?>
<div class="flex h-screen overflow-hidden">
  <?php require_once 'backend/includes/buyer-sidebar.php'; ?>
  <div class="flex-1 flex flex-col overflow-hidden">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-3 flex-shrink-0">
      <button class="lg:hidden text-gray-500" onclick="toggleSidebar()"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
      <h1 class="text-xl font-bold text-gray-800">Pengumuman</h1>
    </header>
    <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
      <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-4">
          <div class="flex items-center justify-between mb-1">
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
              <h2 class="font-bold text-gray-800">Semua Pengumuman</h2>
            </div>
            <span id="ann-count" class="text-gold text-sm font-bold">0 pengumuman</span>
          </div>
        </div>
        <div id="announcements-list"><div class="text-center py-10"><div class="spinner-dark mx-auto mb-2"></div><p class="text-sm text-gray-400">Memuat...</p></div></div>
      </div>
    </main>
  </div>
</div>

<!-- Detail Modal -->
<div id="modal-detail" class="modal-overlay hidden" onclick="if(event.target===this)this.classList.add('hidden')">
  <div class="modal-box max-w-lg">
    <div class="flex justify-between items-start mb-4">
      <h3 id="modal-title" class="font-bold text-gray-800 text-lg pr-4"></h3>
      <button onclick="document.getElementById('modal-detail').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 flex-shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <p id="modal-time" class="text-xs text-gray-400 mb-4"></p>
    <div id="modal-content" class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed"></div>
  </div>
</div>

<?php require_once 'backend/includes/footer.php'; ?>
<script>
let announcements = [];
auth.onAuthStateChanged(async user => {
  if (!user) { window.location.href = 'login.php'; return; }
  const snap = await db.collection('announcements').orderBy('created_at','desc').get();
  announcements = snap.docs.map(d => ({id:d.id,...d.data()}));
  document.getElementById('ann-count').textContent = announcements.length + ' pengumuman';
  renderAnnouncements();
});

function renderAnnouncements() {
  if (!announcements.length) {
    document.getElementById('announcements-list').innerHTML = '<div class="text-center py-10 text-gray-400 bg-white rounded-2xl"><p>Belum ada pengumuman</p></div>';
    return;
  }
  document.getElementById('announcements-list').innerHTML = `<div class="space-y-3">${announcements.map(a => {
    const d = a.created_at ? timeAgo(a.created_at) : '';
    const preview = (a.content||'').substring(0,200);
    const isLong = (a.content||'').length > 200;
    return `<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 announcement-card cursor-pointer hover:shadow-md transition-shadow" onclick="showDetail('${a.id}')">
      <div class="flex justify-between items-start mb-2">
        <h3 class="font-bold text-gray-800 text-sm">${a.title||'Pengumuman'}</h3>
        <span class="text-xs text-gray-400 flex-shrink-0 ml-3">${d}</span>
      </div>
      <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-wrap">${preview}${isLong?'...':''}</p>
      ${isLong?`<button class="text-primary text-xs font-semibold mt-2 hover:underline">Selengkapnya</button>`:''}
    </div>`;
  }).join('')}</div>`;
}

function timeAgo(ts) {
  const d = ts.toDate ? ts.toDate() : new Date(ts.seconds*1000);
  const diff = Math.floor((Date.now()-d.getTime())/1000);
  if(diff<3600) return Math.floor(diff/60)+' menit lalu';
  if(diff<86400) return Math.floor(diff/3600)+' jam lalu';
  if(diff<604800) return Math.floor(diff/86400)+' hari lalu';
  if(diff<2592000) return Math.floor(diff/604800)+' minggu lalu';
  return Math.floor(diff/2592000)+' bulan lalu';
}

function showDetail(id) {
  const a = announcements.find(x => x.id === id);
  if (!a) return;
  document.getElementById('modal-title').textContent = a.title || 'Pengumuman';
  document.getElementById('modal-time').textContent = a.created_at ? timeAgo(a.created_at) : '';
  document.getElementById('modal-content').textContent = a.content || '';
  document.getElementById('modal-detail').classList.remove('hidden');
}
</script>
