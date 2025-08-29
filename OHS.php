<?php
require_once __DIR__ . '/config/database.php';
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

// Ambil data PTW untuk bulan/tahun yang diminta
$stmt = $pdo->prepare('SELECT * FROM ptw_records WHERE month=? AND year=? ORDER BY display_order, contractor_name');
$stmt->execute(array($month, $year));
$ptwRecords = $stmt->fetchAll();

// Jika kosong, fallback ke bulan-tahun terbaru yang tersedia di tabel
if (!$ptwRecords) {
  $latestStmt = $pdo->query('SELECT year, month FROM ptw_records ORDER BY year DESC, month DESC LIMIT 1');
  $latest = $latestStmt ? $latestStmt->fetch() : null;
  if ($latest) {
    $year = (int)$latest['year'];
    $month = (int)$latest['month'];
    $stmt = $pdo->prepare('SELECT * FROM ptw_records WHERE month=? AND year=? ORDER BY display_order, contractor_name');
    $stmt->execute(array($month, $year));
    $ptwRecords = $stmt->fetchAll();
  }
}

// Hitung total dan data untuk chart
$totals = array('num_ptw'=>0,'general'=>0,'hot_work'=>0,'lifting'=>0,'excavation'=>0,'electrical'=>0,'work_high'=>0,'radiography'=>0,'manpower'=>0);
foreach ($ptwRecords as $r) {
  foreach ($totals as $k=>$_) { $totals[$k] += (int)$r[$k]; }
}
$labels = array_map(function($r){ return $r['contractor_name']; }, $ptwRecords);
$ptwCounts = array_map(function($r){ return (int)$r['num_ptw']; }, $ptwRecords);

// Info bulan untuk header
$monthNames = array(1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
$monthName = isset($monthNames[$month]) ? $monthNames[$month] : date('F', mktime(0,0,0,$month,1,$year));
$cutoff = sprintf('01 %s – %s %s %d', $monthName, date('t', mktime(0,0,0,$month,1,$year)), $monthName, $year);
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
      
      /* Pengaturan tabel */
      table {
        width: 100% !important;
        border-collapse: collapse !important;
        page-break-inside: avoid !important;
        font-size: 8px !important;
      }
      th, td {
        padding: 4px 5px !important;
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

      /* Override warna dan style lainnya */
      .bg-primary-blue { background-color: #0A4D9E !important; }
      .bg-blue-100 { background-color: #DBEAFE !important; }
      .bg-blue-50 { background-color: #EFF6FF !important; }
      .bg-green-200 { background-color: #BBF7D0 !important; }
      .text-white { color: #fff !important; }
      .text-black { color: #000 !important; }
      .rounded-lg { border-radius: 0 !important; }
      .shadow { box-shadow: none !important; }
      .overflow-x-auto { overflow: visible !important; }
      .font-bold { font-weight: bold !important; }
      .font-extrabold { font-weight: 800 !important; }
      h2 { color: #000 !important; }
      
      /* Perbaikan untuk grafik */
      canvas {
        width: 100% !important;
        height: auto !important;
        max-height: 300px !important;
      }
      
      /* Halaman baru untuk setiap bagian */
      .page-break {
        page-break-before: always;
      }
      
      .page-break-inside-avoid {
        page-break-inside: avoid;
      }
    }
    
    .incident-box {
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 1rem;
      box-shadow: 0 2px 8px rgba(10,77,158,0.08);
      padding: 1.5rem;
      margin-bottom: 2rem;
    }
    
    .chart-container {
      height: 300px;
      position: relative;
    }
    
    /* Styling tambahan */
    .logo-placeholder {
      background-color: #0A4D9E;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      border-radius: 4px;
      height: 35px;
    }
    
    .photo-placeholder {
      background-color: #e0e0e0;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #666;
      font-weight: bold;
      border: 1px solid #ccc;
      border-radius: 8px;
    }
    
    /* Penanda halaman untuk cetakan */
    .page-section {
      margin-bottom: 2rem;
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
    
    /* Perbaikan sidebar */
    #sidebar {
      transition: transform 0.3s ease-in-out;
      z-index: 100;
    }
    
    /* Header footer */
    header, footer {
      z-index: 50;
    }
    
    /* Sticky button */
    .sticky {
      z-index: 40;
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
    <img src="img/batamindo.png" alt="Batamindo Investment Cakrawala Logo" class="logo w-32 h-8 object-contain">
    <div class="report-info">
             <div class="main-title">Report OHSS Monthly</div>
       <div class="sub-title">BIC / OHSS-25-034-006-179 | Cut of date: <?php echo $cutoff; ?></div>
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
         BIC / OHSS-25-034-006-179 | Cut of date: <?php echo $cutoff; ?>
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
    <!-- Halaman 1: Tabel -->
    <div class="page-section">
      <h2 class="text-center text-primary-blue font-extrabold text-xl mb-2">
        1. PTW- IJIN KERJA AMAN (Permit Register)
      </h2>
      <div class="overflow-x-auto" style="width:100%">
        <table class="w-full max-w-none mx-auto border border-gray-400 rounded-lg bg-white shadow text-xs">
          <thead>
            <tr class="bg-primary-blue text-white text-center">
              <th colspan="2" class="py-1 px-2 border-b border-gray-400 border-r border-l text-base font-bold">LIST <?php echo $year; ?></th>
                             <th colspan="8" class="py-1 px-2 border-b border-gray-400 border-r text-base font-bold">MONTH OF : <?php echo strtoupper($monthName); ?></th>
              <th rowspan="2" class="py-1 px-2 border-b border-gray-400 border-r text-base font-bold align-middle">MAN-POWER</th>
            </tr>
            <tr class="bg-primary-blue text-white text-center">
              <th class="py-1 px-2 border-b border-gray-400 border-r border-l">No</th>
              <th class="py-1 px-2 border-b border-gray-400 border-r">BIC CONTRACTORS ON PROJECT</th>
              <th class="py-1 px-2 border-b border-gray-400 border-r">NUMBER OF PTW</th>
              <th class="py-1 px-2 border-b border-gray-400 border-r">GENERAL</th>
              <th class="py-1 px-2 border-b border-gray-400 border-r">HOT WORK</th>
              <th class="py-1 px-2 border-b border-gray-400 border-r">LIFTING</th>
              <th class="py-1 px-2 border-b border-gray-400 border-r">EXCAVATION</th>
              <th class="py-1 px-2 border-b border-gray-400 border-r">ELECTRICAL</th>
              <th class="py-1 px-2 border-b border-gray-400 border-r">WORK HIGH</th>
              <th class="py-1 px-2 border-b border-gray-400 border-r">RADIOGRAPHY</th>
            </tr>
          </thead>
          <tbody class="text-xs">
            <?php foreach ($ptwRecords as $i=>$row): ?>
            <tr class="<?php echo $i % 2 === 0 ? 'bg-blue-50' : 'bg-white'; ?> text-center font-bold">
              <td class="py-1 px-2 border-b border-gray-400 border-r border-l"><?php echo $i+1; ?></td>
              <td class="py-1 px-2 border-b border-gray-400 border-r text-left"><?php echo htmlspecialchars($row['contractor_name']); ?></td>
              <td class="py-1 px-2 border-b border-gray-400 border-r"><?php echo (int)$row['num_ptw']; ?></td>
              <td class="py-1 px-2 border-b border-gray-400 border-r"><?php echo (int)$row['general']; ?></td>
              <td class="py-1 px-2 border-b border-gray-400 border-r"><?php echo (int)$row['hot_work']; ?></td>
              <td class="py-1 px-2 border-b border-gray-400 border-r"><?php echo (int)$row['lifting']; ?></td>
              <td class="py-1 px-2 border-b border-gray-400 border-r"><?php echo (int)$row['excavation']; ?></td>
              <td class="py-1 px-2 border-b border-gray-400 border-r"><?php echo (int)$row['electrical']; ?></td>
              <td class="py-1 px-2 border-b border-gray-400 border-r"><?php echo (int)$row['work_high']; ?></td>
              <td class="py-1 px-2 border-b border-gray-400 border-r"><?php echo (int)$row['radiography']; ?></td>
              <td class="py-1 px-2 border-b border-gray-400"><?php echo (int)$row['manpower']; ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="bg-green-200 text-center font-bold">
              <td colspan="2" class="py-1 px-2 border-b-0 border-gray-400 border-r border-l text-right">Total</td>
              <td class="py-1 px-2 border-b-0 border-gray-400 border-r"><?php echo $totals['num_ptw']; ?></td>
              <td class="py-1 px-2 border-b-0 border-gray-400 border-r"><?php echo $totals['general']; ?></td>
              <td class="py-1 px-2 border-b-0 border-gray-400 border-r"><?php echo $totals['hot_work']; ?></td>
              <td class="py-1 px-2 border-b-0 border-gray-400 border-r"><?php echo $totals['lifting']; ?></td>
              <td class="py-1 px-2 border-b-0 border-gray-400 border-r"><?php echo $totals['excavation']; ?></td>
              <td class="py-1 px-2 border-b-0 border-gray-400 border-r"><?php echo $totals['electrical']; ?></td>
              <td class="py-1 px-2 border-b-0 border-gray-400 border-r"><?php echo $totals['work_high']; ?></td>
              <td class="py-1 px-2 border-b-0 border-gray-400 border-r"><?php echo $totals['radiography']; ?></td>
              <td class="py-1 px-2 border-b-0 border-gray-400"><?php echo $totals['manpower']; ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Halaman 2: Grafik -->
    <div class="page-break page-break-inside-avoid">
      <div class="w-full bg-white rounded-lg shadow p-4" style="margin-top:0;">
        <h2 class="text-center text-lg font-bold mb-4">2. TOTAL NUMBER PTW STATISTIC SUBMISSION</h2>
        <div class="chart-container" style="width:100%;">
          <canvas id="ptwBarChart" style="width:100% !important; height:300px !important; max-width:none !important;"></canvas>
        </div>
      </div>
    </div>

    <!-- Halaman 3: Laporan Insiden -->
    <?php
    $incidents = $pdo->query('SELECT * FROM ohs_incidents WHERE status = "published" ORDER BY incident_date DESC, id DESC LIMIT 2')->fetchAll();
    if ($incidents && count($incidents) > 0):
      foreach ($incidents as $idx => $incident):
    ?>
    <div class="page-break page-break-inside-avoid">
      <div class="incident-box bg-white rounded-xl p-6 mt-8">
        <div class="font-extrabold text-lg mb-2">
          <?php echo ($idx === 0) ? '3.' : '4.'; ?> INCIDENT & ACCIDENT REPORT & SHARING LESSON LEARNT (if any)
        </div>
        <div class="font-bold text-xl mb-1">Lesson Learned: <?php echo htmlspecialchars($incident['title']); ?></div>
        <div class="italic text-base mb-3">Date: <?php echo date('d F Y', strtotime($incident['incident_date'])); ?> | Time: <?php echo htmlspecialchars($incident['incident_time']); ?> WIB</div>
        <div class="flex flex-col md:flex-row gap-4">
          <!-- Kiri: Foto -->
          <div class="flex flex-col gap-2 md:w-1/2">
            <?php if ($incident['map_image_path']): ?>
              <img src="<?php echo $incident['map_image_path']; ?>" alt="Incident Map" class="w-full h-72 object-contain rounded shadow" />
            <?php else: ?>
              <div class="photo-placeholder w-full h-72 rounded">INCIDENT MAP</div>
            <?php endif; ?>
            <?php if ($incident['photo_image_path']): ?>
              <img src="<?php echo $incident['photo_image_path']; ?>" alt="Photo Evidence" class="w-full h-72 object-contain rounded shadow" />
            <?php else: ?>
              <div class="photo-placeholder w-full h-72 rounded">PHOTO EVIDENCE</div>
            <?php endif; ?>
          </div>
          <!-- Kanan: Ringkasan & Detail -->
          <div class="md:w-1/2 flex flex-col gap-2">
            <div>
              <div class="font-bold mb-1">Incident Summary:</div>
              <ul class="list-disc ml-5 text-sm">
                <li><b>Who:</b> <?php echo htmlspecialchars($incident['who_name']); ?> (NPK: <?php echo htmlspecialchars($incident['who_npk']); ?>)</li>
                <li><b>Summary:</b> <?php echo nl2br(htmlspecialchars($incident['summary'])); ?></li>
                <li><b>Result:</b> <?php echo nl2br(htmlspecialchars($incident['result'])); ?></li>
              </ul>
            </div>
            <div>
              <div class="font-bold mb-1">Root Causes:</div>
              <ol class="list-decimal ml-5 text-sm">
                <?php foreach (explode("\n", $incident['root_causes']) as $cause): ?>
                  <?php if (trim($cause)): ?><li><?php echo htmlspecialchars($cause); ?></li><?php endif; ?>
                <?php endforeach; ?>
              </ol>
            </div>
            <div>
              <div class="font-bold mb-1">Key Takeaways:</div>
              <ul class="list-disc ml-5 text-sm">
                <?php foreach (explode("\n", $incident['key_takeaways']) as $take): ?>
                  <?php if (trim($take)): ?><li><?php echo htmlspecialchars($take); ?></li><?php endif; ?>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </div>
        <!-- Corrective Actions -->
        <div class="mt-4">
          <div class="font-bold mb-1">✔️ Corrective Actions:</div>
          <ul class="text-sm ml-5">
            <?php foreach (explode("\n", $incident['corrective_actions']) as $action): ?>
              <?php if (trim($action)): ?><li><?php echo htmlspecialchars($action); ?></li><?php endif; ?>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
    <?php endforeach;
    else: ?>
    <div class="page-break page-break-inside-avoid">
      <div class="incident-box bg-white rounded-xl p-6 mt-8">
        <div class="w-full text-center py-12 text-gray-500 text-lg font-semibold">
          <i class="fas fa-info-circle text-2xl mr-2"></i> No incident reported this month
        </div>
      </div>
    </div>
    <?php endif; ?>
  </main>

  <footer class="bg-header-footer-bg text-white text-center py-2 mt-3 text-xs">
    <p>&copy; 2025 Batamindo Investment Cakrawala. All rights reserved</p>
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Sembunyikan loader setelah 1.5 detik
      setTimeout(function() {
        const loaderBg = document.getElementById('loader-bg');
        if (loaderBg) {
          loaderBg.style.opacity = '0';
          setTimeout(function() {
            loaderBg.style.display = 'none';
            // Tampilkan konten utama dengan efek fade in
            const mainContent = document.querySelector('.fadein');
            if (mainContent) {
              mainContent.classList.add('show');
            }
          }, 500);
        }
      }, 1500);
      
      // Fullscreen toggle logic
      const fullscreenBtn = document.getElementById('fullscreenBtn');
      const exitFullscreenBtn = document.getElementById('exitFullscreenBtn');
      
      fullscreenBtn.addEventListener('click', () => {
        const docElm = document.documentElement;
        if (docElm.requestFullscreen) {
          docElm.requestFullscreen();
        } else if (docElm.mozRequestFullScreen) { /* Firefox */
          docElm.mozRequestFullScreen();
        } else if (docElm.webkitRequestFullscreen) { /* Chrome, Safari & Opera */
          docElm.webkitRequestFullscreen();
        } else if (docElm.msRequestFullscreen) { /* IE/Edge */
          docElm.msRequestFullscreen();
        }
      });
      
      exitFullscreenBtn.addEventListener('click', () => {
        if (document.exitFullscreen) {
          document.exitFullscreen();
        } else if (document.mozCancelFullScreen) { /* Firefox */
          document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) { /* Chrome, Safari and Opera */
          document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) { /* IE/Edge */
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

      // Sidebar toggle logic
      const sidebar = document.getElementById('sidebar');
      const menuBtn = document.getElementById('menuBtn');
      const sidebarBackBtn = document.getElementById('sidebarBackBtn');
      
      menuBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        sidebar.classList.remove('-translate-x-full');
      });
      
      document.addEventListener('click', function(e) {
        if (!sidebar.contains(e.target) && !menuBtn.contains(e.target)) {
          sidebar.classList.add('-translate-x-full');
        }
      });
      
      sidebarBackBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        sidebar.classList.add('-translate-x-full');
      });
      
      // Grafik PTW Statistic
      const ctx = document.getElementById('ptwBarChart');
      if (ctx) {
        new Chart(ctx, {
          type: 'bar',
          data: {
                         labels: <?php echo json_encode($labels); ?>,
             datasets: [{
               label: 'Number of PTW',
               data: <?php echo json_encode($ptwCounts); ?>,
               backgroundColor: 'rgba(37, 99, 235, 0.7)',
               borderColor: 'rgba(37, 99, 235, 1)',
               borderWidth: 1
             }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { display: false },
              title: { display: false }
            },
            scales: {
              x: {
                ticks: { 
                  font: { 
                    size: 9
                  }, 
                  maxRotation: 45,
                  minRotation: 45,
                  autoSkip: false
                },
                grid: { display: false }
              },
              y: {
                beginAtZero: true,
                ticks: { stepSize: 2 },
                grid: { color: '#e5e7eb' }
              }
            },
            layout: {
              padding: {
                left: 10,
                right: 10,
                top: 10,
                bottom: 20
              }
            }
          }
        });
      }
    });

    // Script untuk tanggal dinamis di header cetak
    window.onbeforeprint = function() {
      const now = new Date();
      const options = { year: '2-digit', month: 'numeric', day: 'numeric', hour: 'numeric', minute: 'numeric', hour12: true };
      const formattedDateTime = now.toLocaleString('en-US', options).replace(',', '');
      document.getElementById('print-datetime').textContent = formattedDateTime;
      document.getElementById('print-datetime-spacer').textContent = formattedDateTime;
    };
  </script>

</body>
</html>