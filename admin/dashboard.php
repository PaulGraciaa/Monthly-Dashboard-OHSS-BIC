<?php
session_start();
require_once '../config/database.php';

// Cek login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true || 
    !isset($_SESSION['admin_id']) || !isset($_SESSION['admin_username'])) {
    header('Location: login.php');
    exit();
}

// Get statistics
$stats = $pdo->query("SELECT * FROM dashboard_stats WHERE is_active = 1 ORDER BY display_order")->fetchAll();
$totalActivities = $pdo->query("SELECT COUNT(*) as total FROM activities WHERE status = 'active'")->fetch()['total'];
$totalNews = $pdo->query("SELECT COUNT(*) as total FROM news WHERE status = 'published'")->fetch()['total'];
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
<body class="bg-gray-100 font-sans">
    <!-- Header -->
    <header class="bg-red-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <img src="../img/batamindo.png" alt="Batamindo Logo" class="h-8 mr-4">
                    <h1 class="text-xl font-bold">Admin Dashboard</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm">Selamat datang, <?php echo $_SESSION['admin_name']; ?></span>
                    <a href="logout.php" class="bg-red-700 hover:bg-red-800 px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg min-h-screen">
            <nav class="mt-8">
                <div class="px-4 space-y-2">
                    <a href="dashboard.php" class="flex items-center px-4 py-2 text-gray-700 bg-blue-50 border-r-4 border-blue-600">
                        <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
                    </a>
                    <a href="kpi.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-chart-bar mr-3"></i>KPI Management
                    </a>
                    <a href="activities.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-calendar-alt mr-3"></i>Activities
                    </a>
                    <a href="news.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-newspaper mr-3"></i>News
                    </a>
                    <a href="config.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-cog mr-3"></i>Configuration
                    </a>
                    <a href="security/content.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-shield-alt w-5 h-5 mr-3"></i>
                        Security Content
                    </a>
                    <a href="firesafety_content.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-fire-extinguisher mr-3"></i>Fire Safety Content
                    </a>
                    <a href="surveillance_content.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-video mr-3"></i>Surveillance Content
                    </a>
                    <a href="security/" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-user-shield w-5 h-5 mr-3"></i>
                        Security Management
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">Dashboard Overview</h2>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-chart-bar text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Activities</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $totalActivities; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-newspaper text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">News</p>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo $totalNews; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <i class="fas fa-cog text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Settings</p>
                                <p class="text-2xl font-semibold text-gray-900">3</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <a href="kpi.php" class="bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 text-center">
                            <i class="fas fa-chart-bar mr-2"></i>Manage KPI
                        </a>
                        <a href="activities.php" class="bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 text-center">
                            <i class="fas fa-plus mr-2"></i>Add Activity
                        </a>
                        <a href="news.php" class="bg-yellow-600 text-white py-3 px-4 rounded-lg hover:bg-yellow-700 text-center">
                            <i class="fas fa-plus mr-2"></i>Add News
                        </a>
                        <a href="../index.php" target="_blank" class="bg-gray-600 text-white py-3 px-4 rounded-lg hover:bg-gray-700 text-center">
                            <i class="fas fa-eye mr-2"></i>View Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html> 