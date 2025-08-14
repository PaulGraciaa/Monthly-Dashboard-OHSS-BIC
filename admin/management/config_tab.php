<?php
require_once '../auth.php';
require_once '../../config/database.php';
requireAdminLogin();


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
        $message = 'Konfigurasi berhasil diperbarui!';
    } else {
        $message = 'Terjadi kesalahan saat memperbarui konfigurasi!';
    }
}

$config = [];
$configData = $pdo->query("SELECT * FROM config")->fetchAll();
foreach ($configData as $item) {
    $config[$item['config_key']] = $item['config_value'];
}
?>


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

<?php if ($message): ?>
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <?php echo $message; ?>
</div>
<?php endif; ?>

<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">
        <i class="fas fa-cog text-blue-600 mr-2"></i>Dashboard Configuration
    </h2>
    <form method="POST" class="space-y-6">
        <input type="hidden" name="action" value="update">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                <input type="text" name="company_name" value="<?php echo $config['company_name'] ?? ''; ?>" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Dashboard Title</label>
                <input type="text" name="dashboard_title" value="<?php echo $config['dashboard_title'] ?? ''; ?>" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Report Code</label>
                <input type="text" name="report_code" value="<?php echo $config['report_code'] ?? ''; ?>" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cut Off Date</label>
                <input type="text" name="cut_off_date" value="<?php echo $config['cut_off_date'] ?? ''; ?>" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="e.g., 01 July –31 July 2025">
            </div>
        </div>
        
        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Settings</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Positive (%)</label>
                    <input type="number" name="performance_positive" value="<?php echo $config['performance_positive'] ?? 90; ?>" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                           min="0" max="100">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Negative (%)</label>
                    <input type="number" name="performance_negative" value="<?php echo $config['performance_negative'] ?? 5; ?>" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                           min="0" max="100">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Others (%)</label>
                    <input type="number" name="performance_others" value="<?php echo $config['performance_others'] ?? 5; ?>" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500"
                           min="0" max="100">
                </div>
            </div>
        </div>
        
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i>Save Configuration
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
