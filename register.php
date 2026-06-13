<?php $page_title = "Daftar — Premium App"; ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $page_title ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-firestore-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-storage-compat.js"></script>
<script src="assets/js/firebase-init.js"></script>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Plus Jakarta Sans', sans-serif; min-height: 100vh; display: flex; -webkit-font-smoothing: antialiased; }

.panel-left {
  width: 40%;
  min-height: 100vh;
  background: linear-gradient(160deg, #0f2219 0%, #1B3528 60%, #1e3d2f 100%);
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 40px 48px;
  position: relative;
  overflow: hidden;
  flex-shrink: 0;
}
.panel-left::before {
  content: '';
  position: absolute;
  width: 500px;
  height: 500px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(234,179,8,0.07), transparent 70%);
  top: -100px;
  right: -100px;
  pointer-events: none;
}
.panel-left::after {
  content: '';
  position: absolute;
  inset: 0;
  background-image: radial-gradient(rgba(255,255,255,0.04) 1px, transparent 1px);
  background-size: 28px 28px;
  pointer-events: none;
}
.panel-right {
  flex: 1;
  display: flex;
  align-items: flex-start;
  justify-content: center;
  padding: 40px 24px;
  background: #ffffff;
  overflow-y: auto;
}
.form-box { width: 100%; max-width: 420px; padding: 16px 0; }

.input-field {
  width: 100%;
  border: 1.5px solid #e5e7eb;
  border-radius: 12px;
  padding: 12px 16px;
  font-size: 14px;
  font-family: 'Plus Jakarta Sans', sans-serif;
  color: #111827;
  background: #fafafa;
  outline: none;
  transition: all 0.2s;
}
.input-field:focus { border-color: #1B3528; box-shadow: 0 0 0 3px rgba(27,53,40,0.08); background: #fff; }
.btn-primary-form {
  width: 100%;
  background: linear-gradient(135deg, #FCD34D, #EAB308);
  color: #000;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-weight: 800;
  font-size: 15px;
  padding: 14px;
  border-radius: 12px;
  border: none;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}
.btn-primary-form:hover { background: linear-gradient(135deg, #EAB308, #CA8A04); box-shadow: 0 6px 20px rgba(234,179,8,0.3); }
.btn-primary-form:disabled { opacity: 0.6; cursor: not-allowed; }
.label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
.grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

.toast {
  position: fixed;
  top: 20px;
  left: 50%;
  transform: translateX(-50%) translateY(-80px);
  z-index: 999;
  transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
  background: #DC2626;
  color: white;
  padding: 12px 24px;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  white-space: nowrap;
  box-shadow: 0 8px 30px rgba(220,38,38,0.3);
}
.toast.show { transform: translateX(-50%) translateY(0); }
.toast.success { background: #16a34a; box-shadow: 0 8px 30px rgba(22,163,74,0.3); }
.spinner { border: 2.5px solid rgba(0,0,0,0.2); border-top-color: #000; border-radius: 50%; width: 18px; height: 18px; animation: spin 0.7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.reseller-box {
  background: #f9fafb;
  border: 1.5px solid #e5e7eb;
  border-radius: 14px;
  padding: 16px;
}
.input-wrap { position: relative; }

@media (max-width: 768px) {
  .panel-left { display: none; }
  .panel-right { padding: 28px 18px; }
  .grid-2 { grid-template-columns: 1fr; }
}
</style>
</head>
<body>
<div id="toast" class="toast"></div>

<!-- LEFT PANEL -->
<div class="panel-left">
  <div style="position:relative;z-index:1">
    <a href="index.php" style="display:inline-flex;align-items:center;gap:10px;text-decoration:none;margin-bottom:48px">
      <div style="width:36px;height:36px;background:rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;border:1px solid rgba(255,255,255,0.2)">
        <span style="color:#fff;font-weight:900;font-size:15px">P</span>
      </div>
      <span style="color:#fff;font-weight:900;font-size:18px;letter-spacing:0.04em">PREMIUM</span>
    </a>

    <h2 style="font-size:clamp(1.5rem,2.2vw,2rem);font-weight:900;color:#fff;line-height:1.2;margin-bottom:10px">
      Bergabung sekarang,<br>
      <span style="background:linear-gradient(90deg,#FCD34D,#EAB308);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">gratis selamanya.</span>
    </h2>
    <p style="color:rgba(255,255,255,0.45);font-size:14px;line-height:1.7;margin-bottom:40px">
      Buat akun dalam 1 menit. Tidak ada biaya pendaftaran, tidak perlu kartu kredit.
    </p>

    <!-- App logos grid -->
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;max-width:260px">
      <?php
      $apps = [
        ['image/Canva_logo.png','Canva'],
        ['image/Netflix_logo.png','Netflix'],
        ['image/Spotify_logo.png','Spotify'],
        ['image/YouTube_logo.png','YouTube'],
        ['image/CapCut_logo.png','CapCut'],
        ['image/AlightMotion_logo.png','Alight'],
      ];
      foreach($apps as $app): ?>
      <div style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:12px;display:flex;flex-direction:column;align-items:center;gap:6px">
        <img src="<?= $app[0] ?>" alt="<?= $app[1] ?>" style="width:28px;height:28px;object-fit:contain" onerror="this.parentElement.style.display='none'">
        <span style="color:rgba(255,255,255,0.5);font-size:10px;font-weight:600"><?= $app[1] ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- RIGHT PANEL -->
<div class="panel-right">
  <div class="form-box">

    <div style="display:none;margin-bottom:28px" id="mobile-logo">
      <a href="index.php" style="display:inline-flex;align-items:center;gap:8px;text-decoration:none">
        <div style="width:32px;height:32px;background:#1B3528;border-radius:8px;display:flex;align-items:center;justify-content:center">
          <span style="color:#fff;font-weight:900;font-size:13px">P</span>
        </div>
        <span style="color:#1B3528;font-weight:900;font-size:17px">PREMIUM</span>
      </a>
    </div>

    <div style="margin-bottom:24px">
      <h1 style="font-size:1.6rem;font-weight:900;color:#111827;margin-bottom:4px">Buat akun baru</h1>
      <p style="color:#9ca3af;font-size:14px">Daftar gratis dan mulai belanja akun premium.</p>
    </div>

    <form id="register-form" style="display:flex;flex-direction:column;gap:14px">

      <div class="grid-2">
        <div>
          <label class="label">Nama Lengkap <span style="color:#ef4444">*</span></label>
          <input type="text" id="reg-name" class="input-field" placeholder="Nama kamu" required>
        </div>
        <div>
          <label class="label">Nomor Telepon <span style="color:#ef4444">*</span></label>
          <input type="tel" id="reg-phone" class="input-field" placeholder="08xx-xxxx-xxxx" required>
        </div>
      </div>

      <div>
        <label class="label">Email <span style="color:#ef4444">*</span></label>
        <input type="email" id="reg-email" class="input-field" placeholder="nama@email.com" required autocomplete="email">
      </div>

      <div>
        <label class="label">Username <span style="color:#ef4444">*</span></label>
        <div class="input-wrap">
          <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:14px;font-weight:500;pointer-events:none">@</span>
          <input type="text" id="reg-username" class="input-field" placeholder="usernamekamu" required style="padding-left:30px">
        </div>
        <p style="color:#9ca3af;font-size:11px;margin-top:4px">Huruf kecil, angka, dan underscore. Min. 4 karakter.</p>
      </div>

      <div class="grid-2">
        <div>
          <label class="label">Password <span style="color:#ef4444">*</span></label>
          <div class="input-wrap">
            <input type="password" id="reg-password" class="input-field" placeholder="Min. 8 karakter" required minlength="8" style="padding-right:40px">
            <button type="button" onclick="togglePass('reg-password')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;padding:2px">
              <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </button>
          </div>
        </div>
        <div>
          <label class="label">Konfirmasi Password <span style="color:#ef4444">*</span></label>
          <div class="input-wrap">
            <input type="password" id="reg-confirm" class="input-field" placeholder="Ulangi password" required style="padding-right:40px">
            <button type="button" onclick="togglePass('reg-confirm')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;padding:2px">
              <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Reseller token -->
      <div class="reseller-box">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">
          <span style="font-size:13px;font-weight:700;color:#374151">Token Reseller</span>
          <span style="font-size:11px;font-weight:600;color:#6b7280;background:#e5e7eb;padding:2px 8px;border-radius:6px">Opsional</span>
        </div>
        <p style="color:#9ca3af;font-size:12px;margin-bottom:10px;line-height:1.5">Punya token reseller? Masukkan untuk mendapatkan harga khusus.</p>
        <input type="text" id="reg-token" class="input-field" placeholder="KODE TOKEN RESELLER" style="font-size:13px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase" oninput="this.value=this.value.toUpperCase()">
        <a href="https://wa.me/62XXXXXXXXXXX" target="_blank" style="display:inline-flex;align-items:center;gap:6px;color:#16a34a;font-size:12px;font-weight:600;text-decoration:none;margin-top:8px">
          <svg style="width:13px;height:13px" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.787"/></svg>
          Hubungi CS untuk info reseller
        </a>
      </div>

      <button type="submit" id="btn-register" class="btn-primary-form" style="margin-top:4px">
        Buat Akun Gratis
      </button>

    </form>

    <p style="text-align:center;font-size:13px;color:#9ca3af;margin-top:18px">
      Sudah punya akun?
      <a href="login.php" style="color:#1B3528;font-weight:700;text-decoration:none">Masuk di sini</a>
    </p>

    <p style="text-align:center;font-size:12px;color:#d1d5db;margin-top:24px">
      <a href="index.php" style="color:#d1d5db;text-decoration:none;display:inline-flex;align-items:center;gap:4px">
        <svg style="width:12px;height:12px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke beranda
      </a>
    </p>
  </div>
</div>

<script>
if (window.innerWidth <= 768) document.getElementById('mobile-logo').style.display = 'block';

function showToast(msg, type) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className = 'toast' + (type === 'success' ? ' success' : '');
  t.offsetHeight;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3500);
}
function togglePass(id) {
  const i = document.getElementById(id);
  i.type = i.type === 'password' ? 'text' : 'password';
}

auth.onAuthStateChanged(user => { if (user) window.location.href = 'dashboard.php'; });

document.getElementById('register-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  const btn = document.getElementById('btn-register');
  const name     = document.getElementById('reg-name').value.trim();
  const phone    = document.getElementById('reg-phone').value.trim();
  const email    = document.getElementById('reg-email').value.trim();
  const username = document.getElementById('reg-username').value.trim().toLowerCase();
  const password = document.getElementById('reg-password').value;
  const confirm  = document.getElementById('reg-confirm').value;
  const token    = document.getElementById('reg-token').value.trim().toUpperCase();

  if (!name || !phone || !email || !username || !password) { showToast('Semua field wajib diisi.'); return; }
  if (password.length < 8) { showToast('Password minimal 8 karakter.'); return; }
  if (password !== confirm) { showToast('Konfirmasi password tidak cocok.'); return; }
  if (username.length < 4) { showToast('Username minimal 4 karakter.'); return; }
  if (!/^[a-z0-9_]+$/.test(username)) { showToast('Username hanya huruf kecil, angka, dan underscore.'); return; }

  btn.disabled = true;
  btn.innerHTML = '<span class="spinner"></span> Mendaftar...';

  try {
    const uCheck = await db.collection('users').where('username','==',username).get();
    if (!uCheck.empty) { showToast('Username sudah digunakan.'); btn.disabled=false; btn.textContent='Buat Akun Gratis'; return; }

    let is_reseller = false;
    if (token) {
      const tSnap = await db.collection('reseller_tokens').doc(token).get();
      if (!tSnap.exists || !tSnap.data().is_active) { showToast('Token reseller tidak valid.'); btn.disabled=false; btn.textContent='Buat Akun Gratis'; return; }
      is_reseller = true;
    }

    const cred = await auth.createUserWithEmailAndPassword(email, password);
    await cred.user.updateProfile({ displayName: name });
    await db.collection('users').doc(cred.user.uid).set({
      uid: cred.user.uid, name, phone, email, username,
      role: 'buyer', balance: 0, is_reseller,
      reseller_token: token || null,
      created_at: firebase.firestore.FieldValue.serverTimestamp()
    });

    showToast('Akun berhasil dibuat!', 'success');
    setTimeout(() => window.location.href = 'dashboard.php', 1200);
  } catch(err) {
    let msg = 'Pendaftaran gagal.';
    if (err.code === 'auth/email-already-in-use') msg = 'Email sudah terdaftar.';
    else if (err.code === 'auth/invalid-email') msg = 'Format email tidak valid.';
    else if (err.code === 'auth/weak-password') msg = 'Password terlalu lemah, min. 8 karakter.';
    showToast(msg);
    btn.disabled = false;
    btn.textContent = 'Buat Akun Gratis';
  }
});
</script>
</body>
</html>
