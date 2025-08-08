<?php
session_start();
require_once '../config/database.php';
checkAdminLogin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'update') {
        $configs = [
            'company_name' => sanitize($_POST['company_name']),
            'dashboard_title' => sanitize($_POST['dashboard_title']),
            'report_code' => sanitize($_POST['report_code']),
            'cut_off_date' => sanitize($_POST['cut_off_date']),
            'performance_positive' => sanitize($_POST['performance_positive']),
            'performance_negative' => sanitize($_POST['performance_negative']),
            'performance_others' => sanitize($_POST['performance_others'])
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
}

// Get current configuration
$config = [];
$configData = $pdo->query("SELECT * FROM config")->fetchAll();
foreach ($configData as $item) {
    $config[$item['config_key']] = $item['config_value'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration - OHSS Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <header class="bg-red-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <img src="../img/batamindo.png" alt="Batamindo Logo" class="h-8 mr-4">
                    <h1 class="text-xl font-bold">Configuration</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="dashboard.php" class="text-white hover:text-gray-200">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                    </a>
                    <a href="logout.php" class="bg-red-700 hover:bg-red-800 px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Configuration Form -->
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
        </div>
    </div>
</body>
</html> 