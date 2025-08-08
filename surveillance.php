<?php
session_start();
require_once 'config/database.php';

// Get configuration
$config = [];
$configData = $pdo->query("SELECT * FROM config")->fetchAll();
foreach ($configData as $item) {
    $config[$item['config_key']] = $item['config_value'];
}

// Get Surveillance content
$surveillanceContent = $pdo->query("SELECT * FROM surveillance_content WHERE is_active = 1 ORDER BY display_order")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Surveillance - OHSS Dashboard</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>

  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'primary-blue': '#0A4D9E',
            'header-footer-bg': '#e53935',
            'background-color': '#f4f7fa',
          },
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          }
        },
      },
    };
  </script>
</head>
<body class="bg-background-color text-gray-800 font-sans min-h-screen flex flex-col">

<!-- Loader Spinner Merah -->
<div id="loader-bg" style="position:fixed;z-index:9999;top:0;left:0;right:0;bottom:0;background:#f4f7fa;display:flex;align-items:center;justify-content:center;transition:opacity 0.5s;">
  <div class="loader" style="border:6px solid #e0e7ef;border-top:6px solid #e53935;border-radius:50%;width:60px;height:60px;animation:spin 1s linear infinite;"></div>
</div>

  <!-- Sidebar -->
  <aside id="sidebar" class="fixed top-0 left-0 h-full w-60 bg-header-footer-bg text-white shadow-2xl z-40 transform -translate-x-full transition-transform duration-300 ease-in-out flex flex-col pt-0 rounded-r-3xl border-r-4 border-header-footer-bg">
    <button id="sidebarBackBtn" class="absolute top-4 left-4 bg-white text-header-footer-bg rounded-full shadow-lg w-10 h-10 flex items-center justify-center hover:bg-header-footer-bg hover:text-white transition focus:outline-none border-2 border-header-footer-bg" title="Tutup Sidebar">
      <i class="fas fa-arrow-left text-xl"></i>
    </button>
    <div class="flex flex-col gap-2 px-6 pt-20">
      <div class="mb-4 flex items-center gap-2">
        <span class="font-bold text-lg tracking-wide text-white drop-shadow">OHSS</span>
      </div>  
      <a href="index.php" class="flex items-center gap-3 px-4 py-2 rounded-xl font-semibold bg-white/10 hover:bg-white/20 text-white transition shadow-sm backdrop-blur-md text-base">
        <i class="fas fa-home text-lg"></i> Dashboard
    </a>
      <a href="OHS.php" class="flex items-center gap-3 px-4 py-2 rounded-xl font-semibold bg-white/10 hover:bg-white/20 text-white transition shadow-sm backdrop-blur-md">
        <i class="fas fa-shield-alt"></i> OHSS
      </a>
      <a href="security.php" class="flex items-center gap-3 px-4 py-2 rounded-xl font-semibold bg-white/10 hover:bg-white/20 text-white transition shadow-sm backdrop-blur-md">
        <i class="fas fa-user-shield"></i> Security
      </a>
      <a href="firesafety.php" class="flex items-center gap-3 px-4 py-2 rounded-xl font-semibold bg-white/10 hover:bg-white/20 text-white transition shadow-sm backdrop-blur-md">
        <i class="fas fa-fire-extinguisher"></i> Fire Safety
      </a>
      <a href="surveillance.php" class="flex items-center gap-3 px-4 py-2 rounded-xl font-semibold bg-white/10 hover:bg-white/20 text-white transition shadow-sm backdrop-blur-md">
        <i class="fas fa-video"></i> Surveillance
      </a>
      <div class="mt-8 text-xs text-white/80 px-2">
        <span class="block">&copy; 2025 <?php echo $config['company_name'] ?? 'Batamindo Investment Cakrawala'; ?>. All Rights Reserved.</span>
      </div>
    </div>
  </aside>

  <!-- Header -->
  <header class="bg-header-footer-bg text-white px-2 py-1 flex items-center justify-between mb-3 min-h-0 h-12 relative">
    <!-- Menu Button -->
    <div class="relative">
      <button id="menuBtn" class="text-white hover:text-gray-200 focus:outline-none" aria-label="Menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
          class="w-5 h-5">
          <line x1="3" y1="12" x2="21" y2="12"></line>
          <line x1="3" y1="6" x2="21" y2="6"></line>
          <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
      </button>
    </div>

    <!-- Logo -->
    <div class="flex items-center flex-grow justify-center">
      <img src="img/batamindo.png" alt="Batamindo Investment Cakrawala Logo" class="h-6 object-contain">
    </div>

    <!-- Title & Date -->
    <div class="text-right hidden sm:block">
      <div class="text-sm font-semibold leading-tight"><strong>Surveillance Management</strong></div>
      <div class="text-[10px] opacity-80 mt-0.5 leading-tight">
        <?php echo $config['report_code'] ?? 'BIC / OHSS-25-034-006-179'; ?> | Cut of date: <?php echo $config['cut_off_date'] ?? '01 July –31 July 2025'; ?>
      </div>
    </div>

    <!-- Fullscreen Buttons -->
    <button id="fullscreenBtn" class="fixed bottom-4 right-4 z-50 bg-header-footer-bg text-white rounded-full shadow-lg p-4 hover:bg-primary-blue transition-colors duration-200 flex items-center justify-center w-14 h-14" title="Full Screen">
      <i class="fas fa-expand text-2xl"></i>
    </button>
    <button id="exitFullscreenBtn" class="fixed bottom-4 right-4 z-50 bg-header-footer-bg text-white rounded-full shadow-lg p-4 hover:bg-primary-blue transition-colors duration-200 flex items-center justify-center w-14 h-14 hidden" title="Exit Full Screen">
      <i class="fas fa-compress text-2xl"></i>
    </button>
  </header>

  <!-- Main Content -->
  <main class="main-content flex-1 fadein">
    <div class="container mx-auto px-4 max-w-7xl">
      <!-- Page Header -->
      <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Surveillance Management</h1>
        <p class="text-gray-600">Advanced CCTV monitoring and surveillance system management</p>
      </div>

      <!-- Surveillance Content Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
        <?php foreach ($surveillanceContent as $content): ?>
        <div class="bg-white rounded-xl shadow-md border border-purple-100 p-6 hover:shadow-lg transition-shadow">
          <div class="flex items-center mb-4">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
              <i class="fas fa-video text-xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900"><?php echo $content['title']; ?></h3>
          </div>
          
          <?php if ($content['image_path']): ?>
          <div class="mb-4">
            <img src="<?php echo $content['image_path']; ?>" alt="<?php echo $content['title']; ?>" 
                 class="w-full h-48 object-cover rounded-lg">
          </div>
          <?php endif; ?>
          
          <div class="text-gray-600 leading-relaxed">
            <?php echo nl2br($content['content']); ?>
          </div>
          
          <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex items-center justify-between text-sm text-gray-500">
              <span>Section: <?php echo ucfirst(str_replace('_', ' ', $content['section_name'])); ?></span>
              <span>Order: <?php echo $content['display_order']; ?></span>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Surveillance Statistics -->
      <div class="mt-8 bg-white rounded-xl shadow-md border border-purple-100 p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Surveillance Statistics</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <div class="text-center">
            <div class="text-3xl font-bold text-purple-600 mb-2">24/7</div>
            <div class="text-gray-600">Monitoring</div>
          </div>
          <div class="text-center">
            <div class="text-3xl font-bold text-green-600 mb-2">50+</div>
            <div class="text-gray-600">CCTV Cameras</div>
          </div>
          <div class="text-center">
            <div class="text-3xl font-bold text-blue-600 mb-2">100%</div>
            <div class="text-gray-600">Coverage</div>
          </div>
          <div class="text-center">
            <div class="text-3xl font-bold text-yellow-600 mb-2">0</div>
            <div class="text-gray-600">Blind Spots</div>
          </div>
        </div>
      </div>

      <!-- Camera Status -->
      <div class="mt-8 bg-white rounded-xl shadow-md border border-purple-100 p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Camera Status Overview</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-green-50 rounded-lg p-4 border border-green-200">
            <div class="flex items-center">
              <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
              <div>
                <div class="text-lg font-semibold text-green-800">Online</div>
                <div class="text-sm text-green-600">45 Cameras</div>
              </div>
            </div>
          </div>
          <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
            <div class="flex items-center">
              <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
              <div>
                <div class="text-lg font-semibold text-yellow-800">Maintenance</div>
                <div class="text-sm text-yellow-600">3 Cameras</div>
              </div>
            </div>
          </div>
          <div class="bg-red-50 rounded-lg p-4 border border-red-200">
            <div class="flex items-center">
              <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
              <div>
                <div class="text-lg font-semibold text-red-800">Offline</div>
                <div class="text-sm text-red-600">2 Cameras</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recording Status -->
      <div class="mt-8 bg-white rounded-xl shadow-md border border-purple-100 p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Recording Status</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Storage Usage</h3>
            <div class="bg-gray-200 rounded-full h-4 mb-2">
              <div class="bg-purple-600 h-4 rounded-full" style="width: 75%"></div>
            </div>
            <div class="flex justify-between text-sm text-gray-600">
              <span>75% Used</span>
              <span>750GB / 1TB</span>
            </div>
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Retention Period</h3>
            <div class="text-3xl font-bold text-purple-600 mb-2">30 Days</div>
            <div class="text-sm text-gray-600">Automatic deletion after 30 days</div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-header-footer-bg text-white text-center py-2 mt-3 text-xs">
    <p>&copy; 2025 <?php echo $config['company_name'] ?? 'Batamindo Investment Cakrawala'; ?>. All rights reserved</p>
  </footer>

  <!-- Scripts -->
  <script>
    // Sidebar toggle logic
    document.addEventListener('DOMContentLoaded', function() {
      const sidebar = document.getElementById('sidebar');
      const menuBtn = document.getElementById('menuBtn');
      const sidebarBackBtn = document.getElementById('sidebarBackBtn');
      
      menuBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        sidebar.classList.toggle('-translate-x-full');
      });
      
      document.addEventListener('click', function(e) {
        if (!sidebar.classList.contains('-translate-x-full')) {
          sidebar.classList.add('-translate-x-full');
        }
      });
      
      sidebar.addEventListener('click', function(e) {
        e.stopPropagation();
      });
      
      sidebarBackBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        sidebar.classList.add('-translate-x-full');
      });
    });

    // Fullscreen toggle logic
    document.addEventListener('DOMContentLoaded', function() {
      const fullscreenBtn = document.getElementById('fullscreenBtn');
      const exitFullscreenBtn = document.getElementById('exitFullscreenBtn');
      
      fullscreenBtn.addEventListener('click', () => {
        const docElm = document.documentElement;
        if (docElm.requestFullscreen) {
          docElm.requestFullscreen();
        } else if (docElm.mozRequestFullScreen) {
          docElm.mozRequestFullScreen();
        } else if (docElm.webkitRequestFullscreen) {
          docElm.webkitRequestFullscreen();
        } else if (docElm.msRequestFullscreen) {
          docElm.msRequestFullscreen();
        }
      });
      
      exitFullscreenBtn.addEventListener('click', () => {
        if (document.exitFullscreen) {
          document.exitFullscreen();
        } else if (document.mozCancelFullScreen) {
          document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) {
          document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
          document.msExitFullscreen();
        }
      });
      
      document.addEventListener('fullscreenchange', () => {
        if (document.fullscreenElement) {
          fullscreenBtn.classList.add('hidden');
          exitFullscreenBtn.classList.remove('hidden');
        } else {
          fullscreenBtn.classList.remove('hidden');
          exitFullscreenBtn.classList.add('hidden');
        }
      });
    });

    // Loader
    document.addEventListener('DOMContentLoaded', function() {
      setTimeout(function() {
        document.getElementById('loader-bg').style.opacity = 0;
        setTimeout(function() {
          document.getElementById('loader-bg').style.display = 'none';
          var main = document.querySelector('main.main-content');
          if (main) main.classList.add('show');
        }, 500);
      }, 900);
    });
  </script>

  <style>
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    .fadein {
      opacity: 0;
      transition: opacity 1s;
    }
    .fadein.show {
      opacity: 1;
    }
  </style>
</body>
</html> 