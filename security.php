<?php
require_once 'config/database.php';

// Get security personnel data
$stmt = $pdo->query("SELECT * FROM security_personnel WHERE is_active = 1 ORDER BY display_order, id");
$personnel = $stmt->fetchAll();

// Get security gallery data
$stmt = $pdo->query("SELECT * FROM security_gallery WHERE is_active = 1 ORDER BY display_order, id");
$gallery = $stmt->fetchAll();
?>

<?php
require_once 'config/database.php';

// Get security personnel data
$stmt = $pdo->query("SELECT * FROM security_personnel WHERE is_active = 1 ORDER BY display_order, id");
$personnel = $stmt->fetchAll();

// Get security gallery data
$stmt = $pdo->query("SELECT * FROM security_gallery WHERE is_active = 1 ORDER BY display_order, id");
$gallery = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>OHSS Performance Dashboard</title>

  <!-- Font Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />

  <!-- Font Awesome -->
  <link rel="stylesheet" href=" https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css " />

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js "></script>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com "></script>

  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'primary-blue': '#0A4D9E',
            'header-footer-bg': '#e53935', // merah terang
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
    /* Menyembunyikan header khusus cetak secara default */
    .print-only-header {
      display: none;
    }
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
      aside, header.bg-header-footer-bg, footer, #fullscreenBtn, #exitFullscreenBtn, .sticky, .print-hidden {
        display: none !important;
      }
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
      main.main-content {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
      }
      .page-break-before {
        page-break-before: always;
      }
      table {
        width: 100% !important;
        border-collapse: collapse !important;
        page-break-inside: avoid !important;
        font-size: 9px !important;
      }
      th, td {
        padding: 4px 6px !important;
        border: 1px solid #ccc !important;
      }
      th {
        background-color: #0A4D9E !important;
        color: #fff !important;
      }
      tr:nth-child(even) {
        background-color: #f2f2f2 !important;
      }
      .bg-primary-blue, .bg-primary-blue\/90 { background-color: #0A4D9E !important; }
      .bg-blue-100 { background-color: #DBEAFE !important; }
      .bg-blue-50 { background-color: #EFF6FF !important; }
      .bg-green-400 { background-color: #4ADE80 !important; }
      .bg-green-100 { background-color: #D1FAE5 !important; }
      .text-white { color: #fff !important; }
      .text-black { color: #000 !important; }
      .rounded-lg, .rounded-t-lg, .rounded-b-lg { border-radius: 0 !important; }
      .shadow, .shadow-lg, .shadow-md { box-shadow: none !important; }
      .overflow-x-auto { overflow: visible !important; }
      .font-bold { font-weight: bold !important; }
      .font-extrabold { font-weight: 800 !important; }
      .underline { text-decoration: underline !important; }
      h2.text-lg, .font-bold.text-base.mb-2, .font-extrabold.text-2xl, .font-extrabold.text-xl {
        color: #000 !important;
      }
    }
    /* Loader Spinner */
    #loader-bg {
      position: fixed;
      z-index: 9999;
      top: 0; left: 0; right: 0; bottom: 0;
      background: #f4f7fa;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: opacity 0.5s;
    }
    .loader {
      border: 6px solid #e0e7ef;
      border-top: 6px solid #e53935;
      border-radius: 50%;
      width: 60px;
      height: 60px;
      animation: spin 1s linear infinite;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    .fade-in {
      opacity: 0;
      transition: opacity 1s;
    }
    .fade-in.show {
      opacity: 1;
    }
    /* Efek hover zoom dan shadow pada foto galeri */
    .gallery-photo {
      transition: transform 0.4s cubic-bezier(.4,2,.3,1), box-shadow 0.4s;
      box-shadow: 0 2px 8px 0 rgba(0,0,0,0.08);
      border-radius: 0.75rem;
    }
    .gallery-photo:hover {
      transform: scale(1.07) rotate(-1deg);
      box-shadow: 0 8px 32px 0 rgba(10,77,158,0.18);
      z-index: 2;
    }
    .gallery-fadein {
      opacity: 0;
      transform: translateY(30px);
      transition: opacity 0.8s, transform 0.8s;
    }
    .gallery-fadein.show {
      opacity: 1;
      transform: none;
    }
  </style>
</head>
<body class="bg-background-color text-gray-800 font-sans min-h-screen flex flex-col">

<!-- Loader Spinner -->
<div id="loader-bg">
  <div class="loader"></div>
</div>

<!-- HEADER KHUSUS PRINT -->
<div class="print-only-header">
  <div class="print-header-top">
    <span id="print-datetime"></span>
    <span class="title">OHSS Performance Dashboard</span>
    <span id="print-datetime-spacer" style="visibility: hidden;"></span>
  </div>
  <div class="print-header-bottom">
    <img src="img/batamindo.png" alt="Batamindo Investment Cakrawala Logo" class="logo">
    <div class="report-info">
      <div class="main-title">Report OHSS Monthly</div>
      <div class="sub-title">BIC / OHSS-25-034-006-142 | Cut of date: 01 June – 30 June 2025</div>
    </div>
  </div>
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
      <a href="index.html" class="flex items-center gap-3 px-4 py-2 rounded-xl font-semibold bg-white/10 hover:bg-white/20 text-white transition shadow-sm backdrop-blur-md text-base">
        <i class="fas fa-home text-lg"></i> Dashboard
      </a>
      <a href="OHS.html" class="flex items-center gap-3 px-4 py-2 rounded-xl font-semibold bg-white/10 hover:bg-white/20 text-white transition shadow-sm backdrop-blur-md">
        <i class="fas fa-shield-alt"></i> OHSS
      </a>
      <a href="security.html" class="flex items-center gap-3 px-4 py-2 rounded-xl font-semibold bg-white/10 hover:bg-white/20 text-white transition shadow-sm backdrop-blur-md">
        <i class="fas fa-user-shield"></i> Security
      </a>
      <a href="firesafety.html" class="flex items-center gap-3 px-4 py-2 rounded-xl font-semibold bg-white/10 hover:bg-white/20 text-white transition shadow-sm backdrop-blur-md">
        <i class="fas fa-fire-extinguisher"></i> Fire Safety
      </a>
      <a href="surveillance.html" class="flex items-center gap-3 px-4 py-2 rounded-xl font-semibold bg-white/10 hover:bg-white/20 text-white transition shadow-sm backdrop-blur-md">
        <i class="fas fa-video"></i> Surveillance
      </a>
      <div class="mt-8 text-xs text-white/80 px-2">
        <span class="block">&copy; 2025 Batamindo Investment Cakrawala.</span>
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
      <div class="text-sm font-semibold leading-tight">Report OHSS Monthly</div>
      <div class="text-[10px] opacity-80 mt-0.5 leading-tight">
        BIC / OHSS-25-034-006-142 | Cut of date: 01 June – 30 June 2025
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
  <main class="main-content flex-1 fade-in">
    <!-- TOMBOL EXPORT PDF -->
    <div class="sticky top-2 z-30 flex justify-end mb-2 px-2 print-hidden">
      <button onclick="window.print()" class="bg-primary-blue hover:bg-blue-800 text-white font-bold py-2 px-6 rounded-lg shadow transition-all duration-200 border border-blue-900 flex items-center gap-2">
        <i class='fas fa-file-pdf text-lg'></i> Export ke PDF
      </button>
    </div>
    <!-- Judul sebelum tabel -->
    <div class="font-bold text-lg mb-2 mt-4">1. Security (Monitoring & Supervision)</div>
    <!-- TABEL SECURITY PERFORMING AUTHORITY -->
    <div class="overflow-x-auto mt-5 px-8">
      <table class="w-full max-w-6xl mx-auto rounded-2xl overflow-hidden shadow-xl bg-white border border-gray-300">
        <thead>
          <tr class="bg-primary-blue text-white text-center">
            <th colspan="4" class="py-3 px-2 text-lg font-extrabold tracking-wide border-b border-gray-300">Security Performing Authority</th>
          </tr>
          <tr class="bg-blue-900 text-white text-center text-xs">
            <th class="py-2 px-2 border-b border-gray-200">No</th>
            <th class="py-2 px-2 border-b border-gray-200">Jabatan</th>
            <th class="py-2 px-2 border-b border-gray-200">Jumlah Personel</th>
            <th class="py-2 px-2 border-b border-gray-200">Nama / Keterangan</th>
          </tr>
        </thead>
        <tbody class="text-xs">
          <?php foreach ($personnel as $index => $item): ?>
          <tr class="<?= $index % 2 == 0 ? 'bg-blue-50' : 'bg-white' ?> hover:bg-blue-100 transition text-center font-semibold">
            <td class="py-2 px-2 border-b border-gray-200"><?= $index + 1 ?></td>
            <td class="py-2 px-2 border-b border-gray-200 text-left"><?= htmlspecialchars($item['position']) ?></td>
            <td class="py-2 px-2 border-b border-gray-200"><?= $item['personnel_count'] ?> personel</td>
            <td class="py-2 px-2 border-b border-gray-200 text-left">
              <?php if ($item['personnel_names']): ?>
                <?= nl2br(htmlspecialchars($item['personnel_names'])) ?>
              <?php else: ?>
                <?= htmlspecialchars(isset($item['description']) ? $item['description'] : '') ?>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>

<!-- Galeri Patroli BBT Tower -->
<section class="max-w-6xl mx-auto px-2 mt-8 mb-8 page-break-before">
  <h2 class="text-2xl md:text-3xl font-bold mb-4 text-center">Dokumentasi</h2>
  <h3 class="text-xl md:text-2xl font-semibold mb-4 text-center">(Dokumentasi Security Monitoring & Sweeping DLL)</h3>
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
    <?php foreach ($gallery as $item): ?>
    <div class="relative bg-white rounded-lg shadow overflow-hidden flex items-center justify-center gallery-fadein">
      <img src="<?= htmlspecialchars($item['photo_path']) ?>" 
           alt="<?= htmlspecialchars(isset($item['photo_alt']) ? $item['photo_alt'] : $item['title']) ?>" 
           class="object-cover w-full h-48 gallery-photo">
      <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-60 text-white text-xs p-2">
        <?= htmlspecialchars($item['description']) ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

  <!-- Footer -->
  <footer class="bg-header-footer-bg text-white text-center py-2 mt-3 text-xs">
    <p>&copy; 2025 Batamindo Investment Cakrawala. All rights reserved</p>
  </footer>

  <!-- Scripts -->
  <script>
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

    // Script untuk tanggal dinamis di header cetak
    window.onbeforeprint = function() {
      const now = new Date();
      const options = { year: '2-digit', month: 'numeric', day: 'numeric', hour: 'numeric', minute: 'numeric', hour12: true };
      const formattedDateTime = now.toLocaleString('en-US', options).replace(',', '');
      document.getElementById('print-datetime').textContent = formattedDateTime;
      document.getElementById('print-datetime-spacer').textContent = formattedDateTime;
    };

    // Loader logic
    document.addEventListener('DOMContentLoaded', function() {
      setTimeout(function() {
        document.getElementById('loader-bg').style.opacity = 0;
        setTimeout(function() {
          document.getElementById('loader-bg').style.display = 'none';
          var main = document.querySelector('main.main-content');
          if (main) main.classList.add('show');
          // Fade-in animasi untuk galeri foto
          document.querySelectorAll('.gallery-fadein').forEach(function(el, i) {
            setTimeout(function() {
              el.classList.add('show');
            }, 200 + i * 120);
          });
        }, 500);
      }, 900); // Loader tampil minimal 0.9 detik
    });
  </script>
</body>
</html>