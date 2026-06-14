<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title ?? 'Premium App') ?></title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: { DEFAULT: '#1B3528', light: '#234535', dark: '#122418' },
            gold:    { DEFAULT: '#EAB308', light: '#FCD34D', dark: '#CA8A04' },
            cream:   { DEFAULT: '#FFF8E7', dark: '#F5EDD0' }
          },
          fontFamily: { sans: ['Inter', 'sans-serif'] }
        }
      }
    }
  </script>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Firebase SDK v10 (Compat) -->
  <script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-auth-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-firestore-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-storage-compat.js"></script>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?= $base_path ?? '' ?>frontend/assets/css/style.css">

  <style>
    body { font-family: 'Inter', sans-serif; }
    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; }
    ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 3px; }
    .sidebar-scroll::-webkit-scrollbar { width: 3px; }
    .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); }
    .menu-section-label { font-size:10px; letter-spacing:0.1em; color:rgba(255,255,255,0.4); text-transform:uppercase; padding: 8px 16px 4px; }
    .submenu { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; }
    .submenu.open { max-height: 200px; }
    .badge-label { font-size:9px; padding:2px 6px; border-radius:4px; font-weight:700; letter-spacing:0.05em; }
    .toast { position:fixed; top:20px; right:20px; z-index:9999; transform:translateX(120%); transition:transform 0.3s ease; }
    .toast.show { transform:translateX(0); }
    .spinner { border:3px solid rgba(255,255,255,0.3); border-top-color:#fff; border-radius:50%; width:20px; height:20px; animation:spin 0.7s linear infinite; display:inline-block; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; display:flex; align-items:center; justify-content:center; }
    .modal-box { background:white; border-radius:16px; padding:24px; max-width:480px; width:90%; max-height:90vh; overflow-y:auto; }
  </style>
</head>
<body class="bg-gray-100 min-h-screen">

<!-- Toast Notification -->
<div id="toast" class="toast">
  <div id="toast-inner" class="px-5 py-3 rounded-xl shadow-lg text-white text-sm font-medium flex items-center gap-2"></div>
</div>

<!-- Firebase Init -->
<script src="<?= $base_path ?? '' ?>frontend/assets/js/firebase-init.js"></script>
