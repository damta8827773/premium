<?php $page_title = "Masuk - Premium App"; ?>
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
<script src="frontend/assets/js/firebase-init.js"></script>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Plus Jakarta Sans', sans-serif; min-height: 100vh; display: flex; -webkit-font-smoothing: antialiased; }

/* Left panel */
.panel-left {
  width: 45%;
  min-height: 100vh;
  background: linear-gradient(160deg, #0f2219 0%, #1B3528 60%, #1e3d2f 100%);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
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

/* Right panel */
.panel-right {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 24px;
  background: #ffffff;
  overflow-y: auto;
}
.form-box { width: 100%; max-width: 400px; }

/* Form elements */
.input-wrap { position: relative; }
.input-field {
  width: 100%;
  border: 1.5px solid #e5e7eb;
  border-radius: 12px;
  padding: 13px 16px;
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
  background: #1B3528;
  color: #fff;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-weight: 700;
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
.btn-primary-form:hover { background: #2d5a42; }
.btn-primary-form:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-google {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  border: 1.5px solid #e5e7eb;
  border-radius: 12px;
  padding: 13px;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 14px;
  font-weight: 600;
  color: #374151;
  background: #fff;
  cursor: pointer;
  transition: all 0.18s;
}
.btn-google:hover { background: #f9fafb; border-color: #d1d5db; }
.btn-google:disabled { opacity: 0.6; cursor: not-allowed; }
.divider { display: flex; align-items: center; gap: 12px; margin: 20px 0; }
.divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #e5e7eb; }
.divider span { color: #9ca3af; font-size: 12px; font-weight: 500; }

/* Toast */
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

/* Spinner */
.spinner { border: 2.5px solid rgba(255,255,255,0.3); border-top-color: #fff; border-radius: 50%; width: 18px; height: 18px; animation: spin 0.7s linear infinite; }
.spinner-dark { border: 2.5px solid rgba(0,0,0,0.15); border-top-color: #1B3528; border-radius: 50%; width: 18px; height: 18px; animation: spin 0.7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* Feature bullets on left panel */
.feature-item { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 18px; }
.feature-icon { width: 36px; height: 36px; border-radius: 10px; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 16px; }
.feature-text-title { font-weight: 700; font-size: 14px; color: #fff; margin-bottom: 2px; }
.feature-text-sub { font-size: 12px; color: rgba(255,255,255,0.45); line-height: 1.4; }

/* Mobile: stack vertically */
@media (max-width: 768px) {
  .panel-left { display: none; }
  .panel-right { padding: 32px 20px; min-height: 100vh; }
}
</style>
</head>
<body>
<div id="toast" class="toast"></div>

<!-- LEFT PANEL -->
<div class="panel-left">
  <div style="position:relative;z-index:1">
    <!-- Logo -->
    <a href="index.php" style="display:inline-flex;align-items:center;gap:10px;text-decoration:none;margin-bottom:56px">
      <div style="width:36px;height:36px;background:rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;border:1px solid rgba(255,255,255,0.2)">
        <span style="color:#fff;font-weight:900;font-size:15px">P</span>
      </div>
      <span style="color:#fff;font-weight:900;font-size:18px;letter-spacing:0.04em">PREMIUM</span>
    </a>

    <!-- Headline -->
    <h2 style="font-size:clamp(1.6rem,2.5vw,2.2rem);font-weight:900;color:#fff;line-height:1.2;margin-bottom:12px">
      Satu akun,<br>
      <span style="background:linear-gradient(90deg,#FCD34D,#EAB308);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">akses semua.</span>
    </h2>
    <p style="color:rgba(255,255,255,0.45);font-size:14px;line-height:1.6;margin-bottom:40px">
      Masuk dan nikmati ratusan produk premium dengan harga terbaik.
    </p>

    <!-- Features -->
    <div>
      <div class="feature-item">
        <div class="feature-icon">🔐</div>
        <div>
          <div class="feature-text-title">Garansi Penuh</div>
          <div class="feature-text-sub">Akun bermasalah langsung diganti tanpa pertanyaan</div>
        </div>
      </div>
      <div class="feature-item">
        <div class="feature-icon">⚡</div>
        <div>
          <div class="feature-text-title">Pengiriman Instan</div>
          <div class="feature-text-sub">Kredensial akun tersedia seketika setelah bayar</div>
        </div>
      </div>
      <div class="feature-item">
        <div class="feature-icon">💳</div>
        <div>
          <div class="feature-text-title">Metode Bayar Lengkap</div>
          <div class="feature-text-sub">QRIS, transfer bank, dan berbagai opsi lokal</div>
        </div>
      </div>
      <div class="feature-item">
        <div class="feature-icon">🛟</div>
        <div>
          <div class="feature-text-title">Support 24 Jam</div>
          <div class="feature-text-sub">Tim admin siap via WhatsApp kapanpun</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bottom testimonial -->
  <div style="position:relative;z-index:1;background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.1);border-radius:16px;padding:20px">
    <p style="color:rgba(255,255,255,0.7);font-size:13px;line-height:1.6;margin-bottom:12px">
      "Udah langganan 6 bulan, gak pernah ada masalah. Admin responsif banget, garansi beneran diproses."
    </p>
    <div style="display:flex;align-items:center;gap:10px">
      <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#EAB308,#CA8A04);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;color:#000">R</div>
      <div>
        <div style="color:#fff;font-size:13px;font-weight:600">Rizky A.</div>
        <div style="color:rgba(255,255,255,0.35);font-size:11px">Pelanggan sejak 2023</div>
      </div>
    </div>
  </div>
</div>

<!-- RIGHT PANEL -->
<div class="panel-right">
  <div class="form-box">

    <!-- Mobile logo -->
    <div style="display:none;margin-bottom:32px" id="mobile-logo">
      <a href="index.php" style="display:inline-flex;align-items:center;gap:8px;text-decoration:none">
        <div style="width:32px;height:32px;background:#1B3528;border-radius:8px;display:flex;align-items:center;justify-content:center">
          <span style="color:#fff;font-weight:900;font-size:13px">P</span>
        </div>
        <span style="color:#1B3528;font-weight:900;font-size:17px">PREMIUM</span>
      </a>
    </div>

    <!-- Heading -->
    <div style="margin-bottom:28px">
      <h1 style="font-size:1.75rem;font-weight:900;color:#111827;margin-bottom:6px">Selamat datang</h1>
      <p style="color:#9ca3af;font-size:14px">Masuk ke akun Premium kamu.</p>
    </div>

    <!-- Google login -->
    <button onclick="loginWithGoogle()" id="btn-google" class="btn-google" style="margin-bottom:4px">
      <img src="frontend/image/Google.png" alt="Google" style="width:20px;height:20px;object-fit:contain">
      Masuk dengan Google
    </button>

    <div class="divider"><span>atau masuk dengan email</span></div>

    <!-- Email/Password form -->
    <form id="login-form" style="display:flex;flex-direction:column;gap:16px">
      <div>
        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Email</label>
        <input type="email" id="login-email" class="input-field" placeholder="nama@email.com" required autocomplete="email">
      </div>
      <div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
          <label style="font-size:13px;font-weight:600;color:#374151">Password</label>
        </div>
        <div class="input-wrap">
          <input type="password" id="login-password" class="input-field" placeholder="Masukkan password" required autocomplete="current-password" style="padding-right:44px">
          <button type="button" onclick="togglePass()" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;padding:4px;display:flex;align-items:center" id="eye-btn">
            <svg id="eye-icon" class="w-5 h-5" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
          </button>
        </div>
      </div>

      <button type="submit" id="btn-login" class="btn-primary-form" style="margin-top:4px">
        Masuk
      </button>
    </form>

    <p style="text-align:center;font-size:13px;color:#9ca3af;margin-top:20px">
      Belum punya akun?
      <a href="register.php" style="color:#1B3528;font-weight:700;text-decoration:none">Daftar gratis</a>
    </p>

    <p style="text-align:center;font-size:12px;color:#d1d5db;margin-top:32px">
      <a href="index.php" style="color:#d1d5db;text-decoration:none;display:inline-flex;align-items:center;gap:4px">
        <svg style="width:12px;height:12px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke beranda
      </a>
    </p>
  </div>
</div>

<script>
// Show mobile logo on small screens
if (window.innerWidth <= 768) document.getElementById('mobile-logo').style.display = 'block';

function showToast(msg, type) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className = 'toast' + (type === 'success' ? ' success' : '');
  t.offsetHeight; // reflow
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3500);
}

let passVisible = false;
function togglePass() {
  passVisible = !passVisible;
  const inp = document.getElementById('login-password');
  inp.type = passVisible ? 'text' : 'password';
}

auth.onAuthStateChanged(async user => {
  if (!user) return;
  try {
    const s = await db.collection('users').doc(user.uid).get();
    window.location.href = (s.exists && s.data().role === 'admin') ? 'admin/index.php' : 'dashboard.php';
  } catch(e) { window.location.href = 'dashboard.php'; }
});

document.getElementById('login-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  const btn = document.getElementById('btn-login');
  const email = document.getElementById('login-email').value.trim();
  const password = document.getElementById('login-password').value;
  if (!email || !password) { showToast('Email dan password wajib diisi.'); return; }
  if (password.length < 6) { showToast('Password minimal 6 karakter.'); return; }
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner"></span>';
  try {
    const cred = await auth.signInWithEmailAndPassword(email, password);
    const s = await db.collection('users').doc(cred.user.uid).get();
    window.location.href = (s.exists && s.data().role === 'admin') ? 'admin/index.php' : 'dashboard.php';
  } catch(err) {
    let msg = 'Login gagal. Coba lagi.';
    if (['auth/user-not-found','auth/wrong-password','auth/invalid-credential'].includes(err.code)) msg = 'Email atau password salah.';
    else if (err.code === 'auth/too-many-requests') msg = 'Terlalu banyak percobaan. Coba lagi nanti.';
    else if (err.code === 'auth/invalid-email') msg = 'Format email tidak valid.';
    showToast(msg);
    btn.disabled = false;
    btn.textContent = 'Masuk';
  }
});

async function loginWithGoogle() {
  const btn = document.getElementById('btn-google');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-dark"></span> Memproses...';
  try {
    const result = await auth.signInWithPopup(googleProvider);
    const user = result.user;
    const snap = await db.collection('users').doc(user.uid).get();
    if (!snap.exists) {
      await db.collection('users').doc(user.uid).set({
        uid: user.uid, name: user.displayName||'', email: user.email||'',
        phone: '', username: (user.email||'').split('@')[0],
        role: 'buyer', balance: 0, is_reseller: false,
        created_at: firebase.firestore.FieldValue.serverTimestamp()
      });
    }
    const s = await db.collection('users').doc(user.uid).get();
    window.location.href = (s.exists && s.data().role === 'admin') ? 'admin/index.php' : 'dashboard.php';
  } catch(err) {
    if (err.code !== 'auth/popup-closed-by-user') showToast('Login Google gagal. Coba lagi.');
    btn.disabled = false;
    btn.innerHTML = '<img src="frontend/image/Google.png" style="width:20px;height:20px;object-fit:contain"> Masuk dengan Google';
  }
}
</script>
</body>
</html>
