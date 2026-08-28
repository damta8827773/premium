<?php
$page_title = "Live Chat Admin";
$current_page = "admin-livechat";
$base_path = "../";
require_once '../backend/includes/head.php';
?>
<style>
.lc-shell { display:flex; height:100%; overflow:hidden; }
.lc-list { width:320px; flex-shrink:0; border-right:1px solid #f0f0f0; display:flex; flex-direction:column; background:#fff; }
.lc-item { padding:12px 16px; border-bottom:1px solid #f8f8f8; cursor:pointer; transition:background 0.15s; }
.lc-item:hover, .lc-item.active { background:#f9fafb; }
.lc-item.active { box-shadow:inset 3px 0 0 0 #1B3528; }
.lc-avatar { width:36px; height:36px; border-radius:50%; background:#1B3528; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:13px; flex-shrink:0; }
.lc-badge-dot { width:8px; height:8px; border-radius:50%; background:#DC2626; flex-shrink:0; }
.bubble-row { display:flex; gap:8px; margin-bottom:14px; align-items:flex-end; }
.bubble-row.mine { flex-direction:row-reverse; }
.bubble-avatar { width:28px; height:28px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:10.5px; font-weight:800; color:#fff; }
.bubble-avatar.ai { background:linear-gradient(135deg,#7c3aed,#a855f7); }
.bubble-avatar.admin { background:#1B3528; }
.bubble-avatar.user { background:#EAB308; color:#000; }
.bubble { max-width:70%; padding:10px 14px; border-radius:16px; font-size:13.5px; line-height:1.5; white-space:pre-wrap; word-break:break-word; }
.bubble-row.mine .bubble { background:#1B3528; color:#fff; border-bottom-right-radius:4px; }
.bubble-row:not(.mine) .bubble { background:#fff; color:#111827; border:1px solid #f0f0f0; border-bottom-left-radius:4px; }
.bubble-meta { font-size:10.5px; color:#9ca3af; margin-top:3px; }
.bubble-row.mine .bubble-meta { text-align:right; }
</style>
<div class="flex h-screen overflow-hidden">
  <?php require_once '../backend/includes/admin-sidebar.php'; ?>
  <div class="flex-1 flex flex-col overflow-hidden">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-3 flex-shrink-0">
      <button class="lg:hidden text-gray-500" onclick="toggleSidebar()"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
      <h1 class="text-xl font-bold text-gray-800">Live Chat</h1>
    </header>
    <main class="flex-1 overflow-hidden bg-gray-50">
      <div class="lc-shell">
        <div class="lc-list">
          <div class="flex gap-1 p-3 border-b border-gray-100">
            <button class="tab-btn active" data-tab="waiting" onclick="switchTab('waiting')">Antrian</button>
            <button class="tab-btn" data-tab="active" onclick="switchTab('active')">Aktif Saya</button>
            <button class="tab-btn" data-tab="all" onclick="switchTab('all')">Semua</button>
          </div>
          <div id="lc-items" class="flex-1 overflow-y-auto"></div>
        </div>
        <div class="flex-1 flex flex-col overflow-hidden">
          <div id="lc-empty" class="flex-1 flex items-center justify-center text-gray-400 text-sm">Pilih chat di sebelah kiri</div>
          <div id="lc-thread" class="flex-1 flex-col overflow-hidden" style="display:none">
            <div class="bg-white border-b border-gray-100 px-5 py-3 flex items-center justify-between flex-shrink-0">
              <div>
                <p id="thread-name" class="font-bold text-gray-800 text-sm"></p>
                <p id="thread-meta" class="text-xs text-gray-400"></p>
              </div>
              <div class="flex gap-2">
                <button id="btn-claim" onclick="claimChat()" class="hidden text-xs font-semibold bg-primary text-white px-3 py-2 rounded-xl hover:bg-primary-light">Klaim Chat</button>
                <button id="btn-close" onclick="closeChat()" class="hidden text-xs font-semibold bg-gray-200 text-gray-700 px-3 py-2 rounded-xl hover:bg-gray-300">Selesaikan</button>
              </div>
            </div>
            <div id="thread-messages" class="flex-1 overflow-y-auto p-5"></div>
            <form id="thread-form" class="bg-white border-t border-gray-100 p-3 flex gap-2 flex-shrink-0">
              <input type="text" id="thread-input" class="input-field" placeholder="Balas sebagai admin..." autocomplete="off" maxlength="2000">
              <button type="submit" class="btn-primary !px-5">Kirim</button>
            </form>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
<?php require_once '../backend/includes/footer.php'; ?>
<script>
let adminUser = null, adminProfile = {};
let waitingChats = [], activeChats = [], allChats = [];
let currentTab = 'waiting';
let openChatId = null, openChatData = null, openChatUnsub = null, msgsUnsub = null;
let waitingInitialLoad = true, activeInitialLoad = true;
const alertedAt = {};
let audioCtx = null;
document.addEventListener('click', () => { try { getAudioCtx().resume(); } catch(e){} }, { once: true });

auth.onAuthStateChanged(async user => {
  if (!user) { window.location.href = '../login.php'; return; }
  const snap = await db.collection('users').doc(user.uid).get();
  if (!snap.exists || snap.data().role !== 'admin') { window.location.href = '../dashboard-225514cdf1ed.php'; return; }
  adminUser = user;
  adminProfile = snap.data();
  document.getElementById('admin-name').textContent = adminProfile.name || 'Admin';
  listenWaiting();
  listenActive();
  startPresenceHeartbeat();
});

function getAudioCtx() {
  if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
  return audioCtx;
}
function playAlertSound() {
  try {
    const ctx = getAudioCtx();
    [880, 1046].forEach((freq, i) => {
      setTimeout(() => {
        const o = ctx.createOscillator(), g = ctx.createGain();
        o.type = 'sine'; o.frequency.setValueAtTime(freq, ctx.currentTime);
        g.gain.setValueAtTime(0.0001, ctx.currentTime);
        g.gain.exponentialRampToValueAtTime(0.25, ctx.currentTime + 0.02);
        g.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.32);
        o.connect(g); g.connect(ctx.destination);
        o.start(); o.stop(ctx.currentTime + 0.35);
      }, i * 150);
    });
  } catch (e) {}
}
function maybeAlert(chat) {
  if (!chat.unread_admin) return;
  const ts = chat.last_message_at ? chat.last_message_at.seconds : 0;
  if (alertedAt[chat.id] === ts) return;
  alertedAt[chat.id] = ts;
  playAlertSound();
}

function listenWaiting() {
  db.collection('chats').where('status', '==', 'waiting_admin').onSnapshot(snap => {
    waitingChats = snap.docs.map(d => ({ id: d.id, ...d.data() }));
    waitingChats.sort((a, b) => (a.escalated_at?.seconds || 0) - (b.escalated_at?.seconds || 0));
    if (!waitingInitialLoad) {
      snap.docChanges().forEach(ch => { if (ch.type !== 'removed') maybeAlert({ id: ch.doc.id, ...ch.doc.data() }); });
    }
    waitingInitialLoad = false;
    if (currentTab === 'waiting') renderList();
    updateBadge();
  });
}
function listenActive() {
  db.collection('chats').where('assigned_admin_id', '==', adminUser.uid).where('status', '==', 'active_admin').onSnapshot(snap => {
    activeChats = snap.docs.map(d => ({ id: d.id, ...d.data() }));
    activeChats.sort((a, b) => (b.last_message_at?.seconds || 0) - (a.last_message_at?.seconds || 0));
    if (!activeInitialLoad) {
      snap.docChanges().forEach(ch => { if (ch.type !== 'removed') maybeAlert({ id: ch.doc.id, ...ch.doc.data() }); });
    }
    activeInitialLoad = false;
    if (currentTab === 'active') renderList();
    updateBadge();
  });
}
function updateBadge() {
  const count = waitingChats.length + activeChats.filter(c => c.unread_admin).length;
  const badge = document.getElementById('chat-badge');
  if (count > 0) { badge.textContent = count; badge.classList.remove('hidden'); }
  else { badge.classList.add('hidden'); }
}

async function switchTab(tab) {
  currentTab = tab;
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.toggle('active', b.dataset.tab === tab));
  if (tab === 'all') {
    const snap = await db.collection('chats').get();
    allChats = snap.docs.map(d => ({ id: d.id, ...d.data() }));
    allChats.sort((a, b) => (b.last_message_at?.seconds || 0) - (a.last_message_at?.seconds || 0));
  }
  renderList();
}

function fmtTime(ts) {
  if (!ts) return '';
  const d = ts.toDate ? ts.toDate() : new Date(ts);
  return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) + ' ' + d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
}
function fmtFull(ts) {
  if (!ts) return '-';
  const d = ts.toDate ? ts.toDate() : new Date(ts);
  return d.toLocaleDateString('id-ID', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' }) + ' · ' + d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
}

function renderList() {
  const list = currentTab === 'waiting' ? waitingChats : currentTab === 'active' ? activeChats : allChats;
  const wrap = document.getElementById('lc-items');
  if (!list.length) { wrap.innerHTML = '<p class="text-center text-gray-400 text-sm py-8">Tidak ada chat</p>'; return; }
  wrap.innerHTML = list.map(c => `
    <div class="lc-item ${c.id === openChatId ? 'active' : ''}" onclick="openChat('${c.id}')">
      <div class="flex items-center gap-3">
        <div class="lc-avatar">${escapeHtml((c.user_name || 'U').charAt(0).toUpperCase())}</div>
        <div class="flex-1 min-w-0">
          <div class="flex items-center justify-between gap-2">
            <p class="text-sm font-semibold text-gray-800 truncate">${escapeHtml(c.user_name || 'User')}</p>
            ${c.unread_admin ? '<span class="lc-badge-dot"></span>' : ''}
          </div>
          <p class="text-xs text-gray-400 truncate">${escapeHtml(c.last_message || '')}</p>
          <p class="text-[10px] text-gray-300 mt-0.5">${fmtTime(c.last_message_at)}</p>
        </div>
      </div>
    </div>`).join('');
}

async function openChat(id) {
  openChatId = id;
  renderList();
  document.getElementById('lc-empty').style.display = 'none';
  document.getElementById('lc-thread').style.display = 'flex';

  if (openChatUnsub) openChatUnsub();
  openChatUnsub = db.collection('chats').doc(id).onSnapshot(snap => {
    if (!snap.exists) return;
    openChatData = { id: snap.id, ...snap.data() };
    renderThreadHeader();
  });

  if (msgsUnsub) msgsUnsub();
  msgsUnsub = db.collection('chats').doc(id).collection('messages').orderBy('created_at', 'asc').onSnapshot(snap => {
    renderThreadMessages(snap.docs.map(d => ({ id: d.id, ...d.data() })));
    snap.docs.forEach(d => {
      const data = d.data();
      if (data.sender_type !== 'admin' && !data.read_by_admin) {
        d.ref.update({ read_by_admin: true, read_by_admin_at: firebase.firestore.FieldValue.serverTimestamp() }).catch(()=>{});
      }
    });
  });
  db.collection('chats').doc(id).update({ unread_admin: false }).catch(()=>{});
}

function renderThreadHeader() {
  document.getElementById('thread-name').textContent = openChatData.user_name || 'User';
  const statusLabel = { ai: 'Dibantu AI', waiting_admin: 'Menunggu diklaim', active_admin: 'Aktif ditangani ' + (openChatData.assigned_admin_name || ''), closed: 'Selesai' }[openChatData.status] || '';
  document.getElementById('thread-meta').textContent = (openChatData.user_email || '') + ' · ' + statusLabel;
  document.getElementById('btn-claim').classList.toggle('hidden', openChatData.status !== 'waiting_admin');
  document.getElementById('btn-close').classList.toggle('hidden', openChatData.status === 'closed');
}

function renderThreadMessages(msgs) {
  const wrap = document.getElementById('thread-messages');
  wrap.innerHTML = msgs.map(m => {
    const mine = m.sender_type === 'admin';
    const avatarClass = m.sender_type === 'ai' ? 'ai' : (m.sender_type === 'admin' ? 'admin' : 'user');
    const avatarText = m.sender_type === 'ai' ? 'AI' : (m.sender_type === 'admin' ? (m.sender_name || 'A').charAt(0).toUpperCase() : (openChatData?.user_name || 'U').charAt(0).toUpperCase());
    return `<div class="bubble-row ${mine ? 'mine' : ''}">
      <div class="bubble-avatar ${avatarClass}">${avatarText}</div>
      <div><div class="bubble">${escapeHtml(m.text)}</div><div class="bubble-meta">${fmtFull(m.created_at)}</div></div>
    </div>`;
  }).join('');
  wrap.scrollTop = wrap.scrollHeight;
}

document.getElementById('thread-form').addEventListener('submit', async e => {
  e.preventDefault();
  const input = document.getElementById('thread-input');
  const text = input.value.trim();
  if (!text || !openChatId) return;
  input.value = '';
  await db.collection('chats').doc(openChatId).collection('messages').add({
    sender_type: 'admin',
    sender_id: adminUser.uid,
    sender_name: adminProfile.name || 'Admin',
    text,
    created_at: firebase.firestore.FieldValue.serverTimestamp(),
    read_by_admin: true,
    read_by_admin_at: firebase.firestore.FieldValue.serverTimestamp(),
    read_by_user: false,
    read_by_user_at: null,
  });
  await db.collection('chats').doc(openChatId).update({
    last_message: text,
    last_message_at: firebase.firestore.FieldValue.serverTimestamp(),
    unread_user: true,
  });
  if (openChatData?.user_id) {
    db.collection('notifications').add({
      user_id: openChatData.user_id, title: 'Admin membalas chat kamu',
      message: text.length > 80 ? text.slice(0, 80) + '...' : text,
      read: false, created_at: firebase.firestore.FieldValue.serverTimestamp(),
    }).catch(() => {});
  }
});

async function claimChat() {
  if (!openChatId) return;
  await db.collection('chats').doc(openChatId).update({
    status: 'active_admin',
    assigned_admin_id: adminUser.uid,
    assigned_admin_name: adminProfile.name || 'Admin',
  });
  showToast('Chat berhasil diklaim', 'success');
}
async function closeChat() {
  if (!openChatId) return;
  if (!confirm('Tandai percakapan ini selesai?')) return;
  await db.collection('chats').doc(openChatId).update({ status: 'closed', closed_at: firebase.firestore.FieldValue.serverTimestamp() });
  showToast('Percakapan ditutup', 'success');
}

function startPresenceHeartbeat() {
  const ping = () => db.collection('presence').doc(adminUser.uid).set({
    state: 'online', last_active: firebase.firestore.FieldValue.serverTimestamp(),
  }, { merge: true }).catch(()=>{});
  ping();
  setInterval(ping, 20000);
  document.addEventListener('visibilitychange', () => { if (!document.hidden) ping(); });
}
</script>
