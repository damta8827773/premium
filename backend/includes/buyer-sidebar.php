<?php
// $current_page must be set by the calling page
$cp = $current_page ?? '';
?>
<!-- Sidebar Overlay (mobile) -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-primary flex flex-col h-screen transition-transform -translate-x-full lg:translate-x-0 sidebar-scroll overflow-y-auto flex-shrink-0">

  <!-- Logo -->
  <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10">
    <div class="w-9 h-9 bg-white rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden">
      <img src="<?= $base_path ?? '' ?>frontend/image/AlightMotion_logo.png" alt="Logo" class="w-6 h-6 object-contain" onerror="this.style.display='none';this.parentElement.innerHTML='<span class=\'text-primary font-bold text-sm\'>P</span>'">
    </div>
    <div>
      <div class="text-white font-bold text-base leading-none">PREMIUM</div>
      <div class="text-white/50 text-xs mt-0.5">BUYER PANEL</div>
    </div>
  </div>

  <!-- Navigation -->
  <nav class="flex-1 px-3 py-4 space-y-0.5">

    <!-- MENU UTAMA -->
    <p class="menu-section-label">Menu Utama</p>

    <a href="<?= $base_path ?? '' ?>dashboard.php" class="<?= $cp==='dashboard'?'bg-white/15 text-white':'text-white/70 hover:bg-white/10 hover:text-white' ?> flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all">
      <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="3" y="14" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="14" width="7" height="7" rx="1" stroke-width="2"/></svg>
      Dashboard
    </a>

    <div>
      <button onclick="toggleMenu('menu-tools')" class="<?= in_array($cp,['otp'])? 'bg-white/15 text-white':'text-white/70 hover:bg-white/10 hover:text-white' ?> w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3" stroke-width="2"/></svg>
        Tools OTP & Invite
        <svg id="arrow-tools" class="w-3.5 h-3.5 ml-auto transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      </button>
      <div id="menu-tools" class="submenu pl-4">
        <a href="<?= $base_path ?? '' ?>tools-otp.php" class="<?= $cp==='otp'?'text-white':'text-white/60 hover:text-white' ?> flex items-center gap-2 px-3 py-2 text-sm transition-all">
          <span class="w-1 h-1 bg-current rounded-full"></span> OTP Tool
        </a>
      </div>
    </div>

    <!-- BELANJA -->
    <p class="menu-section-label mt-3">Belanja</p>

    <div>
      <button onclick="toggleMenu('menu-katalog')" class="<?= in_array($cp,['toko','stok'])? 'bg-white/15 text-white':'text-white/70 hover:bg-white/10 hover:text-white' ?> w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        Katalog
        <svg id="arrow-katalog" class="w-3.5 h-3.5 ml-auto transition-transform <?= in_array($cp,['toko','stok'])?'rotate-90':'' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      </button>
      <div id="menu-katalog" class="submenu <?= in_array($cp,['toko','stok'])?'open':'' ?> pl-4">
        <a href="<?= $base_path ?? '' ?>toko.php" class="<?= $cp==='toko'?'text-white font-semibold':'text-white/60 hover:text-white' ?> flex items-center gap-2 px-3 py-2 text-sm transition-all">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
          Toko
        </a>
        <a href="<?= $base_path ?? '' ?>cek-stok.php" class="<?= $cp==='stok'?'text-white font-semibold':'text-white/60 hover:text-white' ?> flex items-center gap-2 px-3 py-2 text-sm transition-all">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
          Cek Stock
        </a>
      </div>
    </div>

    <a href="<?= $base_path ?? '' ?>pesanan.php" class="<?= $cp==='pesanan'?'bg-white/15 text-white':'text-white/70 hover:bg-white/10 hover:text-white' ?> flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all">
      <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
      Riwayat Pesanan
    </a>

    <!-- KEUANGAN -->
    <p class="menu-section-label mt-3">Keuangan</p>

    <a href="<?= $base_path ?? '' ?>deposit.php" class="<?= $cp==='deposit'?'bg-white/15 text-white':'text-white/70 hover:bg-white/10 hover:text-white' ?> flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all">
      <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      Deposit Saldo
    </a>

    <a href="<?= $base_path ?? '' ?>riwayat-saldo.php" class="<?= $cp==='riwayat-saldo'?'bg-white/15 text-white':'text-white/70 hover:bg-white/10 hover:text-white' ?> flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all">
      <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
      Riwayat Saldo
    </a>

    <a href="<?= $base_path ?? '' ?>redeem.php" class="<?= $cp==='redeem'?'bg-white/15 text-white':'text-white/70 hover:bg-white/10 hover:text-white' ?> flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all">
      <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
      Redeem Voucher
    </a>

    <!-- LAYANAN -->
    <p class="menu-section-label mt-3">Layanan</p>

    <a href="<?= $base_path ?? '' ?>garansi.php" class="<?= $cp==='garansi'?'bg-white/15 text-white':'text-white/70 hover:bg-white/10 hover:text-white' ?> flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all">
      <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
      Aktivasi Garansi
    </a>

    <a href="<?= $base_path ?? '' ?>klaim-garansi.php" class="<?= $cp==='klaim'?'bg-white/15 text-white':'text-white/70 hover:bg-white/10 hover:text-white' ?> flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all">
      <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
      Claim Garansi
    </a>

    <!-- LAINNYA -->
    <p class="menu-section-label mt-3">Lainnya</p>

    <a href="<?= $base_path ?? '' ?>pengumuman.php" class="<?= $cp==='pengumuman'?'bg-white/15 text-white':'text-white/70 hover:bg-white/10 hover:text-white' ?> flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all">
      <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
      Pengumuman
    </a>

    <a href="<?= $base_path ?? '' ?>kontak-admin.php" class="<?= $cp==='kontak'?'bg-white/15 text-white':'text-white/70 hover:bg-white/10 hover:text-white' ?> flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all">
      <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
      Kontak Admin
    </a>

  </nav>

  <!-- User Profile -->
  <div class="border-t border-white/10 px-4 py-4 flex items-center gap-3">
    <div id="sidebar-avatar" class="w-9 h-9 rounded-full bg-gold flex items-center justify-center text-black font-bold text-sm flex-shrink-0">?</div>
    <div class="flex-1 min-w-0">
      <div id="sidebar-name" class="text-white text-sm font-semibold truncate">Loading...</div>
      <div id="sidebar-role" class="text-white/50 text-xs">Buyer</div>
    </div>
    <button onclick="doLogout()" title="Logout" class="text-white/50 hover:text-red-400 transition-colors">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
    </button>
  </div>
</aside>

<script>
function toggleMenu(id) {
  const el = document.getElementById(id);
  const arrow = document.getElementById('arrow-' + id.replace('menu-',''));
  el.classList.toggle('open');
  if (arrow) arrow.classList.toggle('rotate-90');
}
function toggleSidebar() {
  const s = document.getElementById('sidebar');
  const o = document.getElementById('sidebar-overlay');
  s.classList.toggle('-translate-x-full');
  o.classList.toggle('hidden');
}
</script>
