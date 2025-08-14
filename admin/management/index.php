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
    <!-- Hamburger Menu Navigation -->
    <nav class="bg-white shadow mb-6">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <span class="font-bold text-lg text-green-700">OHSS Management</span>
                </div>
                <div class="hidden md:flex space-x-4">
                    <a href="activities_tab.php" class="text-gray-700 hover:text-green-700 font-semibold">Activities</a>
                    <a href="kpi_tab.php" class="text-gray-700 hover:text-green-700 font-semibold">KPI</a>
                    <a href="dashboard_stats_tab.php" class="text-gray-700 hover:text-green-700 font-semibold">Stats</a>
                    <a href="config_tab.php" class="text-gray-700 hover:text-green-700 font-semibold">Config</a>
                    <a href="news_tab.php" class="text-gray-700 hover:text-green-700 font-semibold">News</a>
                </div>
                <div class="md:hidden flex items-center">
                    <button id="hamburgerBtn" class="text-gray-700 focus:outline-none">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
            <div id="mobileMenu" class="md:hidden hidden flex-col space-y-2 pb-4">
                <a href="activities_tab.php" class="block text-gray-700 hover:text-green-700 font-semibold">Activities</a>
                <a href="kpi_tab.php" class="block text-gray-700 hover:text-green-700 font-semibold">KPI</a>
                <a href="dashboard_stats_tab.php" class="block text-gray-700 hover:text-green-700 font-semibold">Stats</a>
                <a href="config_tab.php" class="block text-gray-700 hover:text-green-700 font-semibold">Config</a>
                <a href="news_tab.php" class="block text-gray-700 hover:text-green-700 font-semibold">News</a>
            </div>
        </div>
    </nav>
    <div class="max-w-2xl mx-auto mt-16 text-center">
        <h1 class="text-3xl font-bold text-green-700 mb-4">Selamat Datang di OHSS Management</h1>
        <p class="text-gray-600">Silakan pilih menu di atas untuk mengelola data.</p>
    </div>
    <script>
        // Hamburger menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('hamburgerBtn');
            const menu = document.getElementById('mobileMenu');
            if (btn && menu) {
                btn.addEventListener('click', function() {
                    menu.classList.toggle('hidden');
                });
            }
        });
    </script>
</body>
</body>
</html>
