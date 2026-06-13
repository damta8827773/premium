<?php
$page_title   = "Tools OTP & Invite";
$current_page = "otp";
$base_path    = "";
require_once 'includes/head.php';
?>
<div class="flex h-screen overflow-hidden">
  <?php require_once 'includes/buyer-sidebar.php'; ?>
  <div class="flex-1 flex flex-col overflow-hidden">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-3 flex-shrink-0">
      <button class="lg:hidden text-gray-500" onclick="toggleSidebar()">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>
      <h1 class="text-xl font-bold text-gray-800">Tools OTP & Invite</h1>
    </header>
    <main class="flex-1 overflow-y-auto p-6 bg-gray-50">

      <!-- Tabs -->
      <div class="flex gap-3 mb-6">
        <button class="tab-btn active" onclick="showTab('otp',this)">OTP Sender</button>
        <button class="tab-btn" onclick="showTab('invite',this)">Invite Link</button>
      </div>

      <!-- OTP Tab -->
      <div id="tab-otp">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 max-w-md">
          <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <div>
              <h2 class="font-bold text-gray-800">OTP Tool</h2>
              <p class="text-xs text-gray-400">Request OTP untuk aktivasi akun</p>
            </div>
          </div>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Platform</label>
              <select id="otp-platform" class="input-field text-sm" onchange="updateOtpGuide()">
                <option value="">-- Pilih Platform --</option>
                <option value="canva">Canva</option>
                <option value="netflix">Netflix</option>
                <option value="spotify">Spotify</option>
                <option value="adobe">Adobe</option>
                <option value="youtube">YouTube Premium</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email Akun</label>
              <input type="email" id="otp-email" class="input-field text-sm" placeholder="email@example.com">
            </div>
            <div id="otp-guide" class="hidden bg-blue-50 rounded-xl p-4 text-sm text-blue-700">
              <p class="font-semibold mb-1">Panduan:</p>
              <p id="otp-guide-text"></p>
            </div>
            <button onclick="requestOtp()" id="btn-otp" class="btn-primary w-full text-sm py-2.5">
              Request OTP via Admin
            </button>
          </div>
        </div>

        <div class="mt-5 bg-yellow-50 border border-yellow-200 rounded-2xl p-5 max-w-md">
          <p class="text-sm font-semibold text-yellow-800 mb-2">Cara Kerja OTP:</p>
          <ol class="text-sm text-yellow-700 space-y-1 list-decimal ml-4">
            <li>Pilih platform dan masukkan email akun kamu</li>
            <li>Klik tombol "Request OTP via Admin"</li>
            <li>Admin akan mengirimkan kode OTP melalui WhatsApp</li>
            <li>Masukkan kode OTP di platform yang bersangkutan</li>
          </ol>
        </div>
      </div>

      <!-- Invite Tab -->
      <div id="tab-invite" class="hidden">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 max-w-md">
          <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            </div>
            <div>
              <h2 class="font-bold text-gray-800">Invite Link</h2>
              <p class="text-xs text-gray-400">Generate link undangan untuk akun sharing</p>
            </div>
          </div>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Platform</label>
              <select id="invite-platform" class="input-field text-sm">
                <option value="">-- Pilih Platform --</option>
                <option value="canva">Canva</option>
                <option value="netflix">Netflix</option>
                <option value="youtube">YouTube Premium</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email Kamu (untuk dikirimkan invite)</label>
              <input type="email" id="invite-email" class="input-field text-sm" placeholder="emailkamu@gmail.com">
            </div>
            <button onclick="requestInvite()" id="btn-invite" class="btn-primary w-full text-sm py-2.5">
              Request Invite via Admin
            </button>
          </div>
        </div>

        <div class="mt-5 bg-blue-50 border border-blue-200 rounded-2xl p-5 max-w-md">
          <p class="text-sm font-semibold text-blue-800 mb-2">Cara Kerja Invite:</p>
          <ol class="text-sm text-blue-700 space-y-1 list-decimal ml-4">
            <li>Pilih platform dan masukkan email kamu</li>
            <li>Klik "Request Invite via Admin"</li>
            <li>Admin akan mengirim link undangan ke email kamu</li>
            <li>Cek inbox/spam email dan klik link undangan</li>
          </ol>
        </div>
      </div>

    </main>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
<script>
const guides = {
  canva:   'Buka Canva → Akun → Login. Masukkan email akun, pilih "Kirim Email". Laporkan ke admin untuk forward OTP.',
  netflix: 'Buka Netflix → Masuk → Gunakan email. Pilih "Kirim kode ke email". Admin akan forwardkan kode.',
  spotify: 'Buka Spotify → Login → Lupa password atau verifikasi. Admin akan bantu kirimkan kode verifikasi.',
  adobe:   'Buka Adobe → Sign In → Masukkan email. Pilih kirim OTP ke email. Admin forward ke kamu via WA.',
  youtube: 'YouTube Premium terhubung akun Google. Hubungi admin untuk panduan verifikasi akun.',
};

auth.onAuthStateChanged(async user => {
  if (!user) { window.location.href = 'login.php'; return; }
});

function showTab(tab, btn) {
  document.getElementById('tab-otp').classList.toggle('hidden', tab !== 'otp');
  document.getElementById('tab-invite').classList.toggle('hidden', tab !== 'invite');
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}

function updateOtpGuide() {
  const val = document.getElementById('otp-platform').value;
  const box  = document.getElementById('otp-guide');
  const txt  = document.getElementById('otp-guide-text');
  if (val && guides[val]) {
    txt.textContent = guides[val];
    box.classList.remove('hidden');
  } else {
    box.classList.add('hidden');
  }
}

function requestOtp() {
  const platform = document.getElementById('otp-platform').value;
  const email    = document.getElementById('otp-email').value.trim();
  if (!platform) { showToast('Pilih platform terlebih dahulu', 'error'); return; }
  if (!email)    { showToast('Masukkan email akun', 'error'); return; }

  const user = auth.currentUser;
  const name = user?.displayName || 'Buyer';
  const msg  = encodeURIComponent(
    `Halo Admin, saya ${name} ingin request OTP untuk platform *${platform}* dengan email *${email}*.`
  );
  window.open(`https://wa.me/62XXXXXXXXXXX?text=${msg}`, '_blank');
}

function requestInvite() {
  const platform = document.getElementById('invite-platform').value;
  const email    = document.getElementById('invite-email').value.trim();
  if (!platform) { showToast('Pilih platform terlebih dahulu', 'error'); return; }
  if (!email)    { showToast('Masukkan email kamu', 'error'); return; }

  const user = auth.currentUser;
  const name = user?.displayName || 'Buyer';
  const msg  = encodeURIComponent(
    `Halo Admin, saya ${name} ingin request link invite untuk platform *${platform}*. Mohon kirim ke email: *${email}*.`
  );
  window.open(`https://wa.me/62XXXXXXXXXXX?text=${msg}`, '_blank');
}
</script>
