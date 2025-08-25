<!DOCTYPE html>
<html lang="id">
<head>
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
      .page-break-before {
        page-break-before: always;
      }
      
      /* Pengaturan tabel */
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

      /* Override warna dan style lainnya */
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

    /* Tambahan style print agar grid mapping tidak pecah saat export */
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
          <tr class="bg-blue-100 text-center font-bold">
            <td class="py-2 px-3 align-top border-t border-b border-gray-400 border-r border-l">01</td>
            <td class="py-2 px-3 text-left font-semibold align-top border-t border-b border-gray-400 border-r">Expand Cameras at Commercial Area (Pujasera Area)
              <div class="font-normal">Was deployed 6 from 6 camera.</div>
            </td>
            <td class="py-2 px-3 align-top border-t border-b border-gray-400 border-r">
              <span class="bg-green-400 text-white px-2 py-1 rounded font-bold text-center block">Done</span>
            </td>
            <td class="py-2 px-3 align-top border-t border-b border-gray-400">Cumulative: 100%</td>
          </tr>
          <tr class="bg-blue-50 text-center font-bold">
            <td class="py-2 px-3 align-top border-b border-gray-400 border-r border-l">02</td>
            <td class="py-2 px-3 text-left font-semibold align-top border-b border-gray-400 border-r">Expand Cameras at Resident Area (Shophouse)
              <div class="font-normal">Was deployed 6 from 6 camera, one camera will be installing in April</div>
            </td>
            <td class="py-2 px-3 align-top border-b border-gray-400 border-r">
              <span class="bg-green-400 text-white px-2 py-1 rounded font-bold text-center block">Done</span>
            </td>
            <td class="py-2 px-3 align-top border-b border-gray-400">Cumulative: 100%<br>Increase this month: 25%</td>
          </tr>
          <tr class="bg-blue-100 text-center font-bold">
            <td class="py-2 px-3 align-top border-b border-gray-400 border-r border-l">03</td>
            <td class="py-2 px-3 text-left font-semibold align-top border-b border-gray-400 border-r">Expand Cameras at Commercial Area (Panasera Area)</td>
            <td class="py-2 px-3 align-top border-b border-gray-400 border-r">
              <span class="bg-yellow-300 text-black px-2 py-1 rounded font-bold text-center block">In progress</span>
            </td>
            <td class="py-2 px-3 align-top border-b border-gray-400">Cumulative: 40%<br>Increase this month: 0%</td>
          </tr>
          <tr class="bg-blue-50 text-center font-bold">
            <td class="py-2 px-3 align-top border-b border-gray-400 border-r border-l">04</td>
            <td class="py-2 px-3 text-left font-semibold align-top border-b border-gray-400 border-r">Upgrading Surveillance System at CCTV Room OPS
              <div class="font-normal">Was installed server rack, waiting other part and equipment</div>
            </td>
            <td class="py-2 px-3 align-top border-b border-gray-400 border-r">
              <span class="bg-yellow-300 text-black px-2 py-1 rounded font-bold text-center block">In Progress</span>
            </td>
            <td class="py-2 px-3 align-top border-b border-gray-400">Cumulative: 70%<br>Increase this month: 20%</td>
        </div>
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
          <tr class="bg-blue-100 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l text-left">Overall CCTV Operational Readiness Performance</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center align-middle">100%</td>
            <td class="py-2 px-3 border-b border-gray-400 text-center align-middle"></td>
          </tr>
          <tr class="bg-blue-100 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l text-left">Overall CCTV Preventive Maintenance (PM) Performance</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center align-middle">100%</td>
            <td class="py-2 px-3 border-b border-gray-400 text-center align-middle">100%</td>
          </tr>
          <tr class="bg-blue-100 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l text-left">Overall CCTV Corrective Maintenance (CM) Performance</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center align-middle">100%</td>
            <td class="py-2 px-3 border-b border-gray-400 text-center align-middle">100%</td>
          </tr>
          <tr class="bg-blue-200 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l text-left align-top" rowspan="2">Utilisation of ISSS – Guard Tour Patrol</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">QR Checkpoint Scanned</td>
            <td class="py-2 px-3 border-b border-gray-400 text-center">5741</td>
          </tr>
          <tr class="bg-blue-200 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">Patrol Hours</td>
            <td class="py-2 px-3 border-b border-gray-400 text-center">833</td>
          </tr>
        </tbody>
      </table>
    </div>
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
          <tr class="bg-blue-100 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l" colspan="4">a. Deployed CCTV Cameras Readiness</td>
          </tr>
          <tr class="bg-blue-50">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l pl-8 font-semibold" rowspan="2">i. CCTV Camera (IP Type)</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center font-semibold">Operational<br><span class='text-green-600 font-bold'>&#9650;152</span></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center font-semibold">Non-Operational<br>00</td>
            <td class="py-2 px-3 border-b border-gray-400 text-center font-semibold" rowspan="2">100%</td>
          </tr>
          <tr class="bg-blue-50">
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center font-semibold">&#9650;152</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center font-semibold">00</td>
          </tr>
          <tr class="bg-blue-50">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l pl-8 font-semibold" rowspan="2">ii. CCTV Fixed Camera (Analog Type)</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center font-semibold">Operational<br>11</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center font-semibold">Non-Operational<br>00</td>
            <td class="py-2 px-3 border-b border-gray-400 text-center font-semibold" rowspan="2">100%</td>
          </tr>
          <tr class="bg-blue-50">
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center font-semibold">11</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center font-semibold">00</td>
          </tr>
          <tr class="bg-blue-100">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l font-bold italic" colspan="4">Highlight</td>
          </tr>
          <tr class="bg-blue-50">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l text-left italic" colspan="4">
              <ul class="list-disc pl-5">
                <li>Surveillance system deployed based existing and future improvements</li>
                <li>Currently migration all of camera system from analog to IP Camera system, this improvement put in phase</li>
              </ul>
            </td>
          </tr>
          <tr class="bg-blue-100 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l" colspan="4">b. Total Portable CCTV Cameras</td>
          </tr>
          <tr class="bg-blue-50">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l pl-8 font-semibold">i. Portable CCTV Cameras</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">Deployed<br>00</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">Standby<br>00</td>
            <td class="py-2 px-3 border-b border-gray-400 text-center"><span class="text-blue-600 font-bold">0%</span></td>
          </tr>
          <tr class="bg-blue-100 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l">c. Preventive Maintenance</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">Scheduled<br>01</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">Completed<br>01</td>
            <td class="py-2 px-3 border-b border-gray-400 text-center"><span class="text-blue-600 font-bold">0%</span> / 100%</td>
          </tr>
          <tr class="bg-blue-50 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l">d. Corrective Maintenance</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">No. of Faults<br>15</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">Completed<br>15</td>
            <td class="py-2 px-3 border-b border-gray-400 text-center"><span class="text-blue-600 font-bold">0%</span> / 100%</td>
          </tr>
          <tr class="bg-blue-100 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l">e. CCTV Footage Request</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">No. of Request<br>11</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">Completed<br>11</td>
            <td class="py-2 px-3 border-b border-gray-400 text-center"><span class="text-blue-600 font-bold">0%</span> / 100%</td>
          </tr>
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
          <tr class="bg-blue-100 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l">Total Number Of Patrol Session Conducted</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">335</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">299</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">283</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">306</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">327</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-green-100 text-black text-center font-bold">311</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 bg-blue-50"></td>
          </tr>
          <tr class="bg-blue-50 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l">Total Patrol Duration Conducted (Hours)</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">732</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">687</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">673</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">749</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">769</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-green-100 text-black text-center font-bold">833</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 bg-blue-50"></td>
          </tr>
          <tr class="bg-blue-100 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l">Total QR Checkpoints Scanned</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">5693</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">5392</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">4704</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">6102</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">5616</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-green-100 text-black text-center font-bold">5741</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 bg-blue-50"></td>
          </tr>
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
          <tr class="bg-blue-100 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l">Team A – Patrol Truck</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">69</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">21</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">44</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">62</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">52</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-green-100 text-black text-center font-bold">72</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 bg-blue-50"></td>
          </tr>
          <tr class="bg-blue-50 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l">Team A – Patrol Bike</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">40</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">41</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">42</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">47</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">53</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-green-100 text-black text-center font-bold">41</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 bg-blue-50"></td>
          </tr>
          <tr class="bg-blue-100 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l">Team B – Patrol Truck</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">83</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">43</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">58</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">56</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">42</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-green-100 text-black text-center font-bold">57</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 bg-blue-50"></td>
          </tr>
          <tr class="bg-blue-50 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l">Team B – Patrol Bike</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">0</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">0</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">16</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">36</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">35</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-green-100 text-black text-center font-bold">50</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 bg-blue-50"></td>
          </tr>
          <tr class="bg-blue-100 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l">Team C – Patrol Truck</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">86</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">40</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">69</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">114</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">90</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-green-100 text-black text-center font-bold">112</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 bg-blue-50"></td>
          </tr>
          <tr class="bg-blue-50 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l">Team C – Patrol Bike</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">6</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">1</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">1</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">11</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">9</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-green-100 text-black text-center font-bold">5</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 bg-blue-50"></td>
          </tr>
          <tr class="bg-blue-100 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l">Team D – Patrol Truck</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">62</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">21</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">74</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">79</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">94</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-green-100 text-black text-center font-bold">96</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 bg-blue-50"></td>
          </tr>
          <tr class="bg-blue-50 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l">Team D – Patrol Bike</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">28</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">39</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">28</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">20</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">14</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-green-100 text-black text-center font-bold">53</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 bg-blue-50"></td>
          </tr>
          <tr class="bg-blue-100 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l">Powerhouse</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">343</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">332</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">337</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">320</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">377</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-green-100 text-black text-center font-bold">342</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 bg-blue-50"></td>
          </tr>
          <tr class="bg-primary-blue text-white font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l">Total</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">717</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">538</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">669</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">745</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">766</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-green-100 text-black text-center font-bold">828</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 bg-blue-50"></td>
          </tr>
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
          <tr class="bg-blue-100 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l">Team A – Patrol Truck</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">622</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">833</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">604</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">1036</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">429</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-green-100 text-black text-center font-bold">1076</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 bg-blue-50"></td>
          </tr>
          <tr class="bg-blue-50 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l">Team A – Patrol Bike</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">1093</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">634</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">1265</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">725</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">440</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-green-100 text-black text-center font-bold">312</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 bg-blue-50"></td>
          </tr>
          <tr class="bg-blue-100 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l">Team B – Patrol Truck</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">1166</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">1348</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">156</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">672</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">399</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-green-100 text-black text-center font-bold">825</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 bg-blue-50"></td>
          </tr>
          <tr class="bg-blue-50 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l">Team B – Patrol Bike</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">0</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">0</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">486</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">905</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">634</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-green-100 text-black text-center font-bold">302</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 bg-blue-50"></td>
          </tr>
          <tr class="bg-blue-100 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l">Team C – Patrol Truck</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">644</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">959</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">519</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">696</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">1063</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-green-100 text-black text-center font-bold">1391</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 bg-blue-50"></td>
          </tr>
          <tr class="bg-blue-50 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l">Team C – Patrol Bike</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">308</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">28</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">29</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">364</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">196</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-green-100 text-black text-center font-bold">53</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 bg-blue-50"></td>
          </tr>
          <tr class="bg-blue-100 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l">Team D – Patrol Truck</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">605</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">126</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">696</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">920</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">702</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-green-100 text-black text-center font-bold"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 bg-blue-50"></td>
          </tr>
          <tr class="bg-blue-50 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l">Team D – Patrol Bike</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">319</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">726</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">145</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">348</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">665</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-green-100 text-black text-center font-bold">336</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 bg-blue-50"></td>
          </tr>
          <tr class="bg-blue-100 font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l">Powerhouse</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">936</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">738</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">804</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">660</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">870</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-green-100 text-black text-center font-bold">744</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 bg-blue-50"></td>
          </tr>
          <tr class="bg-primary-blue text-white font-bold">
            <td class="py-2 px-3 border-b border-gray-400 border-r border-l">Total</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">5693</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">5392</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">4704</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">6102</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r text-center">5616</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-green-100 text-black text-center font-bold">5741</td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 border-r bg-blue-50"></td>
            <td class="py-2 px-3 border-b border-gray-400 bg-blue-50"></td>
          </tr>
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
        <!-- Gambar 1-10 dibungkus agar tidak terpotong saat print -->
        <div class="flex flex-col items-center bg-white rounded-xl shadow-md border border-gray-200 p-4 mb-2 print:break-inside-avoid">
          <img src="img/map.png" alt="BIP Parking Areas" class="w-full max-w-xs h-auto rounded-lg border mb-2 print:max-w-full print:h-auto">
          <div class="font-extrabold text-blue-900 text-base text-center mb-1">1. BIP Parking Areas</div>
        </div>
        <div class="flex flex-col items-center bg-white rounded-xl shadow-md border border-gray-200 p-4 mb-2 print:break-inside-avoid">
          <img src="img/map.png" alt="Multi Purpose Hall (MPH)" class="w-full max-w-xs h-auto rounded-lg border mb-2 print:max-w-full print:h-auto">
          <div class="font-extrabold text-blue-900 text-base text-center mb-1">2. Multi Purpose Hall (MPH)</div>
        </div>
        <div class="flex flex-col items-center bg-white rounded-xl shadow-md border border-gray-200 p-4 mb-2 print:break-inside-avoid">
          <img src="img/map.png" alt="Community Centre" class="w-full max-w-xs h-auto rounded-lg border mb-2 print:max-w-full print:h-auto">
          <div class="font-extrabold text-blue-900 text-base text-center mb-1">3. Community Centre</div>
        </div>
        <div class="flex flex-col items-center bg-white rounded-xl shadow-md border border-gray-200 p-4 mb-2 print:break-inside-avoid">
          <img src="img/map.png" alt="Panasera Areas" class="w-full max-w-xs h-auto rounded-lg border mb-2 print:max-w-full print:h-auto">
          <div class="font-extrabold text-blue-900 text-base text-center mb-1">4. Panasera Areas</div>
        </div>
        <div class="flex flex-col items-center bg-white rounded-xl shadow-md border border-gray-200 p-4 mb-2 print:break-inside-avoid">
          <img src="img/map.png" alt="Power House #01" class="w-full max-w-xs h-auto rounded-lg border mb-2 print:max-w-full print:h-auto">
          <div class="font-extrabold text-blue-900 text-base text-center mb-1">5. Power House #01</div>
        </div>
        <div class="flex flex-col items-center bg-white rounded-xl shadow-md border border-gray-200 p-4 mb-2 print:break-inside-avoid">
          <img src="img/map.png" alt="Power House #4" class="w-full max-w-xs h-auto rounded-lg border mb-2 print:max-w-full print:h-auto">
          <div class="font-extrabold text-blue-900 text-base text-center mb-1">6. Power House #4</div>
        </div>
        <div class="flex flex-col items-center bg-white rounded-xl shadow-md border border-gray-200 p-4 mb-2 print:break-inside-avoid">
          <img src="img/map.png" alt="Power House #03" class="w-full max-w-xs h-auto rounded-lg border mb-2 print:max-w-full print:h-auto">
          <div class="font-extrabold text-blue-900 text-base text-center mb-1">7. Power House #03</div>
        </div>
        <div class="flex flex-col items-center bg-white rounded-xl shadow-md border border-gray-200 p-4 mb-2 print:break-inside-avoid">
          <img src="img/map.png" alt="STP WWTP areas" class="w-full max-w-xs h-auto rounded-lg border mb-2 print:max-w-full print:h-auto">
          <div class="font-extrabold text-blue-900 text-base text-center mb-1">8. STP WWTP areas</div>
        </div>
        <div class="flex flex-col items-center bg-white rounded-xl shadow-md border border-gray-200 p-4 mb-2 print:break-inside-avoid">
          <img src="img/map.png" alt="WTP area" class="w-full max-w-xs h-auto rounded-lg border mb-2 print:max-w-full print:h-auto">
          <div class="font-extrabold text-blue-900 text-base text-center mb-1">9. WTP area</div>
        </div>
        <div class="flex flex-col items-center bg-white rounded-xl shadow-md border border-gray-200 p-4 mb-2 print:break-inside-avoid">
          <img src="img/map.png" alt="Dormitory block" class="w-full max-w-xs h-auto rounded-lg border mb-2 print:max-w-full print:h-auto">
          <div class="font-extrabold text-blue-900 text-base text-center mb-1">10. Dormitory block</div>
        </div>
        <!-- Gambar 11-12 tetap di luar pembungkus agar bisa lanjut ke halaman berikutnya jika perlu -->
        <div class="flex flex-col items-center bg-white rounded-xl shadow-md border border-gray-200 p-4 mb-2 print:break-inside-avoid">
          <img src="img/map.png" alt="OPS Area" class="w-full max-w-xs h-auto rounded-lg border mb-2 print:max-w-full print:h-auto">
          <div class="font-extrabold text-blue-900 text-base text-center mb-1">11. OPS Area</div>
        </div>
        <div class="flex flex-col items-center bg-white rounded-xl shadow-md border border-gray-200 p-4 mb-2 print:break-inside-avoid">
          <img src="img/map.png" alt="Wisma Batamindo" class="w-full max-w-xs h-auto rounded-lg border mb-2 print:max-w-full print:h-auto">
            <div class="font-extrabold text-blue-900 text-base text-center mb-1">12. Wisma Batamindo</div>
        </div>
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