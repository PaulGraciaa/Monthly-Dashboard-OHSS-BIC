<?php
require_once '../auth.php';
require_once '../../config/database.php';
requireAdminLogin();

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    try {
        $configs = [
            'company_name' => isset($_POST['company_name']) ? sanitize($_POST['company_name']) : '',
            'dashboard_title' => isset($_POST['dashboard_title']) ? sanitize($_POST['dashboard_title']) : '',
            'report_code' => isset($_POST['report_code']) ? sanitize($_POST['report_code']) : '',
            'cut_off_date' => isset($_POST['cut_off_date']) ? sanitize($_POST['cut_off_date']) : '',
            'performance_positive' => isset($_POST['performance_positive']) ? sanitize($_POST['performance_positive']) : 90,
            'performance_negative' => isset($_POST['performance_negative']) ? sanitize($_POST['performance_negative']) : 5,
            'performance_others' => isset($_POST['performance_others']) ? sanitize($_POST['performance_others']) : 5
        ];
        
        foreach ($configs as $key => $value) {
            $stmt = $pdo->prepare("UPDATE ohss_config SET config_value = ? WHERE config_key = ?");
            if (!$stmt->execute([$value, $key])) {
                throw new Exception("Error updating $key configuration");
            }
        }
        $message = '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">Configuration updated successfully!</div>';
    } catch (Exception $e) {
        $message = '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}

try {
    $config = [];
    $configData = $pdo->query("SELECT * FROM ohss_config")->fetchAll();
    foreach ($configData as $item) {
        $config[$item['config_key']] = $item['config_value'];
    }
} catch (PDOException $e) {
    $message = '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">Error loading configuration data</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration - OHSS Management</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FF0000', // Batamindo Red
                        secondary: '#1a1a1a',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen">

<?php
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $configs = [
        'company_name' => isset($_POST['company_name']) ? sanitize($_POST['company_name']) : '',
        'dashboard_title' => isset($_POST['dashboard_title']) ? sanitize($_POST['dashboard_title']) : '',
        'report_code' => isset($_POST['report_code']) ? sanitize($_POST['report_code']) : '',
        'cut_off_date' => isset($_POST['cut_off_date']) ? sanitize($_POST['cut_off_date']) : '',
        'performance_positive' => isset($_POST['performance_positive']) ? sanitize($_POST['performance_positive']) : 90,
        'performance_negative' => isset($_POST['performance_negative']) ? sanitize($_POST['performance_negative']) : 5,
        'performance_others' => isset($_POST['performance_others']) ? sanitize($_POST['performance_others']) : 5
    ];
    $success = true;
    foreach ($configs as $key => $value) {
        $stmt = $pdo->prepare("UPDATE config SET config_value = ? WHERE config_key = ?");
        if (!$stmt->execute([$value, $key])) {
            $success = false;
        }
    }
    if ($success) {
        $message = 'Configuration updated successfully!';
    } else {
        $message = 'Error occurred while updating configuration!';
    }
}

$config = [];
$configData = $pdo->query("SELECT * FROM config")->fetchAll();
foreach ($configData as $item) {
    $config[$item['config_key']] = $item['config_value'];
}
?>


<!-- Batamindo Header and Navigation -->
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
            <div class="flex items-center space-x-3">
                <div class="text-right">
                    <p class="text-sm text-white">Welcome, Admin</p>
                    <p class="text-xs text-red-200"><?php echo date('l, d F Y'); ?></p>
                </div>
                <a href="../logout.php" class="bg-white hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-150">
                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                </a>
            </div>
        </div>
        <!-- Navigation -->
        <nav class="mt-4">
            <div class="flex justify-between items-center">
                <div class="hidden md:flex items-center space-x-1">
                    <a href="activities_tab.php" class="px-4 py-2 rounded-lg text-white hover:bg-red-500 transition-colors duration-150">
                        <i class="fas fa-tasks mr-1"></i> Activities
                    </a>
                    <a href="kpi_tab.php" class="px-4 py-2 rounded-lg text-white hover:bg-red-500 transition-colors duration-150">
                        <i class="fas fa-chart-line mr-1"></i> KPI
                    </a>
                    <a href="dashboard_stats_tab.php" class="px-4 py-2 rounded-lg text-white hover:bg-red-500 transition-colors duration-150">
                        <i class="fas fa-chart-bar mr-1"></i> Stats
                    </a>
                    <a href="config_tab.php" class="px-4 py-2 rounded-lg bg-red-500 text-white font-medium">
                        <i class="fas fa-cog mr-1"></i> Config
                    </a>
                    <a href="news_tab.php" class="px-4 py-2 rounded-lg text-white hover:bg-red-500 transition-colors duration-150">
                        <i class="fas fa-newspaper mr-1"></i> News
                    </a>
                </div>
                <div class="md:hidden">
                    <button id="hamburgerBtn" class="text-white p-2 focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
            <!-- Mobile Menu -->
            <div id="mobileMenu" class="md:hidden hidden mt-4 bg-blue-700 rounded-lg p-4 space-y-2">
                <a href="activities_tab.php" class="block px-4 py-2 text-blue-100 hover:bg-blue-600 rounded transition-colors duration-150">
                    <i class="fas fa-tasks mr-2"></i> Activities
                </a>
                <a href="kpi_tab.php" class="block px-4 py-2 text-blue-100 hover:bg-blue-600 rounded transition-colors duration-150">
                    <i class="fas fa-chart-line mr-2"></i> KPI
                </a>
                <a href="dashboard_stats_tab.php" class="block px-4 py-2 text-blue-100 hover:bg-blue-600 rounded transition-colors duration-150">
                    <i class="fas fa-chart-bar mr-2"></i> Stats
                </a>
                <a href="config_tab.php" class="block px-4 py-2 bg-blue-600 text-white rounded font-medium">
                    <i class="fas fa-cog mr-2"></i> Config
                </a>
                <a href="news_tab.php" class="block px-4 py-2 text-blue-100 hover:bg-blue-600 rounded transition-colors duration-150">
                    <i class="fas fa-newspaper mr-2"></i> News
                </a>
                <hr class="border-blue-600 my-2">
                <a href="../logout.php" class="block px-4 py-2 text-red-300 hover:bg-red-600 hover:text-white rounded transition-colors duration-150">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            </div>
        </nav>
    </div>
</header>

<?php if ($message): ?>
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <?php echo $message; ?>
</div>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-4">
    <?php if ($message): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-md">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
            <p><?php echo $message; ?></p>
        </div>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-cog text-blue-600 mr-3"></i>
                <span>System Configuration</span>
            </h2>
            <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                <i class="fas fa-clock mr-2"></i>Last updated: <?php echo date('d M Y'); ?>
            </span>
        </div>

        <form method="POST" class="space-y-8">
            <input type="hidden" name="action" value="update">
            
            <!-- Company Information Section -->
            <div class="bg-gray-50 rounded-xl p-6 space-y-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-building text-blue-600 mr-2"></i>Company Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Company Name</label>
                        <div class="relative">
                            <i class="fas fa-building absolute left-3 top-3 text-gray-400"></i>
                            <input type="text" name="company_name" value="<?php echo $config['company_name'] ?? ''; ?>" required 
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Dashboard Title</label>
                        <div class="relative">
                            <i class="fas fa-desktop absolute left-3 top-3 text-gray-400"></i>
                            <input type="text" name="dashboard_title" value="<?php echo $config['dashboard_title'] ?? ''; ?>" required 
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Report Code</label>
                        <div class="relative">
                            <i class="fas fa-hashtag absolute left-3 top-3 text-gray-400"></i>
                            <input type="text" name="report_code" value="<?php echo $config['report_code'] ?? ''; ?>" required 
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Cut Off Date</label>
                        <div class="relative">
                            <i class="fas fa-calendar absolute left-3 top-3 text-gray-400"></i>
                            <input type="text" name="cut_off_date" value="<?php echo $config['cut_off_date'] ?? ''; ?>" required 
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                   placeholder="e.g., 01 July –31 July 2025">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Performance Settings Section -->
            <div class="bg-gray-50 rounded-xl p-6 space-y-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-chart-pie text-blue-600 mr-2"></i>Performance Distribution
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-4 rounded-lg shadow-sm space-y-2 border border-gray-100">
                        <label class="block text-sm font-medium text-gray-700">Positive (%)</label>
                        <div class="relative">
                            <i class="fas fa-plus-circle absolute left-3 top-3 text-green-500"></i>
                            <input type="number" name="performance_positive" value="<?php echo $config['performance_positive'] ?? 90; ?>" required 
                                   class="w-full pl-10 pr-4 py-2.5 border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 bg-green-50"
                                   min="0" max="100">
                        </div>
                    </div>
                    
                    <div class="bg-white p-4 rounded-lg shadow-sm space-y-2 border border-gray-100">
                        <label class="block text-sm font-medium text-gray-700">Negative (%)</label>
                        <div class="relative">
                            <i class="fas fa-minus-circle absolute left-3 top-3 text-red-500"></i>
                            <input type="number" name="performance_negative" value="<?php echo $config['performance_negative'] ?? 5; ?>" required 
                                   class="w-full pl-10 pr-4 py-2.5 border border-red-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 bg-red-50"
                                   min="0" max="100">
                        </div>
                    </div>
                    
                    <div class="bg-white p-4 rounded-lg shadow-sm space-y-2 border border-gray-100">
                        <label class="block text-sm font-medium text-gray-700">Others (%)</label>
                        <div class="relative">
                            <i class="fas fa-dot-circle absolute left-3 top-3 text-gray-500"></i>
                            <input type="number" name="performance_others" value="<?php echo $config['performance_others'] ?? 5; ?>" required 
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all duration-200 bg-gray-50"
                                   min="0" max="100">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end pt-6">
                <button type="submit" class="flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-8 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5">
                    <i class="fas fa-save"></i>
                    <span class="font-medium">Save Changes</span>
                </button>
            </div>
    </form>
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
</div>
