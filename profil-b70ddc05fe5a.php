<?php
$page_title = "Profil";
$current_page = "profil";
$base_path = "";
require_once 'backend/includes/head.php';
?>
<div class="flex h-screen overflow-hidden">
  <?php require_once 'backend/includes/buyer-sidebar.php'; ?>
  <div class="flex-1 flex flex-col overflow-hidden">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-3 flex-shrink-0">
      <button class="lg:hidden text-gray-500" onclick="toggleSidebar()"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
      <h1 class="text-xl font-bold text-gray-800">Profil</h1>
    </header>
    <main class="flex-1 overflow-y-auto p-6 bg-app">
      <div class="max-w-lg mx-auto">
        <div class="card-premium p-6 text-center mb-4">
          <div id="profil-avatar" class="w-20 h-20 rounded-full bg-gold flex items-center justify-center text-black font-bold text-2xl mx-auto mb-3 overflow-hidden ring-2 ring-gold/30">?</div>
          <h2 id="profil-name" class="font-bold text-lg text-gray-800">Memuat...</h2>
          <p id="profil-email" class="text-sm text-gray-400"></p>
          <span id="profil-role" class="inline-block mt-2 text-xs font-semibold px-3 py-1 rounded-full bg-primary/10 text-primary"></span>
        </div>

        <div class="card-premium p-5 mb-4">
          <h3 class="font-bold text-gray-800 text-sm mb-3">Info Akun</h3>
          <div class="space-y-3 text-sm">
            <div class="flex justify-between"><span class="text-gray-400">Saldo</span><span id="profil-balance" class="font-semibold text-gray-800">Rp 0</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Bergabung sejak</span><span id="profil-joined" class="font-semibold text-gray-800">-</span></div>
          </div>
        </div>

        <div class="card-premium p-5 mb-4">
          <h3 class="font-bold text-gray-800 text-sm mb-1">Program Referral</h3>
          <p class="text-xs text-gray-400 mb-3">Ajak teman daftar pakai link kamu - kalian berdua dapat bonus saldo Rp 5.000 begitu mereka selesai daftar.</p>
          <div class="flex gap-2 mb-2">
            <input type="text" id="referral-link" class="input-field text-xs" readonly>
            <button onclick="copyReferralLink()" class="btn-primary !px-4 text-xs flex-shrink-0">Salin</button>
          </div>
          <p id="referral-stats" class="text-xs text-gray-400"></p>
        </div>

        <form id="form-nama" class="card-premium p-5">
          <h3 class="font-bold text-gray-800 text-sm mb-3">Ubah Nama</h3>
          <input type="text" id="input-nama" class="input-field mb-3" placeholder="Nama kamu" maxlength="80" required>
          <button type="submit" class="btn-primary w-full">Simpan</button>
        </form>
      </div>
    </main>
  </div>
</div>
<?php require_once 'backend/includes/footer.php'; ?>
<script>
let profilUser = null;

auth.onAuthStateChanged(async user => {
  if (!user) { window.location.href = 'login.php'; return; }
  profilUser = user;
  try {
    const snap = await db.collection('users').doc(user.uid).get();
    const data = snap.data() || {};
    const displayName = data.name || user.displayName || user.email;

    document.getElementById('profil-name').textContent = displayName;
    document.getElementById('profil-email').textContent = data.email || user.email;
    document.getElementById('profil-role').textContent = data.is_reseller ? 'Reseller' : 'Buyer';
    document.getElementById('profil-balance').textContent = formatRupiah(data.balance || 0);
    document.getElementById('profil-joined').textContent = data.created_at ? formatDate(data.created_at) : '-';
    document.getElementById('input-nama').value = displayName;

    if (data.username) {
      document.getElementById('referral-link').value = window.location.origin + '/register.php?ref=' + data.username;
      db.collection('balance_history')
        .where('user_id', '==', user.uid)
        .where('type', '==', 'referral')
        .where('description', '==', 'Bonus referral - user baru mendaftar')
        .get()
        .then(snap => {
          const count = snap.size;
          const total = snap.docs.reduce((s, d) => s + (d.data().amount || 0), 0);
          document.getElementById('referral-stats').textContent = count > 0
            ? `${count} orang sudah daftar pakai link kamu · total bonus ${formatRupiah(total)}`
            : 'Belum ada yang pakai link referral kamu.';
        }).catch(() => {});
    }

    const avatarEl = document.getElementById('profil-avatar');
    avatarEl.textContent = displayName.charAt(0).toUpperCase();
    if (user.photoURL) {
      setAvatarImage(avatarEl, user.photoURL);
    } else {
      gravatarUrl(data.email || user.email).then(url => setAvatarImage(avatarEl, url)).catch(() => {});
    }
  } catch (e) {
    showToast('Gagal memuat profil: ' + e.message, 'error');
  }
});

function copyReferralLink() {
  const input = document.getElementById('referral-link');
  if (!input.value) return;
  navigator.clipboard.writeText(input.value).then(() => showToast('Link referral disalin!', 'success'));
}

document.getElementById('form-nama').addEventListener('submit', async e => {
  e.preventDefault();
  const name = document.getElementById('input-nama').value.trim();
  if (!name || !profilUser) return;
  try {
    await db.collection('users').doc(profilUser.uid).update({ name });
    showToast('Nama berhasil diperbarui.', 'success');
  } catch (err) {
    showToast('Gagal menyimpan nama: ' + err.message, 'error');
  }
});
</script>
