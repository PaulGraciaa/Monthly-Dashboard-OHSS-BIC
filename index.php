<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>OHSS Performance Dashboard</title>

  <!-- Font Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

  <!-- Swiper JS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <script>
    // Register Chart.js DataLabels plugin
    Chart.register(ChartDataLabels);
    
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
    @keyframes blinkRedGray {
      0%, 100% { color: #f40000; }
      50% { color: #2c08d1; }
    }
    .animate-blink {
      animation: blinkRedGray 1s infinite;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    @keyframes blinkBorderRedBlue {
      0%, 100% { border-color: #f40000; }
      50% { border-color: #2c08d1; }
    }
    .animate-blink-border {
      animation: blinkBorderRedBlue 1s infinite;
      border-style: solid;
    }

    .kpi-card {
      transition: all 0.3s ease;
    }

    .kpi-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .chart-container {
      position: relative;
      overflow: hidden;
    }

    .chart-container canvas {
      transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    #leadingTab, #laggingTab {
      transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      transform: translateY(0);
    }

    #leadingTab:hover, #laggingTab:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 8px rgba(10, 77, 158, 0.15);
    }

    #tabIndicator {
      box-shadow: 0 2px 4px rgba(10, 77, 158, 0.3);
    }
    
    .fadein {
      opacity: 0;
      transition: opacity 1s;
    }
    .fadein.show {
      opacity: 1;
    }
    
    .life-image-hover {
      transition: transform 0.3s ease;
    }
    .life-image-hover:hover {
      transform: scale(1.05);
    }
    
    .activity-card {
      transition: all 0.3s ease;
    }
    .activity-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 20px rgba(0,0,0,0.15);
    }
  </style>
</head>
<body class="bg-background-color text-gray-800 font-sans min-h-screen flex flex-col">

<?php
session_start();
require_once 'config/database.php';

// Get dashboard statistics
$stats = $pdo->query("SELECT * FROM dashboard_stats WHERE is_active = 1 ORDER BY display_order")->fetchAll();

// Get KPI Leading data
$kpiLeading = $pdo->query("SELECT * FROM kpi_leading ORDER BY indicator_name")->fetchAll();

// Get KPI Lagging data
$kpiLagging = $pdo->query("SELECT * FROM kpi_lagging ORDER BY indicator_name")->fetchAll();

// Get activities
try {
    $activities = $pdo->query("SELECT * FROM activities WHERE status = 'active' ORDER BY activity_date DESC")->fetchAll();
} catch (Exception $e) {
    $activities = [];
}

// Get news
$news = $pdo->query("SELECT * FROM news WHERE status = 'published' ORDER BY publish_date DESC")->fetchAll();

// Get configuration
$config = [];
$configData = $pdo->query("SELECT * FROM config")->fetchAll();
foreach ($configData as $item) {
    $config[$item['config_key']] = $item['config_value'];
}

// Get Life Saving Rules & BASCOM
$lsr_bascom = $pdo->query("SELECT * FROM life_saving_rules ORDER BY id DESC")->fetchAll();
?>

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
  <main class="main-content flex-1 fadein show">
    <div class="container mx-auto px-4 max-w-7xl">
      <!-- KPI Cards (horizontal, lebih kecil) -->
      <div class="flex flex-row gap-3 mb-3">
        <?php if (!empty($stats)): ?>
          <?php foreach ($stats as $stat): ?>
          <div class="bg-white p-2 rounded-xl text-center flex-1 flex flex-col justify-center items-center min-w-[70px] h-20">
            <div class="text-lg font-extrabold text-primary-blue tracking-wide flex items-center gap-2">
              <i class="<?php echo $stat['stat_icon']; ?> text-primary-blue text-sm"></i> <?php echo $stat['stat_value']; ?>
            </div>
            <div class="mt-1 text-black-600 text-xs font-semibold"><strong><?php echo $stat['stat_name']; ?></strong></div>
          </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="w-full text-center text-gray-400 py-6">Tidak ada data statistik dashboard.</div>
        <?php endif; ?>
      </div>
      
      <!-- KPI, Chart, Golden Rules dalam satu grid utama -->
      <section class="grid grid-cols-1 lg:grid-cols-12 gap-3 my-1 items-stretch">
        <!-- Kolom 1: KPI (diperpanjang) -->
        <div class="lg:col-span-6 bg-white p-2 rounded-xl shadow-md border border-blue-100 flex flex-col kpi-card min-h-[320px]">
          <div class="flex items-center gap-2 mb-2 border-b border-blue-100 pb-1">
            <i class="fas fa-chart-bar text-sm text-primary-blue"></i>
            <h3 class="font-bold text-xs mb-0.5 text-primary-blue">KPI OHSS</h3>
          </div>
          <!-- Tab Navigation -->
          <div class="flex mb-2 border-b border-gray-200 relative">
            <div class="absolute bottom-0 left-0 h-0.5 bg-primary-blue transition-all duration-500 ease-in-out" id="tabIndicator" style="width: 50%;"></div>
            <button id="leadingTab" class="flex-1 py-1 px-3 text-xs font-semibold text-primary-blue border-b-2 border-transparent bg-blue-50 rounded-t-lg transition-all duration-500 ease-in-out relative z-10">
              <strong>Leading Indicators</strong>
            </button>
            <button id="laggingTab" class="flex-1 py-1 px-3 text-xs font-semibold text-gray-500 border-b-2 border-transparent hover:text-primary-blue transition-all duration-500 ease-in-out relative z-10">
              <strong>Lagging Indicators</strong>
            </button>
          </div>
          <div class="flex-1 flex flex-col justify-end relative chart-container">
            <?php if (empty($kpiLeading) && empty($kpiLagging)): ?>
              <div class="absolute inset-0 flex items-center justify-center text-gray-400">Tidak ada data KPI.</div>
            <?php endif; ?>
            <canvas id="kpiLeadingChart" width="400" height="280" style="width:100%;height:280px;position:absolute;top:0;left:0;opacity:1;transition:opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1);"></canvas>
            <canvas id="kpiLaggingChart" width="400" height="280" style="width:100%;height:280px;position:absolute;top:0;left:0;opacity:0;transition:opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1);"></canvas>
          </div>
        </div>
        
        <!-- Kolom 2: Life Saving Rules (diperkecil) -->
        <div class="lg:col-span-3 bg-white p-2 rounded-xl shadow-md border border-blue-100 flex flex-col kpi-card min-h-[320px]">
          <div class="flex items-center gap-2 mb-2 border-b border-blue-100 pb-1">
            <i class="fas fa-shield-alt text-sm text-primary-blue"></i>
            <h3 class="font-bold text-xs mb-0.5 text-primary-blue"><strong>Life Saving Rules & BASCOM</strong></h3>
          </div>
          <div class="flex-1 flex flex-col justify-center items-center">
            <?php if (!empty($lsr_bascom)): ?>
              <div id="lsrCarousel" class="relative w-full flex flex-col items-center h-72">
                <?php foreach ($lsr_bascom as $idx => $item): ?>
                  <div class="absolute inset-0 flex flex-col items-center justify-center transition-opacity duration-700 <?php echo $idx === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0'; ?> lsr-slide" data-index="<?= $idx ?>">
                    <?php if (!empty($item['gambar'])): ?>
                      <img src="uploads/life_saving_rules/<?= htmlspecialchars($item['gambar']) ?>" class="h-56 object-contain mx-auto mb-2 life-image-hover" />
                    <?php endif; ?>
                    <h4 class="text-xs font-bold text-primary-blue mb-1"><?= htmlspecialchars($item['judul']) ?></h4>
                    <p class="text-[10px] text-gray-600 leading-tight mb-2"><?= htmlspecialchars($item['deskripsi']) ?></p>
                    <div class="flex justify-center gap-1 mt-2">
                      <?php foreach ($lsr_bascom as $dotIdx => $dotItem): ?>
                        <button class="lsr-dot w-2 h-2 rounded-full bg-primary-blue opacity-<?php echo $dotIdx === $idx ? '100' : '40'; ?> transition-opacity duration-300" data-index="<?= $dotIdx ?>" aria-label="Slide <?= $dotIdx + 1 ?>"></button>
                      <?php endforeach; ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
              <script>
                const lsrSlides = document.querySelectorAll('.lsr-slide');
                const lsrDots = document.querySelectorAll('.lsr-dot');
                let lsrCurrent = 0;
                function showLsrSlide(idx) {
                  lsrSlides.forEach((slide, i) => {
                    slide.classList.toggle('opacity-100', i === idx);
                    slide.classList.toggle('opacity-0', i !== idx);
                    slide.classList.toggle('z-10', i === idx);
                    slide.classList.toggle('z-0', i !== idx);
                  });
                  lsrDots.forEach((dot, i) => {
                    dot.classList.toggle('opacity-100', i === idx);
                    dot.classList.toggle('opacity-40', i !== idx);
                  });
                  lsrCurrent = idx;
                }
                lsrDots.forEach(dot => {
                  dot.addEventListener('click', function() {
                    showLsrSlide(parseInt(this.dataset.index));
                  });
                });
                setInterval(() => {
                  let next = (lsrCurrent + 1) % lsrSlides.length;
                  showLsrSlide(next);
                }, 7000);
              </script>
            <?php else: ?>
              <div class="text-gray-400">Tidak ada data Life Saving Rules & BASCOM.</div>
            <?php endif; ?>
          </div>
        </div>
        
        <!-- Kolom 3: Performance dan News (Kanan) -->
        <div class="lg:col-span-3 flex flex-col gap-3 h-full">
          <!-- Performance -->
          <div class="bg-white p-2 rounded-xl shadow-md border border-blue-100 flex flex-col kpi-card flex-1">
            <div class="flex items-center gap-2 mb-2 border-b border-blue-100 pb-1">
              <i class="fas fa-chart-pie text-sm text-primary-blue"></i>
              <h3 class="font-bold text-xs mb-0.5 text-primary-blue"><strong>Performance of the Month</strong></h3>
            </div>
            <div class="flex-1 flex flex-col justify-center items-center">
              <?php if (empty($kpiLeading) && empty($kpiLagging)): ?>
                <div class="text-gray-400">Tidak ada data performa bulan ini.</div>
              <?php else: ?>
                <canvas id="positiveNegativeChart" width="120" height="120" style="width:120px;height:120px;" class="mx-auto"></canvas>
              <?php endif; ?>
            </div>
          </div>
          
          <!-- News -->
          <div class="bg-white p-2 rounded-xl shadow-md border border-blue-100 flex flex-col kpi-card flex-1">
            <div class="flex items-center gap-2 mb-2 border-b border-blue-100 pb-1">
              <i class="fas fa-newspaper text-sm text-primary-blue"></i>
              <h3 class="font-bold text-xs mb-0.5 text-primary-blue"><strong>News</strong></h3>
            </div>
            <div class="flex-1 flex flex-col justify-center items-center relative">
              <!-- News Carousel -->
              <div id="newsCarousel" class="w-full h-full relative overflow-hidden">
                <?php if (!empty($news)): ?>
                  <?php foreach ($news as $index => $newsItem): ?>
                  <div class="news-item absolute inset-0 flex flex-col justify-center items-center text-center p-2 <?php echo $index === 0 ? 'opacity-100' : 'opacity-0'; ?> transition-opacity duration-500">
                    <div class="bg-blue-50 rounded-lg p-2 mb-2 w-full">
                      <div class="text-[8px] text-gray-500 mb-1"><?php echo date('d F Y', strtotime($newsItem['publish_date'])); ?></div>
                      <h4 class="text-xs font-bold text-primary-blue mb-1"><?php echo $newsItem['title']; ?></h4>
                      <p class="text-[10px] text-gray-700 leading-tight"><?php echo $newsItem['content']; ?></p>
                    </div>
                  </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div class="flex items-center justify-center h-full text-gray-400">Tidak ada berita terbaru.</div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Activities Carousel - Improved Version -->
      <section class="mb-1">
        <div class="relative w-full rounded-xl bg-white shadow-md border border-blue-100 overflow-hidden p-3">
          <div class="flex items-center justify-center mb-3">
            <h3 class="text-lg font-bold text-primary-blue tracking-wide"><strong>Activities of the Month</strong></h3>
          </div>
            <?php if (!empty($activities)): ?>
              <div class="flex gap-6 overflow-x-hidden py-3 px-2 activities-auto-scroll">
                <?php foreach ($activities as $activity): ?>
                <div class="min-w-[300px] max-w-xs flex-shrink-0">
                  <div class="relative h-56 w-full rounded-2xl overflow-hidden shadow-xl group border border-gray-200">
                    <img src="<?php echo $activity['image_path']; ?>" alt="<?php echo htmlspecialchars($activity['title']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 w-full px-4 pb-4 pt-10 flex flex-col justify-end">
                      <h4 class="text-base font-bold text-white mb-1 truncate drop-shadow-lg"><?php echo htmlspecialchars($activity['title']); ?></h4>
                      <p class="text-xs text-gray-200 mb-2 drop-shadow">Date: <?php echo date('d F Y', strtotime($activity['activity_date'])); ?></p>
                    </div>
                  </div>
                </div>
                <?php endforeach; ?>
                <!-- Duplikat konten agar looping seamless -->
                <?php foreach ($activities as $activity): ?>
                <div class="min-w-[300px] max-w-xs flex-shrink-0">
                  <div class="relative h-56 w-full rounded-2xl overflow-hidden shadow-xl group border border-gray-200">
                    <img src="<?php echo $activity['image_path']; ?>" alt="<?php echo htmlspecialchars($activity['title']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 w-full px-4 pb-4 pt-10 flex flex-col justify-end">
                      <h4 class="text-base font-bold text-white mb-1 truncate drop-shadow-lg"><?php echo htmlspecialchars($activity['title']); ?></h4>
                      <p class="text-xs text-gray-200 mb-2 drop-shadow">Date: <?php echo date('d F Y', strtotime($activity['activity_date'])); ?></p>
                    </div>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <div class="flex items-center justify-center h-32 text-gray-400">
                Tidak ada aktivitas bulan ini.
              </div>
            <?php endif; ?>
            <style>
              .activities-auto-scroll {
                  display: flex;
                  gap: 1.5rem;
                  animation: scroll-activities 40s linear infinite;
                  will-change: transform;
              }
              @keyframes scroll-activities {
                  0% { transform: translateX(0); }
                  100% { transform: translateX(-50%); }
              }
              /* Duplikat konten agar scroll seamless */
              .activities-auto-scroll {
                  width: max-content;
              }
            </style>
        </div>
      </section>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-header-footer-bg text-white text-center py-2 mt-3 text-xs">
    <p>&copy; 2025 <?php echo $config['company_name'] ?? 'Batamindo Investment Cakrawala'; ?>. All rights reserved</p>
  </footer>

  <script>
    // KPI Data from PHP
    const kpiLeadingData = <?php echo json_encode($kpiLeading); ?>;
    const kpiLaggingData = <?php echo json_encode($kpiLagging); ?>;
    const activitiesData = <?php echo json_encode($activities); ?>;
    const performanceData = {
      positive: <?php echo $config['performance_positive'] ?? 90; ?>,
      negative: <?php echo $config['performance_negative'] ?? 5; ?>,
      others: <?php echo $config['performance_others'] ?? 5; ?>
    };

    // Initialize Swiper for Activities
    document.addEventListener('DOMContentLoaded', function() {
      if (document.querySelector('.activities-swiper')) {
        new Swiper('.activities-swiper', {
          slidesPerView: 'auto',
          spaceBetween: 16,
          centeredSlides: false,
          loop: true,
          navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
          },
          breakpoints: {
            640: {
              slidesPerView: 2,
            },
            768: {
              slidesPerView: 3,
            },
            1024: {
              slidesPerView: 4,
            },
          },
        });
      }
    });

    // Positive vs Negative Chart
    new Chart(document.getElementById('positiveNegativeChart').getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: ['Positive', 'Negative', 'Others'],
        datasets: [{
          label: '',
          data: [performanceData.positive, performanceData.negative, performanceData.others],
          backgroundColor: [
            'rgba(46,204,113,0.8)', // green
            'rgba(231,76,60,0.8)',   // red
            'rgba(155,155,155,0.8)'  // gray
          ],
          borderColor: [
            'rgba(39,174,96,1)',
            'rgba(192,57,43,1)',
            'rgba(100,100,100,1)'
          ],
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { 
            display: true,
            position: 'bottom',
            align: 'center',
            labels: {
              font: { size: 7 },
              usePointStyle: true,
              padding: 4,
              boxWidth: 6,
              boxHeight: 6
            }
          },
          tooltip: {
            enabled: true,
            callbacks: {
              label: function(context) {
                return context.label + ': ' + context.parsed + '%';
              }
            }
          },
          datalabels: {
            display: true,
            color: '#0A4D9E',
            font: {
              size: 9,
              weight: 'bold'
            },
            formatter: function(value, context) {
              return value + '%';
            }
          }
        },
        cutout: '60%'
      }
    });

    // KPI Leading Chart
    const leadingChart = new Chart(document.getElementById('kpiLeadingChart').getContext('2d'), {
      type: 'bar',
      data: {
        labels: kpiLeadingData.map(item => item.indicator_name),
        datasets: [{
          label: 'Leading Indicators',
          data: kpiLeadingData.map(item => item.actual_value),
          backgroundColor: 'rgba(10,77,158,0.8)',
          borderColor: 'rgba(10,77,158,1)',
          borderWidth: 2
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
          legend: { display: false },
          tooltip: { enabled: true },
          datalabels: {
            display: true,
            color: '#0A4D9E',
            font: {
              size: 9,
              weight: 'bold'
            },
            anchor: 'end',
            align: 'right',
            offset: 4,
            formatter: function(value) {
              return value > 0 ? value : '';
            }
          }
        },
        scales: { 
          y: { beginAtZero: true, ticks: { font: { size: 8, weight: 'bold' }, autoSkip: false } },
          x: { 
            beginAtZero: true, 
            ticks: { font: { size: 8, weight: 'bold' } }
          }
        },
        layout: {
          padding: {
            right: 60
          }
        }
      }
    });

    // KPI Lagging Chart
    const laggingChart = new Chart(document.getElementById('kpiLaggingChart').getContext('2d'), {
      type: 'bar',
      data: {
        labels: kpiLaggingData.map(item => item.indicator_name),
        datasets: [{
          label: 'Lagging Indicators',
          data: kpiLaggingData.map(item => item.actual_value),
          backgroundColor: 'rgba(229,57,53,0.8)',
          borderColor: 'rgba(229,57,53,1)',
          borderWidth: 2
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
          legend: { display: false },
          tooltip: { enabled: true },
          datalabels: {
            display: true,
            color: '#e53935',
            font: {
              size: 9,
              weight: 'bold'
            },
            anchor: 'end',
            align: 'right',
            offset: 4,
            formatter: function(value) {
              return value;
            }
          }
        },
        scales: { 
          y: { beginAtZero: true, ticks: { font: { size: 8, weight: 'bold' }, autoSkip: false } },
          x: { 
            beginAtZero: true, 
            ticks: { font: { size: 8, weight: 'bold' } }
          }
        },
        layout: {
          padding: {
            right: 60
          }
        }
      }
    });

    // Tab Navigation for KPI Charts
    document.addEventListener('DOMContentLoaded', function() {
      const leadingTab = document.getElementById('leadingTab');
      const laggingTab = document.getElementById('laggingTab');
      const leadingChartEl = document.getElementById('kpiLeadingChart');
      const laggingChartEl = document.getElementById('kpiLaggingChart');
      
      let currentTab = 'leading';
      let autoSwitchInterval;

      function showLeadingChart() {
        leadingTab.classList.add('text-primary-blue', 'bg-blue-50');
        leadingTab.classList.remove('text-gray-500');
        laggingTab.classList.add('text-gray-500');
        laggingTab.classList.remove('text-primary-blue', 'bg-blue-50');
        
        const tabIndicator = document.getElementById('tabIndicator');
        tabIndicator.style.transform = 'translateX(0%)';
        
        leadingChartEl.style.opacity = '1';
        laggingChartEl.style.opacity = '0';
        currentTab = 'leading';
      }

      function showLaggingChart() {
        laggingTab.classList.add('text-primary-blue', 'bg-blue-50');
        laggingTab.classList.remove('text-gray-500');
        leadingTab.classList.add('text-gray-500');
        leadingTab.classList.remove('text-primary-blue', 'bg-blue-50');
        
        const tabIndicator = document.getElementById('tabIndicator');
        tabIndicator.style.transform = 'translateX(100%)';
        
        laggingChartEl.style.opacity = '1';
        leadingChartEl.style.opacity = '0';
        currentTab = 'lagging';
      }

      function switchTab() {
        setTimeout(() => {
          if (currentTab === 'leading') {
            showLaggingChart();
          } else {
            showLeadingChart();
          }
        }, 100);
      }

      function startAutoSwitch() {
        autoSwitchInterval = setInterval(switchTab, 15000);
      }

      function restartAutoSwitch() {
        clearInterval(autoSwitchInterval);
        startAutoSwitch();
      }

      leadingTab.addEventListener('click', function() {
        showLeadingChart();
        restartAutoSwitch();
      });
      
      laggingTab.addEventListener('click', function() {
        showLaggingChart();
        restartAutoSwitch();
      });

      showLeadingChart();
      startAutoSwitch();
    });

    // Life Saving Rules Carousel - Fixed Version
    document.addEventListener('DOMContentLoaded', function() {
      const lifeContents = [
        document.getElementById('lifeContent1'),
        document.getElementById('lifeContent2'),
        document.getElementById('lifeContent3'),
        document.getElementById('lifeContent4')
      ];
      const lifeDots = document.querySelectorAll('.life-dot');
      let currentLifeIndex = 0;
      let lifeAutoSlideInterval;
      
      function showLifeContent(index) {
        lifeContents.forEach((content, i) => {
          if (content) {
            content.style.opacity = (i === index) ? '1' : '0';
          }
        });
        
        lifeDots.forEach((dot, i) => {
          if (dot) {
            if (i === index) {
              dot.classList.add('bg-primary-blue', 'opacity-100');
              dot.classList.remove('bg-gray-300', 'opacity-50');
            } else {
              dot.classList.remove('bg-primary-blue', 'opacity-100');
              dot.classList.add('bg-gray-300', 'opacity-50');
            }
          }
        });
      }
      
      function nextLifeContent() {
        currentLifeIndex = (currentLifeIndex + 1) % lifeContents.length;
        showLifeContent(currentLifeIndex);
      }

      function startLifeAutoSlide() {
        lifeAutoSlideInterval = setInterval(nextLifeContent, 10000);
      }

      function stopLifeAutoSlide() {
        clearInterval(lifeAutoSlideInterval);
      }

      function restartLifeAutoSlide() {
        stopLifeAutoSlide();
        startLifeAutoSlide();
      }

      lifeDots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
          currentLifeIndex = index;
          showLifeContent(currentLifeIndex);
          restartLifeAutoSlide();
        });
      });

      const lifeCarousel = document.getElementById('lifeCarousel');
      if (lifeCarousel) {
        lifeCarousel.addEventListener('mouseenter', stopLifeAutoSlide);
        lifeCarousel.addEventListener('mouseleave', startLifeAutoSlide);
      }
      
      startLifeAutoSlide();
      showLifeContent(0);
    });

    // News Carousel
    document.addEventListener('DOMContentLoaded', function() {
      const newsItems = document.querySelectorAll('.news-item');
      if (newsItems.length === 0) return;
      
      let currentNewsIndex = 0;
      let newsAutoSlideInterval;

      function showNewsItem(index) {
        newsItems.forEach((item, i) => {
          item.style.opacity = (i === index) ? '1' : '0';
        });
      }

      function nextNewsItem() {
        currentNewsIndex = (currentNewsIndex + 1) % newsItems.length;
        showNewsItem(currentNewsIndex);
      }

      function startNewsAutoSlide() {
        newsAutoSlideInterval = setInterval(nextNewsItem, 4000);
      }

      function stopNewsAutoSlide() {
        clearInterval(newsAutoSlideInterval);
      }

      const newsCarousel = document.getElementById('newsCarousel');
      if (newsCarousel) {
        newsCarousel.addEventListener('mouseenter', stopNewsAutoSlide);
        newsCarousel.addEventListener('mouseleave', startNewsAutoSlide);
      }

      startNewsAutoSlide();
      showNewsItem(0);
    });

    // Sidebar toggle logic
    document.addEventListener('DOMContentLoaded', function() {
      const sidebar = document.getElementById('sidebar');
      const menuBtn = document.getElementById('menuBtn');
      const sidebarBackBtn = document.getElementById('sidebarBackBtn');
      
      if (!sidebar || !menuBtn) return;
      
      menuBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        sidebar.classList.toggle('-translate-x-full');
      });
      
      document.addEventListener('click', function(e) {
        if (sidebar && !sidebar.classList.contains('-translate-x-full')) {
          sidebar.classList.add('-translate-x-full');
        }
      });
      
      if (sidebar) {
        sidebar.addEventListener('click', function(e) {
          e.stopPropagation();
        });
      }
      
      if (sidebarBackBtn) {
        sidebarBackBtn.addEventListener('click', function(e) {
          e.stopPropagation();
          sidebar.classList.add('-translate-x-full');
        });
      }
    });

    // Fullscreen toggle logic
    document.addEventListener('DOMContentLoaded', function() {
      const fullscreenBtn = document.getElementById('fullscreenBtn');
      const exitFullscreenBtn = document.getElementById('exitFullscreenBtn');
      
      if (!fullscreenBtn || !exitFullscreenBtn) return;
      
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
  </script>
</body>
</html>