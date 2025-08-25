<?php
require_once '../auth.php';
require_once '../../config/database.php';
requireAdminLogin();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - OHSS Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .tab-btn.active { background-color: #1f2937; }
    </style>
</head>
</body>
    <!-- Header and Navigation -->
    <header class="bg-gradient-to-r from-red-600 to-red-800 text-white py-4 shadow-lg mb-6">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Company Header -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-4">
                    <img src="../../img/batamindo.png" alt="Batamindo" class="h-12 w-auto bg-white p-1 rounded">
                    <div>
                        <h1 class="text-2xl font-bold text-white">Batamindo Industrial Park</h1>
                        <p class="text-red-200">OHS Security System Management</p>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-3">
                    <div class="text-right">
                        <p class="text-sm text-white">Welcome, Admin</p>
                        <p class="text-xs text-red-200"><?php echo date('l, d F Y'); ?></p>
                    </div>
                    <a href="../logout.php" class="bg-white hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-150">
                        <i class="fas fa-sign-out-alt mr-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </header>


    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <?php
            try {
                // Get statistics from database
                $stats = $pdo->query("SELECT COUNT(*) as count, is_active FROM ohss_dashboard_stats GROUP BY is_active")->fetchAll();
                $total_stats = array_sum(array_column($stats, 'count'));
                $active_stats = array_reduce($stats, function($carry, $item) {
                    return $carry + ($item['is_active'] ? $item['count'] : 0);
                }, 0);
            } catch (PDOException $e) {
                $total_stats = 0;
                $active_stats = 0;
            }

            try {
                // Get KPI stats
                $kpi_stats = $pdo->query("SELECT COUNT(*) as count FROM ohss_dashboard_kpi")->fetch();
                $kpi_count = $kpi_stats ? $kpi_stats['count'] : 0;
            } catch (PDOException $e) {
                $kpi_count = 0;
            }

            try {
                // Get activities count
                $activities = $pdo->query("SELECT COUNT(*) as count FROM ohss_activities")->fetch();
                $activities_count = $activities ? $activities['count'] : 0;
            } catch (PDOException $e) {
                $activities_count = 0;
            }

            try {
                // Get news count
                $news = $pdo->query("SELECT COUNT(*) as count FROM ohss_news")->fetch();
                $news_count = $news ? $news['count'] : 0;
            } catch (PDOException $e) {
                $news_count = 0;
            }
            ?>

            <!-- KPI Card -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white hover:scale-105 transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">KPI Status</h3>
                    <i class="fas fa-chart-line text-2xl opacity-75"></i>
                </div>
                <div class="text-3xl font-bold"><?php echo $kpi_count; ?></div>
                <div class="text-blue-100 text-sm mt-2">Total KPI Metrics</div>
            </div>

            <!-- Activities Card -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white hover:scale-105 transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Activities</h3>
                    <i class="fas fa-tasks text-2xl opacity-75"></i>
                </div>
                <div class="text-3xl font-bold"><?php echo $activities_count; ?></div>
                <div class="text-green-100 text-sm mt-2">Recorded Activities</div>
            </div>

            <!-- News Card -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white hover:scale-105 transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">News Updates</h3>
                    <i class="fas fa-newspaper text-2xl opacity-75"></i>
                </div>
                <div class="text-3xl font-bold"><?php echo $news_count; ?></div>
                <div class="text-purple-100 text-sm mt-2">Published News</div>
            </div>

            <!-- Stats Card -->
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white hover:scale-105 transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Dashboard Stats</h3>
                    <i class="fas fa-chart-bar text-2xl opacity-75"></i>
                </div>
                <div class="text-3xl font-bold"><?php echo $total_stats; ?></div>
                <div class="text-red-100 text-sm mt-2"><?php echo $active_stats; ?> Active Metrics</div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Recent Activities</h2>
                <p class="text-sm text-gray-600 mt-1">Latest activities and updates</p>
            </div>
            <div class="p-6">
                <div class="space-y-6">
                    <?php
                    try {
                        $recent_activities = $pdo->query("SELECT * FROM ohss_activities ORDER BY created_at DESC LIMIT 5")->fetchAll();
                        if ($recent_activities) {
                            foreach ($recent_activities as $activity): ?>
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 bg-blue-100 rounded-full p-2">
                                    <i class="fas fa-clipboard-list text-blue-600"></i>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($activity['title']); ?></h4>
                                    <p class="text-gray-600 mt-1"><?php echo htmlspecialchars($activity['description']); ?></p>
                                    <p class="text-sm text-gray-500 mt-1"><?php echo date('d M Y', strtotime($activity['created_at'])); ?></p>
                                </div>
                            </div>
                            <?php endforeach;
                        } else { ?>
                            <div class="text-gray-500 text-center py-4">No recent activities found</div>
                        <?php }
                    } catch (PDOException $e) { ?>
                        <div class="text-gray-500 text-center py-4">Unable to load activities</div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <!-- Latest News & KPI Summary -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Latest News -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">Latest News</h2>
                    <p class="text-sm text-gray-600 mt-1">Recent announcements and updates</p>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        <?php
                        try {
                            $latest_news = $pdo->query("SELECT * FROM ohss_news ORDER BY created_at DESC LIMIT 3")->fetchAll();
                            if ($latest_news) {
                                foreach ($latest_news as $news): ?>
                                <div class="border-l-4 border-purple-500 pl-4">
                                    <h4 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($news['title']); ?></h4>
                                    <p class="text-gray-600 mt-1"><?php echo htmlspecialchars(substr($news['content'], 0, 100)) . '...'; ?></p>
                                    <p class="text-sm text-gray-500 mt-2"><?php echo date('d M Y', strtotime($news['created_at'])); ?></p>
                                </div>
                                <?php endforeach;
                            } else { ?>
                                <div class="text-gray-500 text-center py-4">No news articles found</div>
                            <?php }
                        } catch (PDOException $e) { ?>
                            <div class="text-gray-500 text-center py-4">Unable to load news</div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- KPI Summary -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">KPI Overview</h2>
                    <p class="text-sm text-gray-600 mt-1">Current performance indicators</p>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        <?php
                        try {
                            $kpi_summary = $pdo->query("SELECT * FROM ohss_dashboard_kpi ORDER BY id DESC LIMIT 3")->fetchAll();
                            if ($kpi_summary) {
                                foreach ($kpi_summary as $kpi): ?>
                                <div class="relative">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-medium text-gray-700"><?php echo htmlspecialchars($kpi['title']); ?></span>
                                        <span class="text-sm font-semibold text-gray-900"><?php echo $kpi['value']; ?>%</span>
                                    </div>
                                    <div class="overflow-hidden h-2 bg-gray-200 rounded">
                                        <div class="h-full bg-blue-600 rounded" style="width: <?php echo $kpi['value']; ?>%"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1"><?php echo htmlspecialchars($kpi['description']); ?></p>
                                </div>
                                <?php endforeach;
                            } else { ?>
                                <div class="text-gray-500 text-center py-4">No KPI data found</div>
                            <?php }
                        } catch (PDOException $e) { ?>
                            <div class="text-gray-500 text-center py-4">Unable to load KPI data</div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Hamburger menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('hamburgerBtn');
            const menu = document.getElementById('mobileMenu');
            if (btn && menu) {
                btn.addEventListener('click', function() {
                    menu.classList.toggle('hidden');
                    menu.classList.toggle('flex');
                    menu.classList.toggle('flex-col');
                });
            }

            // Add hover effect to cards
            document.querySelectorAll('.bg-gradient-to-br').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.classList.add('transform', 'scale-105');
                });
                card.addEventListener('mouseleave', function() {
                    this.classList.remove('transform', 'scale-105');
                });
            });

            // Auto-refresh dashboard data every 5 minutes
            setInterval(function() {
                location.reload();
            }, 300000);
        });
    </script>
</body>
</html>
