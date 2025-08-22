<?php
require_once '../auth.php';
require_once '../../config/database.php';
requireAdminLogin();

// Simple sanitize function if not already defined
if (!function_exists('sanitize')) {
    function sanitize($str) {
        return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
    }
}

// ...existing code...
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    try {
        if ($_POST['action'] == 'create_leading') {
            $indicator_name = isset($_POST['indicator_name']) ? sanitize($_POST['indicator_name']) : '';
            $actual_value = isset($_POST['actual_value']) ? sanitize($_POST['actual_value']) : '';
            if ($indicator_name !== '' && $actual_value !== '') {
                $stmt = $pdo->prepare("INSERT INTO kpi_leading (indicator_name, actual_value) VALUES (?, ?)");
                $stmt->execute([$indicator_name, $actual_value]);
                $_SESSION['notif'] = 'KPI Leading berhasil ditambahkan!';
            }
        } elseif ($_POST['action'] == 'delete_leading') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            if ($id > 0) {
                $stmt = $pdo->prepare("DELETE FROM kpi_leading WHERE id = ?");
                $stmt->execute([$id]);
                $_SESSION['notif'] = 'KPI Leading berhasil dihapus!';
            }
        } elseif ($_POST['action'] == 'create_lagging') {
            $indicator_name = isset($_POST['indicator_name']) ? sanitize($_POST['indicator_name']) : '';
            $actual_value = isset($_POST['actual_value']) ? sanitize($_POST['actual_value']) : '';
            if ($indicator_name !== '' && $actual_value !== '') {
                $stmt = $pdo->prepare("INSERT INTO kpi_lagging (indicator_name, actual_value) VALUES (?, ?)");
                $stmt->execute([$indicator_name, $actual_value]);
                $_SESSION['notif'] = 'KPI Lagging berhasil ditambahkan!';
            }
        } elseif ($_POST['action'] == 'delete_lagging') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            if ($id > 0) {
                $stmt = $pdo->prepare("DELETE FROM kpi_lagging WHERE id = ?");
                $stmt->execute([$id]);
                $_SESSION['notif'] = 'KPI Lagging berhasil dihapus!';
            }
        } elseif ($_POST['action'] == 'update_leading') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $actual_value = isset($_POST['actual_value']) ? sanitize($_POST['actual_value']) : '';
            $target_value = isset($_POST['target_value']) && $_POST['target_value'] !== '' ? trim($_POST['target_value']) : null;
            $notes = isset($_POST['notes']) && $_POST['notes'] !== '' ? trim($_POST['notes']) : null;
            if ($id > 0 && $actual_value !== '') {
                $stmt = $pdo->prepare("UPDATE kpi_leading SET actual_value = ?, target_value = ?, notes = ? WHERE id = ?");
                $stmt->execute([$actual_value, $target_value, $notes, $id]);
                $_SESSION['notif'] = 'KPI Leading berhasil diperbarui!';
            }
        } elseif ($_POST['action'] == 'update_lagging') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $actual_value = isset($_POST['actual_value']) ? sanitize($_POST['actual_value']) : '';
            if ($id > 0 && $actual_value !== '') {
                $stmt = $pdo->prepare("UPDATE kpi_lagging SET actual_value = ? WHERE id = ?");
                $stmt->execute([$actual_value, $id]);
                $_SESSION['notif'] = 'KPI Lagging berhasil diperbarui!';
            }
        }
    } catch (Exception $e) {
    $_SESSION['notif'] = 'Terjadi error: ' . $e->getMessage();
    }
}

$leadingKPIs = $pdo->query("SELECT * FROM kpi_leading ORDER BY indicator_name")->fetchAll();
$laggingKPIs = $pdo->query("SELECT * FROM kpi_lagging ORDER BY indicator_name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KPI Management - Batamindo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <!-- Red Header Section -->
    <div class="bg-gradient-to-r from-red-600 to-red-800">
        <header class="text-white py-4">
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

        <!-- Navigation -->
        <div class="border-t border-red-500/30">
            <div class="max-w-7xl mx-auto px-4 py-2">
                <nav class="flex space-x-4">
                    <a href="../dashboard.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-chart-line mr-1"></i> Dashboard
                    </a>
                    <a href="activities_tab.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-tasks mr-1"></i> Activities
                    </a>
                    <a href="life_saving_rules_tab.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-shield-alt mr-1"></i> Life Saving Rules & BASCOM
                    </a>
                    <a href="kpi_tab.php" class="bg-red-700 text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-chart-bar mr-1"></i> KPI
                    </a>
                    <a href="news_tab.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-newspaper mr-1"></i> News
                    </a>
                    <a href="config_tab.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-cog mr-1"></i> Config
                    </a>
                    <a href="dashboard_stats_tab.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-chart-pie mr-1"></i> Stats
                    </a>
                </nav>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <?php if (!isset($_SESSION)) { session_start(); } ?>
        <?php if (!empty($_SESSION['notif'])): ?>
        <style>
        @keyframes notifSlideIn {
            0% { opacity: 0; transform: translateY(-30px) scale(0.95); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes notifFadeOut {
            to { opacity: 0; transform: translateY(-10px) scale(0.98); }
        }
        .notif-animate-in { animation: notifSlideIn 0.5s cubic-bezier(.4,0,.2,1); }
        .notif-animate-out { animation: notifFadeOut 0.5s cubic-bezier(.4,0,.2,1) forwards; }
        </style>
        <div id="notifBox" class="fixed top-8 right-8 z-50 min-w-[260px] max-w-xs bg-white border border-green-400 shadow-2xl rounded-xl flex items-center px-5 py-4 gap-3 notif-animate-in" style="box-shadow:0 8px 32px 0 rgba(34,197,94,0.15);">
            <div class="flex-shrink-0">
                <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-green-100">
                    <i class="fas fa-check text-green-600 text-xl"></i>
                </span>
            </div>
            <div class="flex-1 text-green-800 font-semibold text-sm">
                <?php echo $_SESSION['notif']; unset($_SESSION['notif']); ?>
            </div>
            <button onclick="closeNotif()" class="ml-2 text-green-400 hover:text-green-700 focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <script>
        function closeNotif() {
            var notif = document.getElementById('notifBox');
            if (notif) {
                notif.classList.remove('notif-animate-in');
                notif.classList.add('notif-animate-out');
                setTimeout(function(){ notif.remove(); }, 500);
            }
        }
        setTimeout(closeNotif, 3000);
        </script>
        <?php endif; ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <?php 
            $total_leading = count($leadingKPIs);
            $total_lagging = count($laggingKPIs);
            ?>
            <!-- Total Leading -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Total Leading</h3>
                    <i class="fas fa-chart-line text-2xl opacity-75"></i>
                </div>
                <div class="text-3xl font-bold"><?php echo $total_leading; ?></div>
                <div class="text-blue-100 text-sm mt-2">Total leading indicators</div>
            </div>
            <!-- Total Lagging -->
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Total Lagging</h3>
                    <i class="fas fa-exclamation-triangle text-2xl opacity-75"></i>
                </div>
                <div class="text-3xl font-bold"><?php echo $total_lagging; ?></div>
                <div class="text-red-100 text-sm mt-2">Total lagging indicators</div>
            </div>
            <!-- Add New Leading -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white cursor-pointer hover:from-green-600 hover:to-green-700 transition-all duration-300" onclick="openLeadingModal()">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Add Leading</h3>
                    <i class="fas fa-plus-circle text-2xl opacity-75"></i>
                </div>
                <div class="text-xl font-bold">Create Leading</div>
                <div class="text-green-100 text-sm mt-2">Click to add new leading</div>
            </div>
            <!-- Add New Lagging -->
            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-lg p-6 text-white cursor-pointer hover:from-yellow-600 hover:to-yellow-700 transition-all duration-300" onclick="openLaggingModal()">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Add Lagging</h3>
                    <i class="fas fa-plus-circle text-2xl opacity-75"></i>
                </div>
                <div class="text-xl font-bold">Create Lagging</div>
                <div class="text-yellow-100 text-sm mt-2">Click to add new lagging</div>
            </div>
        </div>

        <!-- Add New Leading Modal -->
        <div id="addNewLeadingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Tambah KPI Leading</h3>
                    <button type="button" onclick="closeLeadingModal()" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="create_leading">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Indikator</label>
                        <input type="text" name="indicator_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Actual Value</label>
                        <input type="number" name="actual_value" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeLeadingModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">Tambah</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add New Lagging Modal -->
        <div id="addNewLaggingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Tambah KPI Lagging</h3>
                    <button type="button" onclick="closeLaggingModal()" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="create_lagging">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Indikator</label>
                        <input type="text" name="indicator_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Actual Value</label>
                        <input type="number" name="actual_value" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeLaggingModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors duration-200">Tambah</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Leading KPI Table -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Leading Indicators</h2>
                <p class="text-sm text-gray-600 mt-1">View and manage all leading KPIs</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Indicator</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Actual</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($leadingKPIs as $kpi): ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 font-medium text-gray-900"><?php echo $kpi['indicator_name']; ?></td>
                            <td class="px-6 py-4 text-black">
                                <form method="POST" class="flex items-center gap-2">
                                    <input type="hidden" name="action" value="update_leading">
                                    <input type="hidden" name="id" value="<?php echo $kpi['id']; ?>">
                                    <input type="number" name="actual_value" value="<?php echo $kpi['actual_value']; ?>" class="w-20 px-2 py-1 border border-gray-300 rounded text-xs" required>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <form method="POST" class="inline-block align-middle" style="display:inline-block;">
                                    <input type="hidden" name="action" value="update_leading">
                                    <input type="hidden" name="id" value="<?php echo $kpi['id']; ?>">
                                    <input type="number" name="actual_value" value="<?php echo $kpi['actual_value']; ?>" class="hidden">
                                    <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-xs mr-1">Simpan</button>
                                </form>
                                <form method="POST" class="inline-block align-middle" onsubmit="return confirm('Hapus KPI ini?');" style="display:inline-block;">
                                    <input type="hidden" name="action" value="delete_leading">
                                    <input type="hidden" name="id" value="<?php echo $kpi['id']; ?>">
                                    <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs"><i class="fas fa-trash-alt mr-1"></i> Hapus</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Lagging KPI Table -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Lagging Indicators</h2>
                <p class="text-sm text-gray-600 mt-1">View and manage all lagging KPIs</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Indicator</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Actual</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($laggingKPIs as $kpi): ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 font-medium text-gray-900"><?php echo $kpi['indicator_name']; ?></td>
                            <td class="px-6 py-4 text-black">
                                <form method="POST" class="flex items-center gap-2">
                                    <input type="hidden" name="action" value="update_lagging">
                                    <input type="hidden" name="id" value="<?php echo $kpi['id']; ?>">
                                    <input type="number" name="actual_value" value="<?php echo $kpi['actual_value']; ?>" class="w-20 px-2 py-1 border border-gray-300 rounded text-xs" required>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <form method="POST" class="inline-block align-middle" style="display:inline-block;">
                                    <input type="hidden" name="action" value="update_lagging">
                                    <input type="hidden" name="id" value="<?php echo $kpi['id']; ?>">
                                    <input type="number" name="actual_value" value="<?php echo $kpi['actual_value']; ?>" class="hidden">
                                    <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-xs mr-1">Simpan</button>
                                </form>
                                <form method="POST" class="inline-block align-middle" onsubmit="return confirm('Hapus KPI ini?');" style="display:inline-block;">
                                    <input type="hidden" name="action" value="delete_lagging">
                                    <input type="hidden" name="id" value="<?php echo $kpi['id']; ?>">
                                    <button type="submit" class="bg-gray-500 text-white px-3 py-1 rounded hover:bg-gray-700 text-xs"><i class="fas fa-trash-alt mr-1"></i> Hapus</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function openLeadingModal() {
            document.getElementById('addNewLaggingModal').classList.add('hidden');
            document.getElementById('addNewLeadingModal').classList.remove('hidden');
        }
        
        function openLaggingModal() {
            document.getElementById('addNewLeadingModal').classList.add('hidden');
            document.getElementById('addNewLaggingModal').classList.remove('hidden');
        }
        
        function closeLaggingModal() {
            document.getElementById('addNewLaggingModal').classList.add('hidden');
        }
        
        function closeLeadingModal() {
            document.getElementById('addNewLeadingModal').classList.add('hidden');
        }
    </script>
</body>
</html>