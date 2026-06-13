// ===== TOAST NOTIFICATIONS =====
function showToast(message, type = 'success') {
  const toast = document.getElementById('toast');
  const inner = document.getElementById('toast-inner');
  if (!toast || !inner) return;
  const colors = { success: '#1B3528', error: '#DC2626', warning: '#D97706', info: '#2563EB' };
  inner.style.background = colors[type] || colors.success;
  inner.textContent = message;
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 3500);
}

// ===== CURRENCY FORMATTER =====
function formatRupiah(amount) {
  return 'Rp ' + Number(amount).toLocaleString('id-ID');
}

// ===== DATE FORMATTER =====
function formatDate(timestamp) {
  if (!timestamp) return '-';
  const d = timestamp.toDate ? timestamp.toDate() : new Date(timestamp);
  return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
}
function formatDateTime(timestamp) {
  if (!timestamp) return '-';
  const d = timestamp.toDate ? timestamp.toDate() : new Date(timestamp);
  return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}
function timeAgo(timestamp) {
  if (!timestamp) return '';
  const d = timestamp.toDate ? timestamp.toDate() : new Date(timestamp);
  const diff = Math.floor((Date.now() - d.getTime()) / 1000);
  if (diff < 60) return 'Baru saja';
  if (diff < 3600) return Math.floor(diff/60) + ' menit lalu';
  if (diff < 86400) return Math.floor(diff/3600) + ' jam lalu';
  if (diff < 604800) return Math.floor(diff/86400) + ' hari lalu';
  if (diff < 2592000) return Math.floor(diff/604800) + ' minggu lalu';
  return Math.floor(diff/2592000) + ' bulan lalu';
}

// ===== GENERATE INVOICE =====
function generateInvoice() {
  const d = new Date();
  const pad = n => String(n).padStart(2, '0');
  return 'INV' + d.getFullYear() + pad(d.getMonth()+1) + pad(d.getDate()) + pad(d.getHours()) + pad(d.getMinutes()) + pad(d.getSeconds()) + Math.floor(Math.random()*1000);
}

// ===== AUTH =====
function doLogout() {
  if (!confirm('Yakin ingin keluar?')) return;
  auth.signOut().then(() => {
    window.location.href = '/login.php';
  });
}

// ===== LOADING BUTTON =====
function setLoading(btn, loading, text = null) {
  if (loading) {
    btn.disabled = true;
    btn.dataset.origText = btn.innerHTML;
    btn.innerHTML = '<span class="spinner"></span>';
  } else {
    btn.disabled = false;
    btn.innerHTML = text || btn.dataset.origText || 'Submit';
  }
}

// ===== STATUS BADGE =====
function statusBadge(status) {
  const map = {
    'selesai':   ['bg-green-100 text-green-700', 'Selesai'],
    'pending':   ['bg-yellow-100 text-yellow-700', 'Pending'],
    'expired':   ['bg-gray-100 text-gray-600', 'Expired'],
    'batal':     ['bg-red-100 text-red-600', 'Batal'],
    'success':   ['bg-green-100 text-green-700', 'Sukses'],
    'failed':    ['bg-red-100 text-red-600', 'Gagal'],
    'aktif':     ['bg-green-100 text-green-700', 'Aktif'],
    'menunggu':  ['bg-yellow-100 text-yellow-700', 'Menunggu'],
    'diproses':  ['bg-blue-100 text-blue-700', 'Diproses'],
    'ditolak':   ['bg-red-100 text-red-600', 'Ditolak'],
  };
  const [cls, label] = map[status] || ['bg-gray-100 text-gray-600', status];
  return `<span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-lg ${cls}">${label}</span>`;
}

// ===== UPDATE SIDEBAR USER INFO =====
auth.onAuthStateChanged(async user => {
  if (!user) return;
  const nameEl = document.getElementById('sidebar-name');
  const avatarEl = document.getElementById('sidebar-avatar');
  const roleEl = document.getElementById('sidebar-role');
  const adminNameEl = document.getElementById('admin-name');

  try {
    const snap = await db.collection('users').doc(user.uid).get();
    const data = snap.data() || {};
    const displayName = data.name || user.displayName || user.email;
    const initial = displayName.charAt(0).toUpperCase();

    if (nameEl) nameEl.textContent = displayName;
    if (adminNameEl) adminNameEl.textContent = displayName;
    if (avatarEl) { avatarEl.textContent = initial; }
    if (roleEl) roleEl.textContent = data.is_reseller ? 'Reseller' : 'Buyer';
  } catch(e) {
    if (nameEl && user.displayName) nameEl.textContent = user.displayName;
  }
});
