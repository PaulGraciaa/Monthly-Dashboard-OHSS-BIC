<?php
require_once 'config/database.php';

// Ambil data dari database
$stmt = $pdo->query("SELECT * FROM surveillance_overall_performance ORDER BY id ASC");
$surveillanceData = $stmt->fetchAll();

// Ambil data improvements progress
$stmt = $pdo->query("SELECT * FROM surveillance_improvements_progress ORDER BY id ASC");
$improvementsData = $stmt->fetchAll();

// Ambil data CCTV System
$stmt = $pdo->query("SELECT * FROM surveillance_cctv_system ORDER BY category, id ASC");
$cctvData = $stmt->fetchAll();

// Ambil data ISSS Software Utilization
$stmt = $pdo->query("SELECT * FROM surveillance_isss_software ORDER BY id ASC");
$isssData = $stmt->fetchAll();

// Ambil data Security Team Patrol Performance
$stmt = $pdo->query("SELECT * FROM surveillance_security_patrol ORDER BY id ASC");
$securityPatrolData = $stmt->fetchAll();

// Ambil data Security Team Performance on QR Scanned
$stmt = $pdo->query("SELECT * FROM surveillance_qr_scanned ORDER BY id ASC");
$qrScannedData = $stmt->fetchAll();

// Ambil data Road Map CCTV & Surveillance Mapping
$stmt = $pdo->query("SELECT * FROM surveillance_roadmap_mapping ORDER BY location_number ASC");
$roadmapMappingData = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <link rel="icon" type="image/png" href="img/logo_safety.png" />
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>OHSS Performance Dashboard - Surveillance</title>

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
        color: #000 !important;
      }
      th {
        background-color: #0A4D9E !important;
        color: #fff !important;
      }
      tr:nth-child(even) {
        background-color: #f2f2f2 !important;
      }

      .bg-primary-blue, .bg-primary-blue\/90 { background-color: #0A4D9E !important; }
      .bg-blue-200 { background-color: #BFDBFE !important; }
      .bg-blue-100 { background-color: #DBEAFE !important; }
      .bg-blue-50 { background-color: #EFF6FF !important; }
      .bg-green-400, .bg-green-100 { background-color: #A7F3D0 !important; }
      .bg-yellow-300 { background-color: #FDE047 !important; }
      .text-white { color: #fff !important; }
      .text-black { color: #000 !important; }
      .rounded-lg, .rounded-t-lg, .rounded-b-lg, .rounded-xl, .rounded-2xl { border-radius: 0 !important; }
      .shadow, .shadow-lg, .shadow-md, .shadow-2xl { box-shadow: none !important; }
      .overflow-x-auto { overflow: visible !important; }
      .font-bold { font-weight: bold !important; }
      .font-extrabold { font-weight: 800 !important; }
      h2, .font-extrabold.text-blue-900 {
        color: #000 !important;
      }
    }
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

    @media print {
      .print\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
      .print\:gap-4 { gap: 1rem !important; }
      .print\:break-inside-avoid { break-inside: avoid !important; page-break-inside: avoid !important; }
      .print-keep-together { page-break-inside: avoid !important; break-inside: avoid !important; }
    }
  </style>
</head>
<body class="bg-background-color text-gray-800 font-sans min-h-screen flex flex-col">

<!-- Loader Spinner Merah -->
<div id="loader-bg" style="position:fixed;z-index:9999;top:0;left:0;right:0;bottom:0;background:#f4f7fa;display:flex;align-items:center;justify-content:center;transition:opacity 0.5s;">
  <div class="loader" style="border:6px solid #e0e7ef;border-top:6px solid #e53935;border-radius:50%;width:60px;height:60px;animation:spin 1s linear infinite;"></div>
</div>

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
        <span class="block">&copy; 2025 Batamindo Investment Cakrawala.</span>
      </div>
    </div>
</aside>

  <header class="bg-header-footer-bg text-white px-2 py-1 flex items-center justify-between mb-3 min-h-0 h-12 relative">
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
    <div class="flex items-center flex-grow justify-center">
      <img src="img/batamindo.png" alt="Batamindo Investment Cakrawala Logo" class="h-6 object-contain">
    </div>
    <div class="text-right hidden sm:block">
      <div class="text-sm font-semibold leading-tight">Report OHSS Monthly</div>
      <div class="text-[10px] opacity-80 mt-0.5 leading-tight">
        BIC / OHSS-25-034-006-142 | Cut of date: 01 June – 30 June 2025
      </div>
    </div>
  </header>
  
  <div class="sticky top-2 z-30 flex justify-end mb-2 px-2 print-hidden">
    <button onclick="window.print()" class="bg-primary-blue hover:bg-blue-800 text-white font-bold py-2 px-6 rounded-lg shadow transition-all duration-200 border border-blue-900 flex items-center gap-2">
      <i class='fas fa-file-pdf text-lg'></i> Export ke PDF
    </button>
  </div>
  
  <button id="fullscreenBtn" class="fixed bottom-4 right-4 z-50 bg-header-footer-bg text-white rounded-full shadow-lg p-4 hover:bg-primary-blue transition-colors duration-200 flex items-center justify-center w-14 h-14" title="Full Screen">
    <i class="fas fa-expand text-2xl"></i>
  </button>
  <button id="exitFullscreenBtn" class="fixed bottom-4 right-4 z-50 bg-header-footer-bg text-white rounded-full shadow-lg p-4 hover:bg-primary-blue transition-colors duration-200 flex items-center justify-center w-14 h-14 hidden" title="Exit Full Screen">
    <i class="fas fa-compress text-2xl"></i>
  </button>

  <main class="main-content flex-1 px-4 py-6 fadein">
    <h2 class="text-lg font-bold mb-4">IMPROVEMENTS PROJECT PROGRESS</h2>
    <div class="overflow-x-auto">
      <table class="min-w-full border border-gray-400 rounded-lg bg-white shadow">
        <thead>
          <tr class="bg-primary-blue text-white text-center">
            <th class="py-2 px-3 border-b border-gray-400 border-r border-l">S/No</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">IMPROVEMENTS PROJECT TITLE</th> 
            <th class="py-2 px-3 border-b border-gray-400 border-r">STATUS</th>
            <th class="py-2 px-3 border-b border-gray-400">PERCENTAGE</th>
          </tr>
        </thead>
        <tbody class="text-sm">
          <?php foreach ($improvementsData as $index => $row): ?>
          <tr class="<?php echo $index % 2 == 0 ? 'bg-blue-100' : 'bg-blue-50'; ?> text-center font-bold">
            <td class="py-2 px-3 align-top border-t border-b border-gray-400 border-r border-l"><?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?></td>
            <td class="py-2 px-3 text-left font-semibold align-top border-t border-b border-gray-400 border-r">
              <?php echo htmlspecialchars($row['project_title']); ?>
              <?php if (!empty($row['description'])): ?>
              <div class="font-normal"><?php echo htmlspecialchars($row['description']); ?></div>
              <?php endif; ?>
            </td>
            <td class="py-2 px-3 align-top border-t border-b border-gray-400 border-r">
              <?php
              $statusClass = '';
              switch($row['status']) {
                  case 'Done':
                      $statusClass = 'bg-green-400 text-white';
                      break;
                  case 'In Progress':
                      $statusClass = 'bg-yellow-300 text-black';
                      break;
                  case 'Pending':
                      $statusClass = 'bg-blue-300 text-black';
                      break;
                  case 'Cancelled':
                      $statusClass = 'bg-red-400 text-white';
                      break;
                  default:
                      $statusClass = 'bg-gray-300 text-black';
              }
              ?>
              <span class="<?php echo $statusClass; ?> px-2 py-1 rounded font-bold text-center block"><?php echo htmlspecialchars($row['status']); ?></span>
            </td>
            <td class="py-2 px-3 align-top border-t border-b border-gray-400"><?php echo htmlspecialchars($row['percentage']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <h2 class="text-lg font-bold mb-2">1. Surveillance System Overall Performance</h2>
    <table class="min-w-full border border-gray-400 rounded-lg bg-white shadow">
      <thead>
        <tr class="bg-primary-blue text-white text-center">
          <th class="py-2 px-3 border-b border-gray-400 border-r border-l text-left" style="width:40%">Performance Indicators</th>
          <th class="py-2 px-3 border-b border-gray-400 border-r text-center">Current Month</th>
          <th class="py-2 px-3 border-b border-gray-400 text-center">Cumulative</th>
        </tr>
      </thead>
      <tbody class="text-sm">
        <?php foreach ($surveillanceData as $index => $row): ?>
        <tr class="<?php echo $index % 2 == 0 ? 'bg-blue-100' : 'bg-blue-50'; ?> font-bold">
          <td class="py-2 px-3 border-b border-gray-400 border-r border-l text-left"><?php echo htmlspecialchars($row['indicator']); ?></td>
          <td class="py-2 px-3 border-b border-gray-400 border-r text-center align-middle"><?php echo htmlspecialchars($row['current_month']); ?></td>
          <td class="py-2 px-3 border-b border-gray-400 text-center align-middle"><?php echo htmlspecialchars($row['cumulative']); ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="overflow-x-auto page-break-before mt-8">
      <h2 class="text-lg font-bold mb-2">2. CCTV System</h2>
      <table class="min-w-full border border-gray-400 rounded-lg bg-white shadow">
        <thead>
          <tr class="bg-primary-blue text-white text-center">
            <th class="py-2 px-3 border-b border-gray-400 border-r border-l text-left" style="width:40%">Description</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r text-center" colspan="2">Quantity</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r text-center">% Readiness / Performance</th>
          </tr>
        </thead>
        <tbody class="text-sm">
          <?php 
          $currentCategory = '';
          foreach ($cctvData as $index => $row): 
            if ($row['category'] != $currentCategory):
              $currentCategory = $row['category'];
              $categoryLabel = '';
              switch($currentCategory) {
                case 'Deployed CCTV Cameras Readiness': $categoryLabel = 'a. Deployed CCTV Cameras Readiness'; break;
                case 'Total Portable CCTV Cameras': $categoryLabel = 'b. Total Portable CCTV Cameras'; break;
                case 'Preventive Maintenance': $categoryLabel = 'c. Preventive Maintenance'; break;
                case 'Corrective Maintenance': $categoryLabel = 'd. Corrective Maintenance'; break;
                case 'CCTV Footage Request': $categoryLabel = 'e. CCTV Footage Request'; break;
                default: $categoryLabel = $currentCategory;
              }
          ?>
          <tr class="bg-blue-100 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l" colspan="4"><?php echo $categoryLabel; ?></td>
          </tr>
          <?php endif; ?>
          <tr class="bg-blue-50">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l pl-8 font-semibold"><?php echo htmlspecialchars($row['description']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center font-semibold">Operational<br><span class='text-green-600 font-bold'><?php echo htmlspecialchars($row['operational']); ?></span></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center font-semibold">Non-Operational<br><?php echo htmlspecialchars($row['non_operational']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 text-center font-semibold"><?php echo htmlspecialchars($row['readiness_percentage']); ?></td>
          </tr>
          <?php if (!empty($row['notes'])): ?>
          <tr class="bg-blue-50">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l text-left italic" colspan="4">
              <?php echo htmlspecialchars($row['notes']); ?>
            </td>
          </tr>
          <?php endif; ?>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="overflow-x-auto page-break-before mt-8">
      <h2 class="text-lg font-bold mb-2">3. ISSS Software Utilization</h2>
      <table class="min-w-full border border-gray-400 rounded-lg bg-white shadow">
        <thead>
          <tr class="bg-primary-blue text-white text-center">
            <th class="py-2 px-3 border-b border-gray-400 border-r border-l" style="width:32%"></th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">Jan</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">Feb</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">Mar</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">Apr</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">May</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r bg-green-400 text-black">Jun</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">Jul</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">Aug</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">Sep</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">Oct</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">Nov</th>
            <th class="py-2 px-3 border-b border-gray-400">Dec</th>
          </tr>
        </thead>
        <tbody class="text-sm">
          <?php foreach ($isssData as $index => $row): ?>
          <tr class="<?php echo $index % 2 == 0 ? 'bg-blue-100' : 'bg-blue-50'; ?> font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l"><?php echo htmlspecialchars($row['metric_name']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center"><?php echo htmlspecialchars($row['jan']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center"><?php echo htmlspecialchars($row['feb']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center"><?php echo htmlspecialchars($row['mar']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center"><?php echo htmlspecialchars($row['apr']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center"><?php echo htmlspecialchars($row['may']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-green-100 text-black text-center font-bold"><?php echo htmlspecialchars($row['jun']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50 text-center"><?php echo htmlspecialchars($row['jul']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50 text-center"><?php echo htmlspecialchars($row['aug']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50 text-center"><?php echo htmlspecialchars($row['sep']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50 text-center"><?php echo htmlspecialchars($row['oct']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50 text-center"><?php echo htmlspecialchars($row['nov']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 bg-blue-50 text-center"><?php echo htmlspecialchars($row['dec']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="overflow-x-auto page-break-before mt-8">
      <h2 class="text-lg font-bold mb-2">4. Security Team Patrol Performance</h2>
      <table class="min-w-full border border-gray-400 rounded-lg bg-white shadow">
        <thead>
          <tr class="bg-primary-blue text-white text-center">
            <th class="py-2 px-3 border-b border-gray-400 border-r border-l" style="width:24%">Patrol Duration (Hours)</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">Jan</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">Feb</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">Mar</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">Apr</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">May</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r bg-green-400 text-black">Jun</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">Jul</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">Aug</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">Sep</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">Oct</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">Nov</th>
            <th class="py-2 px-3 border-b border-gray-400">Dec</th>
          </tr>
        </thead>
        <tbody class="text-sm">
          <?php foreach ($securityPatrolData as $index => $row): ?>
          <tr class="<?php echo $index % 2 == 0 ? 'bg-blue-100' : 'bg-blue-50'; ?> font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l"><?php echo htmlspecialchars($row['team_name']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center"><?php echo htmlspecialchars($row['jan']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center"><?php echo htmlspecialchars($row['feb']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center"><?php echo htmlspecialchars($row['mar']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center"><?php echo htmlspecialchars($row['apr']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center"><?php echo htmlspecialchars($row['may']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-green-100 text-black text-center font-bold"><?php echo htmlspecialchars($row['jun']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50 text-center"><?php echo htmlspecialchars($row['jul']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50 text-center"><?php echo htmlspecialchars($row['aug']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50 text-center"><?php echo htmlspecialchars($row['sep']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50 text-center"><?php echo htmlspecialchars($row['oct']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50 text-center"><?php echo htmlspecialchars($row['nov']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 bg-blue-50 text-center"><?php echo htmlspecialchars($row['dec']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="overflow-x-auto page-break-before mt-8">
      <h2 class="text-lg font-bold mb-2">5. Security Team Performance on QR Scanned</h2>
      <table class="min-w-full border border-gray-400 rounded-lg bg-white shadow">
        <thead>
          <tr class="bg-primary-blue text-white text-center">
            <th class="py-2 px-3 border-b border-gray-400 border-r border-l" style="width:24%">QR Checkpoints Scanned</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">Jan</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">Feb</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">Mar</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">Apr</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">May</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r bg-green-400 text-black">Jun</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">Jul</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">Aug</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">Sep</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">Oct</th>
            <th class="py-2 px-3 border-b border-gray-400 border-r">Nov</th>
            <th class="py-2 px-3 border-b border-gray-400">Dec</th>
          </tr>
        </thead>
        <tbody class="text-sm">
          <?php foreach ($qrScannedData as $index => $row): ?>
          <tr class="<?php echo $index % 2 == 0 ? 'bg-blue-100' : 'bg-blue-50'; ?> font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l"><?php echo htmlspecialchars($row['team_name']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center"><?php echo htmlspecialchars($row['jan']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center"><?php echo htmlspecialchars($row['feb']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center"><?php echo htmlspecialchars($row['mar']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center"><?php echo htmlspecialchars($row['apr']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center"><?php echo htmlspecialchars($row['may']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-green-100 text-black text-center font-bold"><?php echo htmlspecialchars($row['jun']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50 text-center"><?php echo htmlspecialchars($row['jul']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50 text-center"><?php echo htmlspecialchars($row['aug']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50 text-center"><?php echo htmlspecialchars($row['sep']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50 text-center"><?php echo htmlspecialchars($row['oct']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50 text-center"><?php echo htmlspecialchars($row['nov']); ?></td>
            <td class="py-2 px-3 border-b border-gray-400 bg-blue-50 text-center"><?php echo htmlspecialchars($row['dec']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="overflow-x-auto page-break-before mt-8">
      <h2 class="text-lg font-bold mb-2">6. Road Map CCTV & Surveillance Mapping(CCTV Monitoring)</h2>
      <div class="w-full flex flex-col items-center">
        <img src="img/Map.png" alt="Road Map CCTV Batamindo" class="w-full h-auto rounded-lg border-2 border-gray-400 shadow-lg mb-2" style="max-width:100%; min-width:0;">
        <span class="text-xs text-gray-600 mt-2 text-center block w-full">Peta lokasi dan distribusi CCTV di Batamindo Industrial Park</span>
      </div>
    </div>
    <div class="overflow-x-auto mt-8 print-keep-together">
      <div class="print-keep-together grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 bg-gradient-to-br from-blue-50 via-white to-blue-100 p-6 rounded-2xl shadow-2xl border border-gray-200 print:grid-cols-2 print:gap-4">
        <?php foreach ($roadmapMappingData as $location): ?>
        <div class="flex flex-col items-center bg-white rounded-xl shadow-md border border-gray-200 p-4 mb-2 print:break-inside-avoid">
          <img src="<?php echo htmlspecialchars($location['image_path']); ?>" alt="<?php echo htmlspecialchars($location['location_name']); ?>" class="w-full max-w-xs h-auto rounded-lg border mb-2 print:max-w-full print:h-auto">
          <div class="font-extrabold text-blue-900 text-base text-center mb-1"><?php echo htmlspecialchars($location['location_number']); ?>. <?php echo htmlspecialchars($location['location_name']); ?></div>
          <?php if (!empty($location['description'])): ?>
          <div class="text-xs text-gray-600 text-center"><?php echo htmlspecialchars($location['description']); ?></div>
          <?php endif; ?>
          <?php if (!empty($location['cctv_coverage'])): ?>
          <div class="text-xs text-green-600 font-semibold text-center mt-1">CCTV: <?php echo htmlspecialchars($location['cctv_coverage']); ?></div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </main>
  
  <footer class="bg-header-footer-bg text-white text-center py-2 mt-3 text-xs">
    <p>&copy; 2025 Batamindo Investment Cakrawala. All rights reserved</p>
  </footer>

  <script>
    // Script untuk tanggal dinamis di header cetak
    window.onbeforeprint = function() {
      const now = new Date();
      const options = { year: '2-digit', month: 'numeric', day: 'numeric', hour: 'numeric', minute: 'numeric', hour12: true };
      const formattedDateTime = now.toLocaleString('en-US', options).replace(',', '');
      document.getElementById('print-datetime').textContent = formattedDateTime;
      document.getElementById('print-datetime-spacer').textContent = formattedDateTime;
    };
    
    document.addEventListener('DOMContentLoaded', function() {
      // Fullscreen toggle logic
      const fullscreenBtn = document.getElementById('fullscreenBtn');
      const exitFullscreenBtn = document.getElementById('exitFullscreenBtn');
      if (fullscreenBtn) {
        fullscreenBtn.addEventListener('click', () => {
          document.documentElement.requestFullscreen().catch(err => {
            console.error(`Error attempting to enable full-screen mode: ${err.message} (${err.name})`);
          });
        });
      }
      if (exitFullscreenBtn) {
        exitFullscreenBtn.addEventListener('click', () => {
          if (document.exitFullscreen) {
            document.exitFullscreen();
          }
        });
      }
      document.addEventListener('fullscreenchange', () => {
        const isFullscreen = !!document.fullscreenElement;
        if(fullscreenBtn) fullscreenBtn.classList.toggle('hidden', isFullscreen);
        if(exitFullscreenBtn) exitFullscreenBtn.classList.toggle('hidden', !isFullscreen);
      });

      // Sidebar toggle logic
      const sidebar = document.getElementById('sidebar');
      const menuBtn = document.getElementById('menuBtn');
      const sidebarBackBtn = document.getElementById('sidebarBackBtn');
      function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
      }
      menuBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        sidebar.classList.toggle('-translate-x-full');
      });
      sidebarBackBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        closeSidebar();
      });
      document.addEventListener('click', function(e) {
        if (!sidebar.contains(e.target) && !menuBtn.contains(e.target)) {
          closeSidebar();
        }
      });
    });
    // Loader Spinner Merah
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
</body>
</html>