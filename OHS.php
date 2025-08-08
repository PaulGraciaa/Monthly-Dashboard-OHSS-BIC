<?php
session_start();
require_once 'config/database.php';

// Get configuration
$config = [];
$configData = $pdo->query("SELECT * FROM config")->fetchAll();
foreach ($configData as $item) {
    $config[$item['config_key']] = $item['config_value'];
}

// Get KPI data
$kpiLeading = $pdo->query("SELECT * FROM kpi_leading ORDER BY indicator_name")->fetchAll();
$kpiLagging = $pdo->query("SELECT * FROM kpi_lagging ORDER BY indicator_name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>OHSS Performance Dashboard - OHSS</title>

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

  <style>
    /* Menyembunyikan header khusus cetak pada tampilan web */
    .print-only-header {
      display: none;
    }

    /* CSS UNTUK PRINT/PDF */
    @media print {
      @page {
        size: landscape;
        margin: 1cm;
      }
      body {
        background-color: #fff !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }
      
      /* Sembunyikan elemen web yang tidak perlu */
      aside, header.bg-header-footer-bg, footer, #fullscreenBtn, #exitFullscreenBtn, .sticky, .print-hidden {
        display: none !important;
      }

      /* Tampilkan dan atur tata letak header khusus untuk cetak */
      .print-only-header {
        display: block !important;
        margin-bottom: 1.5rem;
        font-family: 'Inter', sans-serif;
      }
      .print-header-top {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center;
        font-size: 10pt;
        color: #000 !important;
      }
      .print-header-top .title {
        font-weight: bold;
        font-size: 14pt;
        flex-grow: 1;
        text-align: center;
      }
      .print-header-bottom {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center;
        margin-top: 1rem;
        color: #000 !important;
      }
      .print-header-bottom .logo {
        height: 35px;
        width: auto;
      }
      .print-header-bottom .report-info {
        text-align: right;
      }
      .print-header-bottom .report-info .main-title {
        font-size: 12pt;
        font-weight: 600;
      }
      .print-header-bottom .report-info .sub-title {
        font-size: 8pt;
      }

      /* Pengaturan konten utama dan halaman baru */
      main.main-content {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
      }
    }
  </style>
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
      <div class="text-sm font-semibold leading-tight"><strong><?php echo $config['dashboard_title'] ?? 'Dashboard OHSS Monthly'; ?></strong></div>
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
      <!-- KPI Charts -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Leading Indicators -->
        <div class="bg-white rounded-xl shadow-md border border-blue-100 p-4">
          <h3 class="text-lg font-bold text-primary-blue mb-4">Leading Indicators</h3>
          <canvas id="leadingChart" width="400" height="300"></canvas>
        </div>
        
        <!-- Lagging Indicators -->
        <div class="bg-white rounded-xl shadow-md border border-blue-100 p-4">
          <h3 class="text-lg font-bold text-primary-blue mb-4">Lagging Indicators</h3>
          <canvas id="laggingChart" width="400" height="300"></canvas>
        </div>
      </div>

      <!-- KPI Tables -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Leading Indicators Table -->
        <div class="bg-white rounded-xl shadow-md border border-blue-100 p-4">
          <h3 class="text-lg font-bold text-primary-blue mb-4">Leading Indicators Detail</h3>
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="bg-blue-50">
                <tr>
                  <th class="px-3 py-2 text-left font-semibold text-primary-blue">Indicator</th>
                  <th class="px-3 py-2 text-center font-semibold text-primary-blue">Target</th>
                  <th class="px-3 py-2 text-center font-semibold text-primary-blue">Actual</th>
                  <th class="px-3 py-2 text-center font-semibold text-primary-blue">%</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($kpiLeading as $kpi): ?>
                <tr class="border-b border-gray-100">
                  <td class="px-3 py-2 text-gray-700"><?php echo $kpi['indicator_name']; ?></td>
                  <td class="px-3 py-2 text-center text-gray-600"><?php echo number_format($kpi['target_value']); ?></td>
                  <td class="px-3 py-2 text-center font-semibold text-primary-blue"><?php echo number_format($kpi['actual_value']); ?></td>
                  <td class="px-3 py-2 text-center">
                    <?php 
                    $percentage = $kpi['target_value'] > 0 ? ($kpi['actual_value'] / $kpi['target_value']) * 100 : 0;
                    $color = $percentage >= 80 ? 'text-green-600' : ($percentage >= 60 ? 'text-yellow-600' : 'text-red-600');
                    echo '<span class="font-bold ' . $color . '">' . number_format($percentage, 1) . '%</span>';
                    ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Lagging Indicators Table -->
        <div class="bg-white rounded-xl shadow-md border border-blue-100 p-4">
          <h3 class="text-lg font-bold text-primary-blue mb-4">Lagging Indicators Detail</h3>
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="bg-red-50">
                <tr>
                  <th class="px-3 py-2 text-left font-semibold text-red-600">Indicator</th>
                  <th class="px-3 py-2 text-center font-semibold text-red-600">Target</th>
                  <th class="px-3 py-2 text-center font-semibold text-red-600">Actual</th>
                  <th class="px-3 py-2 text-center font-semibold text-red-600">Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($kpiLagging as $kpi): ?>
                <tr class="border-b border-gray-100">
                  <td class="px-3 py-2 text-gray-700"><?php echo $kpi['indicator_name']; ?></td>
                  <td class="px-3 py-2 text-center text-gray-600"><?php echo number_format($kpi['target_value']); ?></td>
                  <td class="px-3 py-2 text-center font-semibold text-red-600"><?php echo number_format($kpi['actual_value']); ?></td>
                  <td class="px-3 py-2 text-center">
                    <?php 
                    $status = $kpi['actual_value'] <= $kpi['target_value'] ? 'Good' : 'Warning';
                    $color = $status == 'Good' ? 'text-green-600' : 'text-red-600';
                    echo '<span class="font-bold ' . $color . '">' . $status . '</span>';
                    ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
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
    // KPI Data from PHP
    const kpiLeadingData = <?php echo json_encode($kpiLeading); ?>;
    const kpiLaggingData = <?php echo json_encode($kpiLagging); ?>;

    // Leading Indicators Chart
    new Chart(document.getElementById('leadingChart').getContext('2d'), {
      type: 'bar',
      data: {
        labels: kpiLeadingData.map(item => item.indicator_name),
        datasets: [{
          label: 'Target',
          data: kpiLeadingData.map(item => item.target_value),
          backgroundColor: 'rgba(10,77,158,0.3)',
          borderColor: 'rgba(10,77,158,1)',
          borderWidth: 1
        }, {
          label: 'Actual',
          data: kpiLeadingData.map(item => item.actual_value),
          backgroundColor: 'rgba(10,77,158,0.8)',
          borderColor: 'rgba(10,77,158,1)',
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
          },
          tooltip: {
            mode: 'index',
            intersect: false,
          }
        },
        scales: {
          x: {
            display: false
          },
          y: {
            beginAtZero: true
          }
        }
      }
    });

    // Lagging Indicators Chart
    new Chart(document.getElementById('laggingChart').getContext('2d'), {
      type: 'bar',
      data: {
        labels: kpiLaggingData.map(item => item.indicator_name),
        datasets: [{
          label: 'Target',
          data: kpiLaggingData.map(item => item.target_value),
          backgroundColor: 'rgba(229,57,53,0.3)',
          borderColor: 'rgba(229,57,53,1)',
          borderWidth: 1
        }, {
          label: 'Actual',
          data: kpiLaggingData.map(item => item.actual_value),
          backgroundColor: 'rgba(229,57,53,0.8)',
          borderColor: 'rgba(229,57,53,1)',
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
          },
          tooltip: {
            mode: 'index',
            intersect: false,
          }
        },
        scales: {
          x: {
            display: false
          },
          y: {
            beginAtZero: true
          }
        }
      }
    });

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