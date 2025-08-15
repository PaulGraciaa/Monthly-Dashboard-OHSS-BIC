<?php
require_once '../auth.php';
require_once '../../config/database.php';
requireAdminLogin();

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $stat_name = isset($_POST['stat_name']) ? sanitize($_POST['stat_name']) : '';
    $stat_value = isset($_POST['stat_value']) ? sanitize($_POST['stat_value']) : '';
    $stat_description = isset($_POST['stat_description']) ? sanitize($_POST['stat_description']) : '';
    $stat_icon = isset($_POST['stat_icon']) ? sanitize($_POST['stat_icon']) : '';
    $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    try {
        if ($_POST['action'] === 'add') {
            $stmt = $pdo->prepare("INSERT INTO dashboard_stats (stat_name, stat_value, stat_description, stat_icon, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$stat_name, $stat_value, $stat_description, $stat_icon, $display_order, $is_active]);
            $message = 'Data berhasil ditambahkan!';
        } elseif ($_POST['action'] === 'update' && $id) {
            $stmt = $pdo->prepare("UPDATE dashboard_stats SET stat_name=?, stat_value=?, stat_description=?, stat_icon=?, display_order=?, is_active=? WHERE id=?");
            $stmt->execute([$stat_name, $stat_value, $stat_description, $stat_icon, $display_order, $is_active, $id]);
            $message = 'Data berhasil diupdate!';
        } elseif ($_POST['action'] === 'delete' && $id) {
            $stmt = $pdo->prepare("DELETE FROM dashboard_stats WHERE id=?");
            $stmt->execute([$id]);
            $message = 'Data berhasil dihapus!';
        }
    } catch (Exception $e) {
        $message = 'Terjadi error: ' . $e->getMessage();
    }
}

$stats = $pdo->query("SELECT * FROM dashboard_stats ORDER BY display_order, id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Stats Management - Batamindo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
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
            <!-- Navigation Menu -->
            <nav class="flex items-center justify-between">
                <div class="text-xl font-bold">Dashboard Stats</div>
                <div class="md:hidden">
                    <button id="menuBtn" class="text-white focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
                <div id="mobileMenu" class="hidden md:flex space-x-4">
                    <a href="index.php" class="text-white hover:text-red-200 px-3 py-2">OHS</a>
                    <a href="news_tab.php" class="text-white hover:text-red-200 px-3 py-2">News</a>
                    <a href="activities_tab.php" class="text-white hover:text-red-200 px-3 py-2">Activities</a>
                    <a href="config_tab.php" class="text-white hover:text-red-200 px-3 py-2">Config</a>
                    <a href="kpi_tab.php" class="text-white hover:text-red-200 px-3 py-2">KPI</a>
                    <a href="dashboard_stats_tab.php" class="text-white hover:text-red-200 px-3 py-2">Dashboard Stats</a>
                </div>
            </nav>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8">
        <?php if ($message): ?>
        <div class="mb-6">
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-md" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <p><?php echo $message; ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Quick Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <?php 
            $total_stats = count($stats);
            $active_stats = count(array_filter($stats, function($stat) { return $stat['is_active'] == 1; }));
            ?>
            <!-- Total Stats -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Total Stats</h3>
                    <i class="fas fa-chart-bar text-2xl opacity-75"></i>
                </div>
                <div class="text-3xl font-bold"><?php echo $total_stats; ?></div>
                <div class="text-blue-100 text-sm mt-2">Total statistics tracked</div>
            </div>

            <!-- Active Stats -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Active Stats</h3>
                    <i class="fas fa-check-circle text-2xl opacity-75"></i>
                </div>
                <div class="text-3xl font-bold"><?php echo $active_stats; ?></div>
                <div class="text-green-100 text-sm mt-2">Currently active stats</div>
            </div>

            <!-- Last Updated -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Last Updated</h3>
                    <i class="fas fa-clock text-2xl opacity-75"></i>
                </div>
                <div class="text-xl font-bold"><?php echo date('d M Y'); ?></div>
                <div class="text-purple-100 text-sm mt-2"><?php echo date('H:i:s'); ?></div>
            </div>

            <!-- Add New Stat -->
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white cursor-pointer hover:from-red-600 hover:to-red-700 transition-all duration-300" onclick="document.getElementById('addNewStatModal').classList.remove('hidden')">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Add New Stat</h3>
                    <i class="fas fa-plus-circle text-2xl opacity-75"></i>
                </div>
                <div class="text-xl font-bold">Create New</div>
                <div class="text-red-100 text-sm mt-2">Click to add new statistic</div>
            </div>
        </div>

        <!-- Stats Table -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Manage Statistics</h2>
                <p class="text-sm text-gray-600 mt-1">View and manage all dashboard statistics</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Name</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Value</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Icon</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Order</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($stats as $stat): ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <form method="POST" class="contents">
                                <input type="hidden" name="id" value="<?php echo $stat['id']; ?>">
                                <td class="px-6 py-4">
                                    <input type="text" name="stat_name" value="<?php echo htmlspecialchars($stat['stat_name']); ?>" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                </td>
                                <td class="px-6 py-4">
                                    <input type="text" name="stat_value" value="<?php echo htmlspecialchars($stat['stat_value']); ?>" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                </td>
                                <td class="px-6 py-4">
                                    <input type="text" name="stat_icon" value="<?php echo htmlspecialchars($stat['stat_icon']); ?>" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent text-center"
                                           placeholder="fa-icon-name">
                                </td>
                                <td class="px-6 py-4">
                                    <input type="number" name="display_order" value="<?php echo $stat['display_order']; ?>" 
                                           class="w-20 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent text-center">
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" <?php if($stat['is_active']) echo 'checked'; ?>>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                                    </label>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button type="submit" name="action" value="update" 
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                                            <i class="fas fa-save mr-1"></i> Save
                                        </button>
                                        <button type="submit" name="action" value="delete" 
                                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-200"
                                                onclick="return confirm('Are you sure you want to delete this statistic?')">
                                            <i class="fas fa-trash-alt mr-1"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </form>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add New Stat Modal -->
    <div id="addNewStatModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900">Add New Statistic</h3>
                <button onclick="document.getElementById('addNewStatModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statistic Name</label>
                    <input type="text" name="stat_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                    <input type="text" name="stat_value" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="stat_description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Icon (Font Awesome class)</label>
                    <input type="text" name="stat_icon" placeholder="fa-chart-bar" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                    <input type="number" name="display_order" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                    <label class="ml-2 text-sm text-gray-700">Active</label>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="document.getElementById('addNewStatModal').classList.add('hidden')" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" name="action" value="add" 
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                        Add Statistic
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        const menuBtn = document.getElementById('menuBtn');
        const mobileMenu = document.getElementById('mobileMenu');

        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            mobileMenu.classList.toggle('flex');
            mobileMenu.classList.toggle('flex-col');
            mobileMenu.classList.toggle('absolute');
            mobileMenu.classList.toggle('top-16');
            mobileMenu.classList.toggle('right-4');
            mobileMenu.classList.toggle('bg-red-800');
            mobileMenu.classList.toggle('p-4');
            mobileMenu.classList.toggle('rounded-lg');
            mobileMenu.classList.toggle('shadow-lg');
            mobileMenu.classList.toggle('z-50');
        });

        // Add animation to stat cards
        document.querySelectorAll('.bg-gradient-to-br').forEach(card => {
            card.classList.add('transition-transform', 'duration-200', 'hover:scale-105');
        });

        // Auto-hide alert messages after 5 seconds
        const alerts = document.querySelectorAll('[role="alert"]');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s ease-out';
                setTimeout(() => alert.remove(), 500);
            }, 5000);
        });
    </script>
</body>
</html>
