<?php
$page_title = "Live Chat";
$current_page = "kontak";
$base_path = "";
require_once 'backend/config/app.php';
require_once 'backend/includes/head.php';
?>
<style>
.chat-shell { display:flex; flex-direction:column; height:100%; max-width:760px; margin:0 auto; }
.bubble-row { display:flex; gap:8px; margin-bottom:14px; align-items:flex-end; }
.bubble-row.me { flex-direction:row-reverse; }
.bubble-avatar { width:30px; height:30px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:800; color:#fff; position:relative; }
.bubble-avatar.ai { background:linear-gradient(135deg,#7c3aed,#a855f7); }
.bubble-avatar.admin { background:#1B3528; }
.bubble-avatar.user { background:#EAB308; color:#000; }
.presence-dot { position:absolute; bottom:-1px; right:-1px; width:9px; height:9px; border-radius:50%; background:#22c55e; border:2px solid #fff; }
.presence-dot.pulse { animation: pulseDot 1.6s infinite; }
@keyframes pulseDot { 0%{box-shadow:0 0 0 0 rgba(34,197,94,0.5);} 70%{box-shadow:0 0 0 6px rgba(34,197,94,0);} 100%{box-shadow:0 0 0 0 rgba(34,197,94,0);} }
.bubble { max-width:75%; padding:10px 14px; border-radius:16px; font-size:13.5px; line-height:1.5; white-space:pre-wrap; word-break:break-word; }
.bubble-row.me .bubble { background:#1B3528; color:#fff; border-bottom-right-radius:4px; }
.bubble-row:not(.me) .bubble { background:#fff; color:#111827; border:1px solid #f0f0f0; border-bottom-left-radius:4px; }
.bubble-meta { font-size:10.5px; color:#9ca3af; margin-top:3px; display:flex; gap:4px; align-items:center; }
.bubble-row.me .bubble-meta { justify-content:flex-end; }
.ai-note { font-size:11px; color:#9ca3af; margin:2px 0 14px 38px; max-width:75%; font-style:italic; }
.queue-banner { background:linear-gradient(135deg,#fffbeb,#fef3c7); border:1px solid #fde68a; border-radius:14px; padding:12px 16px; margin-bottom:14px; font-size:13px; color:#92400e; display:flex; align-items:center; gap:10px; }
.typing-dots span { width:5px; height:5px; border-radius:50%; background:#9ca3af; display:inline-block; margin:0 1px; animation:typingBounce 1.2s infinite; }
.typing-dots span:nth-child(2){animation-delay:0.15s} .typing-dots span:nth-child(3){animation-delay:0.3s}
@keyframes typingBounce { 0%,60%,100%{transform:translateY(0);opacity:0.4} 30%{transform:translateY(-4px);opacity:1} }
</style>
<div class="flex h-screen overflow-hidden">
  <?php require_once 'backend/includes/buyer-sidebar.php'; ?>
  <div class="flex-1 flex flex-col overflow-hidden">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between gap-3 flex-shrink-0">
      <div class="flex items-center gap-3">
        <button class="lg:hidden text-gray-500" onclick="toggleSidebar()"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
        <div>
          <h1 class="text-xl font-bold text-gray-800">Live Chat</h1>
          <p id="chat-status-label" class="text-xs text-gray-400">Memuat...</p>
        </div>
      </div>
      <button onclick="requestHumanAdmin()" id="btn-human" class="text-xs font-semibold bg-primary text-white px-3 py-2 rounded-xl hover:bg-primary-light flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        Hubungi Admin Manusia
      </button>
    </header>
    <main class="flex-1 overflow-y-auto p-6 bg-app">
      <div class="chat-shell">
        <div id="queue-wrap"></div>
        <div id="messages" class="flex-1"></div>
        <div id="typing-indicator" style="display:none" class="bubble-row">
          <div class="bubble-avatar ai">AI</div>
          <div class="bubble"><span class="typing-dots"><span></span><span></span><span></span></span></div>
        </div>
      </div>
    </main>
    <div class="bg-white border-t border-gray-200 p-4 flex-shrink-0">
      <form id="send-form" class="max-w-[760px] mx-auto flex gap-2">
        <input type="text" id="msg-input" class="input-field" placeholder="Tulis pesan..." autocomplete="off" maxlength="2000">
        <button type="submit" id="btn-send" class="btn-primary !px-5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg></button>
      </form>
      <p class="text-center text-[11px] text-gray-300 mt-2">
        Butuh cepat? WA Admin:
        <a href="https://wa.me/<?= htmlspecialchars(ADMIN_WA_1) ?>" target="_blank" class="text-primary font-semibold">Admin 1</a> ·
        <a href="https://wa.me/<?= htmlspecialchars(ADMIN_WA_2) ?>" target="_blank" class="text-primary font-semibold">Admin 2</a>
      </p>
    </div>
  </div>
</div>
<?php require_once 'backend/includes/footer.php'; ?>
<script>
let currentUser = null, userProfile = {}, chatId = null, chatRef = null, chatData = {};
let messagesUnsub = null, chatUnsub = null, presenceUnsub = null;
let recentMessages = [];

auth.onAuthStateChanged(async user => {
  if (!user) { window.location.href = 'login.php'; return; }
  currentUser = user;
  const snap = await db.collection('users').doc(user.uid).get();
  userProfile = snap.data() || {};
  await initChat();
  startPresenceHeartbeat();
});

async function initChat() {
  const existing = await db.collection('chats')
    .where('user_id', '==', currentUser.uid)
    .get();
  let openChats = existing.docs.map(d => ({ id: d.id, ...d.data() })).filter(c => c.status !== 'closed');
  openChats.sort((a, b) => (b.created_at?.seconds || 0) - (a.created_at?.seconds || 0));

  let doc;
  if (openChats.length) {
    doc = openChats[0];
    chatId = doc.id;
  } else {
    const ref = db.collection('chats').doc();
    chatId = ref.id;
    const initial = {
      user_id: currentUser.uid,
      user_name: userProfile.name || currentUser.email,
      user_email: userProfile.email || currentUser.email,
      status: 'ai',
      assigned_admin_id: null,
      assigned_admin_name: null,
      last_message: '',
      last_message_at: firebase.firestore.FieldValue.serverTimestamp(),
      unread_admin: false,
      unread_user: false,
      created_at: firebase.firestore.FieldValue.serverTimestamp(),
      escalated_at: null,
      closed_at: null,
    };
    await ref.set(initial);
    doc = { id: chatId, ...initial };
  }

  chatRef = db.collection('chats').doc(chatId);
  chatData = doc;
  renderStatusLabel();
  listenChat();
  listenMessages();
}

function listenChat() {
  if (chatUnsub) chatUnsub();
  chatUnsub = chatRef.onSnapshot(async snap => {
    if (!snap.exists) return;
    chatData = { id: snap.id, ...snap.data() };
    renderStatusLabel();
    await renderQueueBanner();
    listenAdminPresence();
  });
}

function renderStatusLabel() {
  const label = document.getElementById('chat-status-label');
  const map = {
    ai: 'Dibantu AI Assistant',
    waiting_admin: 'Menunggu admin...',
    active_admin: chatData.assigned_admin_name ? 'Dibantu ' + chatData.assigned_admin_name : 'Dibantu admin',
    closed: 'Percakapan selesai',
  };
  label.textContent = map[chatData.status] || '';
}

async function renderQueueBanner() {
  const wrap = document.getElementById('queue-wrap');
  if (chatData.status !== 'waiting_admin') { wrap.innerHTML = ''; return; }
  const waiting = await db.collection('chats').where('status', '==', 'waiting_admin').get();
  const list = waiting.docs.map(d => ({ id: d.id, escalated_at: d.data().escalated_at }));
  list.sort((a, b) => (a.escalated_at?.seconds || 0) - (b.escalated_at?.seconds || 0));
  const position = Math.max(1, list.findIndex(c => c.id === chatId) + 1);
  const eta = position * 5;
  wrap.innerHTML = `<div class="queue-banner">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <div><strong>Antrian ke-${position}</strong> · estimasi tunggu ~${eta} menit. Admin akan segera membalas.</div>
  </div>`;
}

function listenAdminPresence() {
  if (presenceUnsub) presenceUnsub();
  if (chatData.status !== 'active_admin' || !chatData.assigned_admin_id) return;
  presenceUnsub = db.collection('presence').doc(chatData.assigned_admin_id).onSnapshot(snap => {
    const online = snap.exists && snap.data().state === 'online' && (Date.now()/1000 - (snap.data().last_active?.seconds || 0)) < 60;
    document.querySelectorAll('.admin-presence-dot').forEach(el => el.classList.toggle('pulse', online));
  });
}

function listenMessages() {
  if (messagesUnsub) messagesUnsub();
  messagesUnsub = chatRef.collection('messages').orderBy('created_at', 'asc').onSnapshot(snap => {
    recentMessages = snap.docs.map(d => ({ id: d.id, ...d.data() }));
    renderMessages();
    markReadByUser(snap.docs);
  });
}

function fmtTime(ts) {
  if (!ts) return '';
  const d = ts.toDate ? ts.toDate() : new Date(ts);
  return d.toLocaleDateString('id-ID', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' }) +
    ' · ' + d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
}

function renderMessages() {
  const wrap = document.getElementById('messages');
  wrap.innerHTML = recentMessages.map(m => {
    const isMe = m.sender_type === 'user';
    const avatarClass = m.sender_type === 'ai' ? 'ai' : (m.sender_type === 'admin' ? 'admin' : 'user');
    const avatarText = m.sender_type === 'ai' ? 'AI' : (m.sender_type === 'admin' ? (m.sender_name || 'A').charAt(0).toUpperCase() : (userProfile.name || 'U').charAt(0).toUpperCase());
    const ticks = isMe ? (m.read_by_admin ? '<span title="Dibaca admin" style="color:#3b82f6">✓✓</span>' : '<span title="Terkirim">✓</span>') : '';
    const dot = m.sender_type === 'admin' ? '<span class="presence-dot admin-presence-dot"></span>' : '';
    let html = `<div class="bubble-row ${isMe ? 'me' : ''}">
      <div class="bubble-avatar ${avatarClass}">${avatarText}${dot}</div>
      <div>
        <div class="bubble">${escapeHtml(m.text)}</div>
        <div class="bubble-meta">${fmtTime(m.created_at)} ${ticks}</div>
      </div>
    </div>`;
    if (m.sender_type === 'ai') {
      html += `<div class="ai-note">💬 Saya akan alihkan ke admin ketika kamu memang membutuhkan admin tersebut — atau klik "Hubungi Admin Manusia" kapan saja.</div>`;
    }
    return html;
  }).join('');
  wrap.scrollIntoView && document.querySelector('main').scrollTo(0, document.querySelector('main').scrollHeight);
}

function markReadByUser(docs) {
  docs.forEach(d => {
    const data = d.data();
    if (data.sender_type !== 'user' && !data.read_by_user) {
      d.ref.update({ read_by_user: true, read_by_user_at: firebase.firestore.FieldValue.serverTimestamp() }).catch(()=>{});
    }
  });
}

document.getElementById('send-form').addEventListener('submit', async e => {
  e.preventDefault();
  const input = document.getElementById('msg-input');
  const text = input.value.trim();
  if (!text || chatData.status === 'closed') return;
  input.value = '';
  await sendUserMessage(text);
});

async function sendUserMessage(text) {
  await chatRef.collection('messages').add({
    sender_type: 'user',
    sender_id: currentUser.uid,
    sender_name: userProfile.name || currentUser.email,
    text,
    created_at: firebase.firestore.FieldValue.serverTimestamp(),
    read_by_user: true,
    read_by_user_at: firebase.firestore.FieldValue.serverTimestamp(),
    read_by_admin: false,
    read_by_admin_at: null,
  });
  await chatRef.update({
    last_message: text,
    last_message_at: firebase.firestore.FieldValue.serverTimestamp(),
    unread_admin: true,
  });
  if (chatData.status === 'ai') await requestAiReply(text);
}

async function requestAiReply(userText) {
  document.getElementById('typing-indicator').style.display = 'flex';
  try {
    const idToken = await currentUser.getIdToken();
    const history = recentMessages.slice(-12).map(m => ({ role: m.sender_type, text: m.text }));
    const res = await fetch('backend/api/ai-chat-reply.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + idToken },
      body: JSON.stringify({ chat_id: chatId, message: userText, history }),
    });
    const data = await res.json();
    document.getElementById('typing-indicator').style.display = 'none';
    if (!res.ok) { showToast(data.error || 'AI sedang tidak tersedia.', 'error'); return; }

    await chatRef.collection('messages').add({
      sender_type: 'ai',
      sender_id: 'ai-assistant',
      sender_name: 'AI Assistant',
      text: data.reply,
      created_at: firebase.firestore.FieldValue.serverTimestamp(),
      read_by_user: true,
      read_by_user_at: firebase.firestore.FieldValue.serverTimestamp(),
      read_by_admin: false,
      read_by_admin_at: null,
    });
    const update = { last_message: data.reply, last_message_at: firebase.firestore.FieldValue.serverTimestamp() };
    if (data.handoff) {
      update.status = 'waiting_admin';
      update.escalated_at = firebase.firestore.FieldValue.serverTimestamp();
      update.unread_admin = true;
    }
    await chatRef.update(update);
  } catch (e) {
    document.getElementById('typing-indicator').style.display = 'none';
    showToast('Gagal menghubungi AI, coba lagi.', 'error');
  }
}

async function requestHumanAdmin() {
  if (chatData.status === 'waiting_admin' || chatData.status === 'active_admin') {
    showToast('Kamu sudah terhubung ke antrian/admin.', 'info');
    return;
  }
  await chatRef.collection('messages').add({
    sender_type: 'user',
    sender_id: currentUser.uid,
    sender_name: userProfile.name || currentUser.email,
    text: '[Meminta bantuan admin manusia]',
    created_at: firebase.firestore.FieldValue.serverTimestamp(),
    read_by_user: true,
    read_by_user_at: firebase.firestore.FieldValue.serverTimestamp(),
    read_by_admin: false,
    read_by_admin_at: null,
  });
  await chatRef.update({
    status: 'waiting_admin',
    escalated_at: firebase.firestore.FieldValue.serverTimestamp(),
    last_message: 'Meminta bantuan admin manusia',
    last_message_at: firebase.firestore.FieldValue.serverTimestamp(),
    unread_admin: true,
  });
  showToast('Kamu masuk antrian admin.', 'success');
}

// Lightweight Firestore-only presence heartbeat (not instant like Realtime DB onDisconnect,
// but good enough for "aktif X menit lalu").
function startPresenceHeartbeat() {
  const ping = () => db.collection('presence').doc(currentUser.uid).set({
    state: 'online',
    last_active: firebase.firestore.FieldValue.serverTimestamp(),
  }, { merge: true }).catch(()=>{});
  ping();
  setInterval(ping, 20000);
  document.addEventListener('visibilitychange', () => { if (!document.hidden) ping(); });
}
</script>
