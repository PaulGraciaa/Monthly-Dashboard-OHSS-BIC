<?php
require_once __DIR__ . '/config/database.php';

// Fetch various Fire Safety datasets used in admin/firesafety
try {
  // Performance summaries (just summary_text for front-end list)
  $stmt = $pdo->query("SELECT summary_text FROM fire_safety_performance WHERE is_active = 1 ORDER BY display_order ASC, id ASC");
  $fireSafetySummaries = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
  $fireSafetySummaries = array();
}

// Helper to fetch full table as associative array
function fetch_table($pdo, $sql) {
  try {
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (Exception $e) {
    return array();
  }
}

$performance_data = fetch_table($pdo, "SELECT * FROM fire_safety_performance WHERE is_active = 1 ORDER BY display_order ASC");
$emergency_data = fetch_table($pdo, "SELECT * FROM fire_safety_emergency_activation WHERE is_active = 1 ORDER BY display_order ASC");
$details_data = fetch_table($pdo, "SELECT * FROM fire_safety_emergency_details WHERE is_active = 1 ORDER BY display_order ASC");
$enforcement_data = fetch_table($pdo, "SELECT * FROM fire_safety_enforcement WHERE is_active = 1 ORDER BY display_order ASC");
$maintenance_data = fetch_table($pdo, "SELECT * FROM fire_equipment_maintenance WHERE is_active = 1 ORDER BY display_order ASC");
$statistics_data = fetch_table($pdo, "SELECT * FROM fire_equipment_statistics WHERE is_active = 1 ORDER BY display_order ASC");
$repair_data = fetch_table($pdo, "SELECT * FROM fire_safety_repair_impairment WHERE is_active = 1 ORDER BY display_order ASC");
$repair_details_data = fetch_table($pdo, "SELECT * FROM fire_safety_repair_details WHERE is_active = 1 ORDER BY display_order ASC");
$drills_data = fetch_table($pdo, "SELECT * FROM fire_safety_drills WHERE is_active = 1 ORDER BY display_order ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <link rel="icon" type="image/png" href="img/logo_safety.png" />
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>OHSS Performance Dashboard - Fire Safety</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

  <script src="https://cdn.tailwindcss.com"></script>

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
    
    @keyframes blinkRedGray {
      0%, 100% { color: #f40000; }
      50% { color: #2c08d1; }
    }
    .animate-blink {
      animation: blinkRedGray 1s infinite;
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

      /* Tampilkan dan tata letak header khusus untuk cetak */
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
      .non-compliance-box {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: center !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
        width: 100% !important;
        max-width: 100% !important;
      }
      .page-break-after {
        page-break-after: always;
        break-after: page;
      }
    }
  </style>
</head>
<body class="bg-background-color text-gray-800 font-sans min-h-screen flex flex-col">

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
        <span class="block">&copy; 2025 Batamindo Investment Cakrawala. All Rights reserved.</span>
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

  <main class="main-content flex-1 px-4 py-6">
    <div class="bg-primary-blue/90 text-white rounded-t-lg px-6 py-3 font-semibold text-base mb-0 border border-primary-blue">
      Fire Safety Performance Summary
    </div>
    <div class="bg-white border border-primary-blue border-t-0 rounded-b-lg px-6 py-4 mb-8 text-sm">
      <ol class="list-decimal pl-5 text-gray-800">
        <?php if (!empty($fireSafetySummaries)): ?>
          <?php foreach ($fireSafetySummaries as $summaryText): ?>
            <li><?= htmlspecialchars($summaryText) ?></li>
          <?php endforeach; ?>
        <?php else: ?>
          <li>Tidak ada data Fire Safety Performance Summary.</li>
        <?php endif; ?>
      </ol>
    </div>
    
    <div class="mb-8">
      <div class="font-bold text-base mb-2">EMERGENCY ACTIVATION</div>
      <div class="overflow-x-auto">
        <table class="min-w-full border border-primary-blue rounded-lg text-sm shadow">
          <thead>
            <tr class="bg-primary-blue text-white text-center">
              <th class="py-2 px-3 border-b border-primary-blue border-r border-l font-bold">Category</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Jan</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Feb</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Mar</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Apr</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">May</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold bg-green-400 text-black">Jun</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Jul</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Aug</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Sep</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Oct</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Nov</th>
              <th class="py-2 px-3 border-b border-primary-blue font-bold">Dec</th>
              <th class="py-2 px-3 border-b border-primary-blue font-bold">Grand Total</th>
            </tr>
          </thead>
          <tbody class="text-center">
            <?php if (empty($emergency_data)): ?>
              <tr>
                <td colspan="14" class="py-6">No emergency activation data available</td>
              </tr>
            <?php else: ?>
              <?php foreach ($emergency_data as $item): ?>
                <tr class="hover:bg-blue-50">
                  <td class="py-2 px-3 border-b border-primary-blue border-r border-l text-left font-bold"><?php echo htmlspecialchars($item['category']); ?></td>
                  <td class="py-2 px-3 border-b border-primary-blue border-r"><?php echo isset($item['jan_value']) ? $item['jan_value'] : '0'; ?></td>
                  <td class="py-2 px-3 border-b border-primary-blue border-r"><?php echo isset($item['feb_value']) ? $item['feb_value'] : '0'; ?></td>
                  <td class="py-2 px-3 border-b border-primary-blue border-r"><?php echo isset($item['mar_value']) ? $item['mar_value'] : '0'; ?></td>
                  <td class="py-2 px-3 border-b border-primary-blue border-r"><?php echo isset($item['apr_value']) ? $item['apr_value'] : '0'; ?></td>
                  <td class="py-2 px-3 border-b border-primary-blue border-r"><?php echo isset($item['may_value']) ? $item['may_value'] : '0'; ?></td>
                  <td class="py-2 px-3 border-b border-primary-blue border-r bg-green-100 text-black font-bold"><?php echo isset($item['jun_value']) ? $item['jun_value'] : '0'; ?></td>
                  <td class="py-2 px-3 border-b border-primary-blue border-r"><?php echo isset($item['jul_value']) ? $item['jul_value'] : '0'; ?></td>
                  <td class="py-2 px-3 border-b border-primary-blue border-r"><?php echo isset($item['aug_value']) ? $item['aug_value'] : '0'; ?></td>
                  <td class="py-2 px-3 border-b border-primary-blue border-r"><?php echo isset($item['sep_value']) ? $item['sep_value'] : '0'; ?></td>
                  <td class="py-2 px-3 border-b border-primary-blue border-r"><?php echo isset($item['oct_value']) ? $item['oct_value'] : '0'; ?></td>
                  <td class="py-2 px-3 border-b border-primary-blue border-r"><?php echo isset($item['nov_value']) ? $item['nov_value'] : '0'; ?></td>
                  <td class="py-2 px-3 border-b border-primary-blue"><?php echo isset($item['dec_value']) ? $item['dec_value'] : '0'; ?></td>
                  <td class="py-2 px-3 border-b border-primary-blue font-bold"><?php echo isset($item['grand_total']) ? $item['grand_total'] : 0; ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    
    <div class="mb-8 mt-8">
      <div class="overflow-x-auto">
        <table class="min-w-full border border-primary-blue rounded-lg text-sm shadow">
          <thead>
            <tr class="bg-primary-blue text-white text-center">
              <th class="py-2 px-3 border-b border-primary-blue border-r border-l font-bold">S/N</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">DATE</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Category</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Sub Category</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Description</th>
              <th class="py-2 px-3 border-b border-primary-blue font-bold">Location</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($details_data)): ?>
              <tr>
                <td colspan="7" class="py-6 text-center">No emergency details available</td>
              </tr>
            <?php else: ?>
              <?php foreach ($details_data as $index => $item): ?>
                <tr class="<?php echo ($index % 2 == 0) ? 'bg-blue-100' : 'bg-white'; ?> text-center font-bold">
                  <td class="py-2 px-3 border-b-0 border-primary-blue border-r border-l align-top"><?php echo htmlspecialchars(isset($item['serial_number']) ? $item['serial_number'] : ($index + 1)); ?></td>
                  <td class="py-2 px-3 border-b-0 border-primary-blue border-r align-top"><?php echo isset($item['incident_date']) ? date('d-M-y', strtotime($item['incident_date'])) : ''; ?></td>
                  <td class="py-2 px-3 border-b-0 border-primary-blue border-r align-top"><?php echo htmlspecialchars(isset($item['category']) ? $item['category'] : ''); ?></td>
                  <td class="py-2 px-3 border-b-0 border-primary-blue border-r align-top"><?php echo htmlspecialchars(isset($item['sub_category']) ? $item['sub_category'] : ''); ?></td>
                  <td class="py-2 px-3 border-b-0 border-primary-blue border-r align-top text-black font-extrabold"><?php echo htmlspecialchars(isset($item['description']) ? substr($item['description'], 0, 200) : ''); ?></td>
                  <td class="py-2 px-3 border-b-0 border-primary-blue align-top"><?php echo htmlspecialchars(isset($item['location']) ? $item['location'] : ''); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="mb-8 mt-8">
      <div class="w-full max-w-3xl mx-auto">
        <canvas id="emergencyBarChart" style="min-height:340px;height:340px;"></canvas>
      </div>
    </div>

    <div class="mb-8 mt-12 page-break-before">
      <div class="font-extrabold text-2xl leading-tight mb-1">FIRE SAFETY ENFORCEMENT</div>
      <div class="text-base mb-4">
        Fire Safety conducted fire safety inspection at <b>06 buildings</b> in June 2025. <b>03 fire safety non-conformances</b> were identified.
      </div>
      <div class="overflow-x-auto mb-6">
        <table class="min-w-full border border-primary-blue rounded-lg text-sm shadow">
          <thead>
            <tr class="bg-primary-blue text-white text-center">
              <th class="py-2 px-3 border-b border-primary-blue border-r border-l font-bold">Year 2025</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Jan</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Feb</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Mar</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Apr</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">May</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold bg-green-400 text-black">Jun</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Jul</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Aug</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Sep</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Oct</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Nov</th>
              <th class="py-2 px-3 border-b border-primary-blue font-bold">Dec</th>
              <th class="py-2 px-3 border-b border-primary-blue font-bold">Total</th>
            </tr>
          </thead>
          <tbody class="text-center">
            <tr class="bg-blue-50 font-bold">
              <td class="py-2 px-3 border-b border-primary-blue border-r border-l text-left">No of Premises</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">09</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">36</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">25</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">07</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">05</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r bg-green-100 text-black font-bold">06</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue"></td>
              <td class="py-2 px-3 border-b border-primary-blue font-bold">89</td>
            </tr>
            <tr class="bg-blue-100 font-bold">
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r border-l text-left">Non-Compliance Cases</td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r">03</td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r">05</td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r">13</td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r">01</td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r">02</td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r bg-green-100 text-black font-bold">03</td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b-0 border-primary-blue"></td>
              <td class="py-2 px-3 border-b-0 border-primary-blue font-bold">27</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="bg-white border border-gray-400 rounded-lg p-6 flex flex-col md:flex-row items-center justify-center gap-8 w-full mb-8 non-compliance-box">
        <div class="flex-shrink-0 flex items-center justify-center mb-6 md:mb-0">
          <div class="flex items-center justify-center min-w-[320px] min-h-[320px]">
            <canvas id="pieNonCompliance" style="width:320px;height:320px;"></canvas>
          </div>
        </div>
        <div class="flex-1 flex flex-col items-center md:items-start gap-4">
          <div class="font-extrabold text-2xl text-center md:text-left w-full mb-2">NON-COMPLIANCE FINDINGS FOR WY 2025</div>
          <div class="text-xs font-bold text-center md:text-left w-full">
            Sum of Non Maintenance of fire safety/firefighting provision<br>
            <span class="text-green-600 text-2xl font-extrabold">96%</span>
          </div>
          <div class="text-xs font-bold text-center md:text-left w-full">
            Sum of Obstruction of fire safety provision<br>
            <span class="text-red-600 text-2xl font-extrabold">4%</span>
          </div>
          <div class="mt-4 flex flex-col gap-1 text-xs font-bold w-full">
            <div class="flex items-center gap-2"><span class="inline-block w-4 h-4 rounded-sm bg-green-400"></span> Sum of Non Maintenance of fire safety/firefighting provision</div>
            <div class="flex items-center gap-2"><span class="inline-block w-4 h-4 rounded-sm bg-orange-400"></span> Sum of Removal of fire safety/firefighting provisions</div>
            <div class="flex items-center gap-2"><span class="inline-block w-4 h-4 rounded-sm bg-red-500"></span> Sum of Obstruction of fire safety provision</div>
            <div class="flex items-center gap-2"><span class="inline-block w-4 h-4 rounded-sm bg-yellow-300"></span> Sum of Obstruction of emergency exit</div>
          </div>
        </div>
      </div>
      <div class="page-break-after"></div>
    </div>
    
    <div class="mb-8 mt-12 page-break-before">
      <div class="font-extrabold text-xl leading-tight mb-1 underline">FIRE EQUIPMENT MAINTENANCE, SERVICING & TESTING</div>
      <div class="text-base mb-4">
        In the month of May, Fire Safety scheduled with 05 Tenant Batamindo with 06 totally Premises for our Annual Servicing of Firefighting / Fire Protection Equipment.
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full border border-primary-blue rounded-lg text-base shadow">
          <thead>
            <tr class="bg-primary-blue text-white text-center">
              <th class="py-2 px-3 border-b border-primary-blue border-r border-l font-bold">S/N</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Date</th>
              <th class="py-2 px-3 border-b border-primary-blue font-bold">Location</th>
            </tr>
          </thead>
          <tbody class="text-center font-extrabold">
            <tr class="bg-blue-100">
              <td class="py-2 px-3 border-b border-primary-blue border-r border-l">01</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">5-Jun-2025</td>
              <td class="py-2 px-3 border-b border-primary-blue">PT. PC Partner</td>
            </tr>
            <tr class="bg-white">
              <td class="py-2 px-3 border-b border-primary-blue border-r border-l">02</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">17-Jun-2025</td>
              <td class="py-2 px-3 border-b border-primary-blue">PT. Greenlam Asia Pasific</td>
            </tr>
            <tr class="bg-blue-100">
              <td class="py-2 px-3 border-b border-primary-blue border-r border-l">03</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">24-Jun-2025</td>
              <td class="py-2 px-3 border-b border-primary-blue">PT. Sanwa Engineering Batam</td>
            </tr>
            <tr class="bg-white">
              <td class="py-2 px-3 border-b border-primary-blue border-r border-l">04</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">25-Jun-2025</td>
              <td class="py-2 px-3 border-b border-primary-blue">PT. Asiatech Manufacturing Indonesia</td>
            </tr>
            <tr class="bg-blue-100">
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r border-l">05</td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r">26-Jun-2025</td>
              <td class="py-2 px-3 border-b-0 border-primary-blue">PT. Wolhrab Indonesia</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    <div class="mb-8 mt-8">
      <div class="text-base mb-2">Total of 340 fire safety/firefighting equipment were serviced this month.</div>
      <div class="overflow-x-auto">
        <table class="min-w-full border border-primary-blue rounded-lg text-base shadow">
          <thead>
            <tr class="bg-primary-blue text-white text-center">
              <th class="py-2 px-3 border-b border-primary-blue border-r border-l font-bold"></th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Jan</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Feb</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Mar</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Apr</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">May</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold bg-green-400 text-black">Jun</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Jul</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Aug</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Sep</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Oct</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Nov</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Dec</th>
              <th class="py-2 px-3 border-b border-primary-blue font-bold">Grand Total</th>
            </tr>
          </thead>
          <tbody class="text-center font-bold">
            <tr class="bg-blue-100">
              <td class="py-2 px-3 border-b border-primary-blue border-r border-l text-left">Fire Alarm Panels</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">9</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">34</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">25</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">7</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">5</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r bg-green-100 text-black font-bold">2</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue font-extrabold">82</td>
            </tr>
            <tr class="bg-white">
              <td class="py-2 px-3 border-b border-primary-blue border-r border-l text-left">Manual Call Points</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">62</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">190</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">128</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">47</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">27</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r bg-green-100 text-black font-bold">26</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue font-extrabold">480</td>
            </tr>
            <tr class="bg-blue-100">
              <td class="py-2 px-3 border-b border-primary-blue border-r border-l text-left">Alarm Bells</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">62</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">190</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">128</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">47</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">27</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r bg-green-100 text-black font-bold">26</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue font-extrabold">480</td>
            </tr>
            <tr class="bg-white">
              <td class="py-2 px-3 border-b border-primary-blue border-r border-l text-left">Fire Extinguishers</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">307</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">888</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">443</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">256</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r">94</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r bg-green-100 text-black font-bold">260</td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b border-primary-blue font-extrabold">2248</td>
            </tr>
            <tr class="bg-blue-100">
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r border-l text-left">Fire Hosereels</td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r">43</td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r">156</td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r">85</td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r">38</td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r">19</td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r bg-green-100 text-black font-bold">26</td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b-0 border-primary-blue font-extrabold">367</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    <div class="mb-8 mt-12 page-break-before">
      <div class="font-extrabold text-xl leading-tight mb-1 underline">FIRE SAFETY REPAIR IMPAIRMENT PROCEDURE</div>
      <div class="text-base mb-4">
        In month of May Fire Safety executed N/A repair impairment procedure and maintenance of firefighting equipment areas on Batamindo Industrial Park
      </div>
      <div class="overflow-x-auto mb-4">
        <table class="min-w-full border border-primary-blue rounded-lg text-base shadow">
          <thead>
            <tr class="bg-primary-blue text-white text-center">
              <th class="py-2 px-3 border-b border-primary-blue border-r border-l font-bold"></th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Jan</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Feb</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Mar</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Apr</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">May</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold bg-green-400 text-black">Jun</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Jul</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Aug</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Sep</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Oct</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Nov</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Dec</th>
              <th class="py-2 px-3 border-b border-primary-blue font-bold">Total</th>
            </tr>
          </thead>
          <tbody class="text-center font-bold">
            <tr class="bg-blue-100">
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r border-l text-left">Repair impairment procedure and maintenance of firefighting equipment</td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r">00</td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r">00</td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r">02</td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r">02</td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r">01</td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r bg-green-100 text-black font-bold">00</td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r"></td>
              <td class="py-2 px-3 border-b-0 border-primary-blue font-extrabold">05</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full border border-primary-blue rounded-lg text-base shadow">
          <thead>
            <tr class="bg-primary-blue text-white text-center">
              <th class="py-2 px-3 border-b border-primary-blue border-r border-l font-bold">S/No</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Date</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Name of Project</th>
              <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Location</th>
              <th class="py-2 px-3 border-b border-primary-blue font-bold">Status</th>
            </tr>
          </thead>
          <tbody class="text-center font-bold">
            <tr class="bg-blue-100">
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r border-l">1</td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r">27-May-2025</td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r">BQ-OHS-25-031 Emergency Repair-To supply, replace and commissioning part of fire hosereel pipe leakage at PT. Rubycon Lot 221</td>
              <td class="py-2 px-3 border-b-0 border-primary-blue border-r">PT. Rubycon Lot 221</td>
              <td class="py-2 px-3 border-b-0 border-primary-blue"><b>31 May</b> : repair has been completed</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    <div class="mb-8 mt-12 page-break-before">
      <div class="font-extrabold text-xl leading-tight mb-1 underline">FIRE SAFETY ENGAGEMENT & TRAINING (DRILL & EXERCISES)</div>
      <div class="text-base mb-4">
        In after pandemic situation, Fire safety is currently slowly and carefully opening up to support companies maintain emergency readiness.
      </div>
      <ol class="list-decimal pl-6">
        <li class="mb-2">
          <span class="font-semibold">Drills and Exercises</span>
          <div class="overflow-x-auto mt-2 mb-4">
            <table class="min-w-full border border-primary-blue rounded-lg text-base shadow">
              <thead>
                <tr class="bg-primary-blue text-white text-center">
                  <th class="py-2 px-3 border-b border-primary-blue border-r border-l font-bold">S/No</th>
                  <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Date</th>
                  <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Location</th>
                  <th class="py-2 px-3 border-b border-primary-blue font-bold">Subject</th>
                </tr>
              </thead>
              <tbody class="text-center font-bold">
                <tr class="bg-blue-100">
                  <td class="py-2 px-3 border-b border-primary-blue border-r border-l">01</td>
                  <td class="py-2 px-3 border-b border-primary-blue border-r">13-Jun-2025</td>
                  <td class="py-2 px-3 border-b border-primary-blue border-r">PT. PC Partner Lot 5</td>
                  <td class="py-2 px-3 border-b border-primary-blue">Joint Evacuation Drill with BIC FS</td>
                </tr>
                <tr class="bg-white">
                  <td class="py-2 px-3 border-b border-primary-blue border-r border-l">02</td>
                  <td class="py-2 px-3 border-b border-primary-blue border-r">25-Jun-2025</td>
                  <td class="py-2 px-3 border-b border-primary-blue border-r">Dormitory Blok Q1-Q6 (PT. TEC Indonesia)</td>
                  <td class="py-2 px-3 border-b border-primary-blue">Joint Evacuation Drill with BIC FS</td>
                </tr>
                <tr class="bg-blue-100">
                  <td class="py-2 px-3 border-b-0 border-primary-blue border-r border-l">03</td>
                  <td class="py-2 px-3 border-b-0 border-primary-blue border-r">30-Jun-2025</td>
                  <td class="py-2 px-3 border-b-0 border-primary-blue border-r">PT. NOK Precision Component Batam</td>
                  <td class="py-2 px-3 border-b-0 border-primary-blue">Internal Drill</td>
                </tr>
              </tbody>
            </table>
          </div>
        </li>
        <li class="mb-2">
          <span class="font-semibold">Fire Training</span>
          <div class="overflow-x-auto mt-2 mb-4">
            <table class="min-w-full border border-primary-blue rounded-lg text-base shadow">
              <thead>
                <tr class="bg-primary-blue text-white text-center">
                  <th class="py-2 px-3 border-b border-primary-blue border-r border-l font-bold">S/No</th>
                  <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Date</th>
                  <th class="py-2 px-3 border-b border-primary-blue border-r font-bold">Location</th>
                  <th class="py-2 px-3 border-b border-primary-blue font-bold">Subject</th>
                </tr>
              </thead>
              <tbody class="text-center font-bold">
                <tr class="bg-blue-100">
                  <td class="py-2 px-3 border-b-0 border-primary-blue border-r border-l">01</td>
                  <td class="py-2 px-3 border-b-0 border-primary-blue border-r">13-Jun-2025</td>
                  <td class="py-2 px-3 border-b-0 border-primary-blue border-r">PT. PC Partner Lot 5</td>
                  <td class="py-2 px-3 border-b-0 border-primary-blue">Fire Extinguisher Training</td>
                </tr>
              </tbody>
            </table>
          </div>
        </li>
      </ol>
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
        fullscreenBtn.classList.toggle('hidden', isFullscreen);
        exitFullscreenBtn.classList.toggle('hidden', !isFullscreen);
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
      
      // Chart Emergency Activation WY2025
      const ctx = document.getElementById('emergencyBarChart').getContext('2d');
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['SPILLAGE', 'TECHNICAL CALL', 'OPERATIONAL STANDBY', 'NON-RESCUE', 'FIRE CALL', 'FIRE INCIDENT'],
          datasets: [{
            data: [0, 2, 3, 13, 19, 0],
            backgroundColor: '#4B7FD6',
            borderRadius: 6,
            barPercentage: 0.7,
            categoryPercentage: 0.7,
            borderSkipped: false,
            maxBarThickness: 38
          }]
        },
        options: {
          indexAxis: 'y',
          plugins: {
            legend: { display: false },
            title: {
              display: true,
              text: 'EMERGENCY ACTIVATION WY2025',
              font: { size: 24, weight: 'bold', family: 'Inter, sans-serif' },
              color: '#333',
              padding: { top: 10, bottom: 30 }
            },
            datalabels: {
              anchor: 'end',
              align: 'right',
              color: '#fff',
              font: { weight: 'bold', size: 18 }
            }
          },
          scales: {
            x: {
              beginAtZero: true,
              grid: { color: 'rgba(0,0,0,0.08)' },
              ticks: { font: { size: 14, weight: 'bold' } }
            },
            y: {
              ticks: { font: { size: 16, weight: 'bold' }, color: '#333' },
              grid: { display: false }
            }
          },
          responsive: true,
          maintainAspectRatio: false,
          layout: { padding: { left: 10, right: 10, top: 10, bottom: 10 } },
        },
        plugins: [ChartDataLabels]
      });

      // Pie Chart Script
      const pieCtx = document.getElementById('pieNonCompliance').getContext('2d');
      new Chart(pieCtx, {
        type: 'pie',
        data: {
          labels: [
            'Sum of Non Maintenance of fire safety/firefighting provision',
            'Sum of Removal of fire safety/firefighting provisions',
            'Sum of Obstruction of fire safety provision',
            'Sum of Obstruction of emergency exit'
          ],
          datasets: [{
            data: [96, 0, 4, 0],
            backgroundColor: ['#4ade80', '#fb923c', '#ef4444', '#fde047'],
            borderWidth: 1
          }]
        },
        options: {
          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                label: function(context) {
                  return context.label + ': ' + context.parsed + '%';
                }
              }
            },
            datalabels: {
              color: '#222',
              font: { weight: 'bold', size: 16 },
              formatter: function(value) {
                return value > 0 ? value + '%' : '';
              }
            }
          }
        },
        plugins: [ChartDataLabels]
      });
    });
  </script>

</body>
</html>