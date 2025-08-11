<?php
session_start();
require_once '../config/database.php';

// Cek login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true || 
    !isset($_SESSION['admin_id']) || !isset($_SESSION['admin_username'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - OHSS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-red-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <img src="../img/batamindo.png" alt="Batamindo Logo" class="h-8 mr-4">
                    <h1 class="text-xl font-bold">Admin Dashboard</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm">Selamat datang, <?php echo $_SESSION['admin_name'] ?? $_SESSION['admin_username']; ?></span>
                    <a href="logout.php" class="bg-red-700 hover:bg-red-800 px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main -->
    <main class="flex-1">
        <div class="max-w-7xl mx-auto px-4 py-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-8">Modules</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <a href="management/" class="block bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                            <i class="fas fa-layer-group text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Module</p>
                            <p class="text-xl font-semibold text-gray-900">Management Center</p>
                            <p class="text-xs text-gray-500 mt-1">KPI, Activities, News, Configuration</p>
                        </div>
                    </div>
                </a>
                <a href="OHS/" class="block bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-notes-medical text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Module</p>
                            <p class="text-xl font-semibold text-gray-900">OHS Incidents</p>
                            <p class="text-xs text-gray-500 mt-1">CRUD Insiden & Lesson Learned</p>
                        </div>
                    </div>
                </a>
                <a href="security/" class="block bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-600">
                            <i class="fas fa-user-shield text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Module</p>
                            <p class="text-xl font-semibold text-gray-900">Security Management</p>
                            <p class="text-xs text-gray-500 mt-1">Personnel, Gallery, Content</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t">
        <div class="max-w-7xl mx-auto px-4 py-4 text-center text-gray-500 text-sm">
            <p>&copy; <?php echo date('Y'); ?> Batamindo Investment Cakrawala. All rights reserved.</p>
        </div>
    </footer>
</body>
</html> 