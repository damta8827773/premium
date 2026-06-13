<?php
$page_title = "Kontak Admin";
$current_page = "kontak";
$base_path = "";
require_once 'includes/head.php';
?>
<div class="flex h-screen overflow-hidden">
  <?php require_once 'includes/buyer-sidebar.php'; ?>
  <div class="flex-1 flex flex-col overflow-hidden">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-3 flex-shrink-0">
      <button class="lg:hidden text-gray-500" onclick="toggleSidebar()"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
      <div>
        <h1 class="text-xl font-bold text-gray-800">Kontak Admin</h1>
        <p class="text-xs text-gray-400">Hubungi admin untuk bantuan</p>
      </div>
    </header>
    <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
      <div class="max-w-xl mx-auto space-y-4">

        <!-- Admin 1 -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center justify-between">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-primary flex items-center justify-center text-white font-bold text-lg flex-shrink-0">AD</div>
            <div>
              <p class="font-bold text-gray-800">Admin Premium</p>
              <span class="text-xs bg-primary text-white font-semibold px-2 py-0.5 rounded mr-1">Admin</span>
              <span class="text-xs text-gray-400">Terima claim garansi</span>
            </div>
          </div>
          <a href="https://wa.me/62XXXXXXXXXXX" target="_blank" class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center hover:bg-green-600 transition-colors flex-shrink-0">
            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.787"/></svg>
          </a>
        </div>

        <!-- Admin 2 -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center justify-between">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-primary flex items-center justify-center text-white font-bold text-lg flex-shrink-0">CS</div>
            <div>
              <p class="font-bold text-gray-800">Customer Service</p>
              <span class="text-xs bg-primary text-white font-semibold px-2 py-0.5 rounded mr-1">Admin</span>
              <span class="text-xs text-gray-400">Online 24 Jam</span>
            </div>
          </div>
          <a href="https://wa.me/62XXXXXXXXXXX" target="_blank" class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center hover:bg-green-600 transition-colors flex-shrink-0">
            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.787"/></svg>
          </a>
        </div>

        <!-- Info -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-4 text-sm text-yellow-700">
          <p class="font-bold mb-1">⚠️ Penting!</p>
          <ul class="space-y-1 text-xs">
            <li>• Admin online 24 jam untuk semua pertanyaan</li>
            <li>• Untuk klaim garansi, gunakan menu <strong>Claim Garansi</strong> terlebih dahulu</li>
            <li>• Jangan bagikan password/data akun ke siapapun selain admin resmi di atas</li>
          </ul>
        </div>
      </div>
    </main>
  </div>
</div>
<?php require_once 'includes/footer.php'; ?>
<script>
auth.onAuthStateChanged(user => { if (!user) window.location.href = 'login.php'; });
</script>
