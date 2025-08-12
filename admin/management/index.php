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
<body class="bg-gray-50 font-sans">
    <div class="min-h-screen">
        <header class="bg-red-600 text-white px-6 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">Management</h1>
                <div class="flex items-center gap-3">
                    <a href="../dashboard.php" class="bg-white text-red-600 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Dashboard
                    </a>
                    <a href="../logout.php" class="bg-red-700 text-white px-4 py-2 rounded-lg hover:bg-red-800 transition">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </header>

        <div class="container mx-auto px-6 py-8">
            <div class="flex flex-wrap gap-2 mb-6">
                <button class="tab-btn bg-gray-800 text-white px-4 py-2 rounded" onclick="showTab('kpi')">
                    <i class="fas fa-chart-line mr-2"></i>KPI
                </button>
                <button class="tab-btn bg-gray-800 text-white px-4 py-2 rounded" onclick="showTab('activities')">
                    <i class="fas fa-calendar-alt mr-2"></i>Activities
                </button>
                <button class="tab-btn bg-gray-800 text-white px-4 py-2 rounded" onclick="showTab('news')">
                    <i class="fas fa-newspaper mr-2"></i>News
                </button>
                <button class="tab-btn bg-gray-800 text-white px-4 py-2 rounded" onclick="showTab('config')">
                    <i class="fas fa-cog mr-2"></i>Configuration
                </button>
                <button class="tab-btn bg-gray-800 text-white px-4 py-2 rounded" onclick="showTab('dashboard_stats')">
                    <i class="fas fa-database mr-2"></i>Dashboard Stats
                </button>
            </div>

            <div id="kpi" class="tab-content active">
                <?php include __DIR__ . '/kpi_tab.php'; ?>
            </div>
            <div id="activities" class="tab-content">
                <?php include __DIR__ . '/activities_tab.php'; ?>
            </div>
            <div id="news" class="tab-content">
                <?php include __DIR__ . '/news_tab.php'; ?>
            </div>
            <div id="config" class="tab-content">
                <?php include __DIR__ . '/config_tab.php'; ?>
            </div>
            <div id="dashboard_stats" class="tab-content">
                <?php include __DIR__ . '/dashboard_stats_tab.php'; ?>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(function(el) {
                el.classList.remove('active');
            });
            document.querySelectorAll('.tab-btn').forEach(function(btn) {
                btn.classList.remove('active');
            });
            document.getElementById(tabId).classList.add('active');
            // Set active button
            const indexMap = { kpi: 0, activities: 1, news: 2, config: 3, dashboard_stats: 4 };
            const buttons = document.querySelectorAll('.tab-btn');
            const idx = indexMap[tabId];
            if (buttons[idx]) buttons[idx].classList.add('active');
        }
        // Default aktif di kpi
        showTab('kpi');
    </script>
</body>
</html>
