<?php $acp = $current_page ?? ''; ?>

<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden" onclick="toggleSidebar()"></div>

<aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-primary flex flex-col h-screen transition-transform -translate-x-full lg:translate-x-0 sidebar-scroll overflow-y-auto flex-shrink-0">

  <!-- Logo -->
  <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10">
    <div class="w-9 h-9 bg-white rounded-lg flex items-center justify-center flex-shrink-0">
      <span class="text-primary font-bold text-lg">P</span>
    </div>
    <div>
      <div class="text-white font-bold text-base leading-none">PREMIUM</div>
      <div class="text-yellow-400 text-xs mt-0.5 font-semibold">ADMIN PANEL</div>
    </div>
  </div>

  <nav class="flex-1 px-3 py-4 space-y-0.5">

    <p class="menu-section-label">Dashboard</p>

    <a href="<?= $base_path ?? '' ?>admin/index.php" class="<?= $acp==='admin-dashboard'?'bg-white/15 text-white':'text-white/70 hover:bg-white/10 hover:text-white' ?> flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="3" y="14" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="14" width="7" height="7" rx="1" stroke-width="2"/></svg>
      Dashboard
    </a>

    <p class="menu-section-label mt-3">Manajemen</p>

    <a href="<?= $base_path ?? '' ?>admin/produk.php" class="<?= $acp==='admin-produk'?'bg-white/15 text-white':'text-white/70 hover:bg-white/10 hover:text-white' ?> flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
      Produk
    </a>

    <a href="<?= $base_path ?? '' ?>admin/stok.php" class="<?= $acp==='admin-stok'?'bg-white/15 text-white':'text-white/70 hover:bg-white/10 hover:text-white' ?> flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
      Kelola Stok
    </a>

    <a href="<?= $base_path ?? '' ?>admin/pesanan.php" class="<?= $acp==='admin-pesanan'?'bg-white/15 text-white':'text-white/70 hover:bg-white/10 hover:text-white' ?> flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
      Pesanan
    </a>

    <p class="menu-section-label mt-3">Keuangan</p>

    <a href="<?= $base_path ?? '' ?>admin/pembayaran.php" class="<?= $acp==='admin-pembayaran'?'bg-white/15 text-white':'text-white/70 hover:bg-white/10 hover:text-white' ?> flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
      Deposit & Pembayaran
      <span id="pending-badge" class="hidden ml-auto bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center"></span>
    </a>

    <a href="<?= $base_path ?? '' ?>admin/voucher.php" class="<?= $acp==='admin-voucher'?'bg-white/15 text-white':'text-white/70 hover:bg-white/10 hover:text-white' ?> flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
      Voucher
    </a>

    <a href="<?= $base_path ?? '' ?>admin/pengguna.php" class="<?= $acp==='admin-pengguna'?'bg-white/15 text-white':'text-white/70 hover:bg-white/10 hover:text-white' ?> flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      Pengguna
    </a>

    <p class="menu-section-label mt-3">Layanan</p>

    <a href="<?= $base_path ?? '' ?>admin/garansi.php" class="<?= $acp==='admin-garansi'?'bg-white/15 text-white':'text-white/70 hover:bg-white/10 hover:text-white' ?> flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
      Garansi & Klaim
    </a>

    <a href="<?= $base_path ?? '' ?>admin/pengumuman.php" class="<?= $acp==='admin-pengumuman'?'bg-white/15 text-white':'text-white/70 hover:bg-white/10 hover:text-white' ?> flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
      Pengumuman
    </a>

    <div class="mt-4 mx-2">
      <a href="<?= $base_path ?? '' ?>dashboard.php" class="flex items-center gap-2 text-white/50 hover:text-white text-xs transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali ke Panel Buyer
      </a>
    </div>

  </nav>

  <!-- Admin Profile -->
  <div class="border-t border-white/10 px-4 py-4 flex items-center gap-3">
    <div class="w-9 h-9 rounded-full bg-yellow-400 flex items-center justify-center text-black font-bold text-sm flex-shrink-0">A</div>
    <div class="flex-1 min-w-0">
      <div id="admin-name" class="text-white text-sm font-semibold truncate">Admin</div>
      <div class="text-yellow-400 text-xs font-medium">Administrator</div>
    </div>
    <button onclick="doLogout()" title="Logout" class="text-white/50 hover:text-red-400 transition-colors">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
    </button>
  </div>
</aside>

<script>
function toggleSidebar() {
  const s = document.getElementById('sidebar');
  const o = document.getElementById('sidebar-overlay');
  s.classList.toggle('-translate-x-full');
  o.classList.toggle('hidden');
}
</script>
