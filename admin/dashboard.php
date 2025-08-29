<?php
session_start();
require_once '../config/database.php';

// Cek login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true || 
    !isset($_SESSION['admin_id']) || !isset($_SESSION['admin_username'])) {
    header('Location: login.php');
    exit();
}

// Handle session name untuk PHP 5.3
$adminName = '';
if (isset($_SESSION['admin_name'])) {
    $adminName = $_SESSION['admin_name'];
} else if (isset($_SESSION['admin_username'])) {
    $adminName = $_SESSION['admin_username'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - OHSS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                },
            },
        }
    </script>
    <style>
        .gradient-bg {
            background: #dc2626; /* Fallback untuk browser lama */
            background: -moz-linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            background: -webkit-linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        }
        .module-card:hover {
            position: relative;
            top: -2px;
        }
        .animate-fade-in {
            opacity: 0;
            animation: fadeIn 0.5s ease-out forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50 font-sans min-h-screen flex flex-col">
    <!-- Header -->
    <header class="gradient-bg text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-6 py-6">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-6 animate-fade-in">
                    <img src="../img/batamindo.png" alt="Batamindo Logo" class="h-12 w-auto bg-white p-1.5 rounded-lg shadow-sm">
                    <div>
                        <h1 class="text-2xl font-bold">Batamindo Industrial Park</h1>
                        <p class="text-red-100 text-sm mt-0.5">OHS Security System Management</p>
                    </div>
                </div>
                <div class="flex items-center space-x-8">
                    <div class="text-right">
                        <p class="text-red-100 text-sm">Selamat datang,</p>
                        <p class="font-medium"><?php echo htmlspecialchars($adminName); ?></p>
                    </div>
                    <a href="logout.php" class="bg-white text-red-600 hover:bg-red-50 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 shadow-sm hover:shadow">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main -->
    <main class="flex-1 animate-fade-in">
        <div class="max-w-7xl mx-auto px-6 py-12">
            <div class="flex justify-between items-center mb-10">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">System Modules</h2>
                    <p class="text-gray-500 mt-1">Access and manage all system functionalities</p>
                </div>
                <div class="text-right text-gray-500">
                    <p class="text-sm"><?php echo date('l, d F Y'); ?></p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Management Center Module -->
                <a href="management/kpi_tab.php" class="module-card group block bg-white rounded-xl shadow-sm p-6 hover:shadow-xl transition-all duration-300 border border-gray-100">
                    <div class="flex items-center space-x-4">
                        <div class="p-4 rounded-xl bg-indigo-100 text-indigo-600 shadow-sm group-hover:shadow group-hover:bg-indigo-200 transition-all duration-300">
                            <i class="fas fa-layer-group text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Module</p>
                            <h3 class="text-lg font-bold text-gray-900 mt-1">Management Center</h3>
                            <p class="text-sm text-gray-500 mt-1">KPI, Activities, News, Configuration</p>
                        </div>
                    </div>
                </a>

                <!-- OHS Incidents Module -->
                <a href="OHS/" class="module-card group block bg-white rounded-xl shadow-sm p-6 hover:shadow-xl transition-all duration-300 border border-gray-100">
                    <div class="flex items-center space-x-4">
                        <div class="p-4 rounded-xl bg-blue-100 text-blue-600 shadow-sm group-hover:shadow group-hover:bg-blue-200 transition-all duration-300">
                            <i class="fas fa-notes-medical text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Module</p>
                            <h3 class="text-lg font-bold text-gray-900 mt-1">OHS Incidents</h3>
                            <p class="text-sm text-gray-500 mt-1">CRUD Insiden & Lesson Learned</p>
                        </div>
                    </div>
                </a>

                <!-- Security Management Module -->
                <a href="security/" class="module-card group block bg-white rounded-xl shadow-sm p-6 hover:shadow-xl transition-all duration-300 border border-gray-100">
                    <div class="flex items-center space-x-4">
                        <div class="p-4 rounded-xl bg-red-100 text-red-600 shadow-sm group-hover:shadow group-hover:bg-red-200 transition-all duration-300">
                            <i class="fas fa-user-shield text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Module</p>
                            <h3 class="text-lg font-bold text-gray-900 mt-1">Security Management</h3>
                            <p class="text-sm text-gray-500 mt-1">Personnel, Gallery, Content</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-auto">
        <div class="max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
            <p class="text-gray-500 text-sm">&copy; <?php echo date('Y'); ?> Batamindo Investment Cakrawala</p>
            <div class="text-gray-400 text-sm">
                <span class="mx-2">•</span>
                <span>All rights reserved</span>
            </div>
        </div>
    </footer>
</body>
</html>