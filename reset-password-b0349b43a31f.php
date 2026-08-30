<?php $page_title = "Atur Ulang Password - Premium App"; require_once 'backend/includes/security-headers.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
<base href="/">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $page_title ?></title>
<link rel="icon" href="/frontend/image/logo.svg" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-auth-compat.js"></script>
<script src="frontend/assets/js/firebase-init.js"></script>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Plus Jakarta Sans', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(160deg, #0f2219 0%, #1B3528 60%, #1e3d2f 100%); -webkit-font-smoothing: antialiased; padding: 24px; }
.box { width: 100%; max-width: 420px; background: #fff; border-radius: 20px; padding: 36px 32px; box-shadow: 0 24px 60px rgba(0,0,0,0.35); }
.logo { display: flex; align-items: center; gap: 10px; margin-bottom: 28px; }
.logo-mark { width: 32px; height: 32px; background: #1B3528; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
.logo-mark span { color: #fff; font-weight: 900; font-size: 13px; }
.logo-text { color: #1B3528; font-weight: 900; font-size: 17px; }
h1 { font-size: 1.4rem; font-weight: 900; color: #111827; margin-bottom: 6px; }
.sub { color: #9ca3af; font-size: 13.5px; margin-bottom: 24px; line-height: 1.5; }
.sub strong { color: #374151; }
.input-field { width: 100%; border: 1.5px solid #e5e7eb; border-radius: 12px; padding: 13px 16px; font-size: 14px; font-family: 'Plus Jakarta Sans', sans-serif; color: #111827; background: #fafafa; outline: none; transition: all 0.2s; margin-bottom: 14px; }
.input-field:focus { border-color: #1B3528; box-shadow: 0 0 0 3px rgba(27,53,40,0.08); background: #fff; }
.btn { width: 100%; background: #1B3528; color: #fff; font-weight: 700; font-size: 15px; padding: 14px; border-radius: 12px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s; }
.btn:hover { background: #2d5a42; }
.btn:disabled { opacity: 0.6; cursor: not-allowed; }
.spinner { border: 2.5px solid rgba(255,255,255,0.3); border-top-color: #fff; border-radius: 50%; width: 18px; height: 18px; animation: spin 0.7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.state-icon { width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 18px; }
.state-icon.error { background: #fef2f2; color: #DC2626; }
.state-icon.success { background: #ecfdf5; color: #16a34a; }
.center { text-align: center; }
.link-back { display: inline-block; margin-top: 18px; color: #1B3528; font-weight: 700; font-size: 13.5px; text-decoration: none; }
.error-text { color: #DC2626; font-size: 13px; margin-top: -6px; margin-bottom: 14px; display: none; }
</style>
</head>
<body>
<div class="box">
  <div class="logo">
    <img src="/frontend/image/logo.svg" alt="Premium Store" class="logo-mark">
    <span class="logo-text">PREMIUM</span>
  </div>

  <div id="state-loading" class="center" style="padding:20px 0">
    <div class="spinner" style="border-top-color:#1B3528;border-color:rgba(27,53,40,0.15);border-top-color:#1B3528;margin:0 auto"></div>
    <p style="margin-top:12px;color:#9ca3af;font-size:13.5px">Memeriksa link reset...</p>
  </div>

  <div id="state-invalid" class="center" style="display:none">
    <div class="state-icon error">
      <svg style="width:26px;height:26px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </div>
    <h1>Link Tidak Valid</h1>
    <p class="sub">Link reset password ini sudah kedaluwarsa atau sudah pernah dipakai. Minta link baru dari halaman login.</p>
    <a href="login.php" class="link-back">← Kembali ke halaman login</a>
  </div>

  <div id="state-form" style="display:none">
    <h1>Atur Password Baru</h1>
    <p class="sub">Untuk akun <strong id="target-email">-</strong></p>
    <form id="reset-form">
      <input type="password" id="new-password" class="input-field" placeholder="Password baru (min. 6 karakter)" required minlength="6" autocomplete="new-password">
      <input type="password" id="confirm-password" class="input-field" placeholder="Ulangi password baru" required minlength="6" autocomplete="new-password">
      <p id="error-text" class="error-text"></p>
      <button type="submit" id="btn-submit" class="btn">Simpan Password Baru</button>
    </form>
  </div>

  <div id="state-success" class="center" style="display:none">
    <div class="state-icon success">
      <svg style="width:26px;height:26px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    </div>
    <h1>Password Berhasil Diubah</h1>
    <p class="sub">Password kamu sudah diperbarui. Silakan login dengan password baru.</p>
    <a href="login.php" class="link-back">Masuk sekarang →</a>
  </div>
</div>

<script>
const params = new URLSearchParams(window.location.search);
const mode = params.get('mode');
const oobCode = params.get('oobCode');

function showState(id) {
  ['state-loading','state-invalid','state-form','state-success'].forEach(s => {
    document.getElementById(s).style.display = (s === id) ? 'block' : 'none';
  });
}

(async function init() {
  if (mode !== 'resetPassword' || !oobCode) { showState('state-invalid'); return; }
  try {
    const email = await auth.verifyPasswordResetCode(oobCode);
    document.getElementById('target-email').textContent = email;
    showState('state-form');
  } catch (err) {
    showState('state-invalid');
  }
})();

document.getElementById('reset-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  const pass = document.getElementById('new-password').value;
  const confirm = document.getElementById('confirm-password').value;
  const errEl = document.getElementById('error-text');
  errEl.style.display = 'none';

  if (pass.length < 6) { errEl.textContent = 'Password minimal 6 karakter.'; errEl.style.display = 'block'; return; }
  if (pass !== confirm) { errEl.textContent = 'Konfirmasi password tidak cocok.'; errEl.style.display = 'block'; return; }

  const btn = document.getElementById('btn-submit');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner"></span>';
  try {
    await auth.confirmPasswordReset(oobCode, pass);
    showState('state-success');
  } catch (err) {
    errEl.textContent = 'Gagal menyimpan password. Link mungkin sudah kedaluwarsa.';
    errEl.style.display = 'block';
    btn.disabled = false;
    btn.textContent = 'Simpan Password Baru';
  }
});
</script>
</body>
</html>
