// ===== TOAST NOTIFICATIONS =====
function showToast(message, type = 'success') {
  const toast = document.getElementById('toast');
  const inner = document.getElementById('toast-inner');
  if (!toast || !inner) return;
  const colors = { success: '#1B3528', error: '#DC2626', warning: '#D97706', info: '#2563EB' };
  const icons = {
    success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>',
    error:   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>',
    warning: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v4m0 4h.01M10.29 3.86l-8.4 14.55A1 1 0 002.75 20h18.5a1 1 0 00.87-1.59l-8.4-14.55a1 1 0 00-1.73 0z"/>',
    info:    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
  };
  inner.style.background = colors[type] || colors.success;
  inner.innerHTML = `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">${icons[type] || icons.success}</svg><span></span>`;
  inner.querySelector('span').textContent = message;
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

// ===== HTML ESCAPE (use before inserting any user-supplied text via innerHTML) =====
function escapeHtml(str) {
  if (str === null || str === undefined) return '';
  return String(str).replace(/[&<>"']/g, c => ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[c]));
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
  const labels = {
    'selesai': 'Selesai', 'pending': 'Pending', 'expired': 'Expired', 'batal': 'Batal',
    'success': 'Sukses', 'failed': 'Gagal', 'aktif': 'Aktif', 'menunggu': 'Menunggu',
    'diproses': 'Diproses', 'ditolak': 'Ditolak',
  };
  const label = labels[status] || status;
  return `<span class="status-pill" data-status="${status}">${label}</span>`;
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
