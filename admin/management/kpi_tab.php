<?php
require_once '../auth.php';
require_once '../../config/database.php';
requireAdminLogin();


$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    try {
        if ($_POST['action'] == 'create_leading') {
            $indicator_name = isset($_POST['indicator_name']) ? sanitize($_POST['indicator_name']) : '';
            $actual_value = isset($_POST['actual_value']) ? sanitize($_POST['actual_value']) : '';
            if ($indicator_name !== '' && $actual_value !== '') {
                $stmt = $pdo->prepare("INSERT INTO kpi_leading (indicator_name, actual_value) VALUES (?, ?)");
                $stmt->execute([$indicator_name, $actual_value]);
                $message = 'KPI Leading berhasil ditambahkan!';
            }
        } elseif ($_POST['action'] == 'delete_leading') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            if ($id > 0) {
                $stmt = $pdo->prepare("DELETE FROM kpi_leading WHERE id = ?");
                $stmt->execute([$id]);
                $message = 'KPI Leading berhasil dihapus!';
            }
        } elseif ($_POST['action'] == 'create_lagging') {
            $indicator_name = isset($_POST['indicator_name']) ? sanitize($_POST['indicator_name']) : '';
            $actual_value = isset($_POST['actual_value']) ? sanitize($_POST['actual_value']) : '';
            if ($indicator_name !== '' && $actual_value !== '') {
                $stmt = $pdo->prepare("INSERT INTO kpi_lagging (indicator_name, actual_value) VALUES (?, ?)");
                $stmt->execute([$indicator_name, $actual_value]);
                $message = 'KPI Lagging berhasil ditambahkan!';
            }
        } elseif ($_POST['action'] == 'delete_lagging') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            if ($id > 0) {
                $stmt = $pdo->prepare("DELETE FROM kpi_lagging WHERE id = ?");
                $stmt->execute([$id]);
                $message = 'KPI Lagging berhasil dihapus!';
            }
        } elseif ($_POST['action'] == 'update_leading') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $actual_value = isset($_POST['actual_value']) ? sanitize($_POST['actual_value']) : '';
            $target_value = isset($_POST['target_value']) && $_POST['target_value'] !== '' ? trim($_POST['target_value']) : null;
            $notes = isset($_POST['notes']) && $_POST['notes'] !== '' ? trim($_POST['notes']) : null;
            if ($id > 0 && $actual_value !== '') {
                $stmt = $pdo->prepare("UPDATE kpi_leading SET actual_value = ?, target_value = ?, notes = ? WHERE id = ?");
                $stmt->execute([$actual_value, $target_value, $notes, $id]);
                $message = 'KPI Leading berhasil diperbarui!';
            }
        } elseif ($_POST['action'] == 'update_lagging') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $actual_value = isset($_POST['actual_value']) ? sanitize($_POST['actual_value']) : '';
            if ($id > 0 && $actual_value !== '') {
                $stmt = $pdo->prepare("UPDATE kpi_lagging SET actual_value = ? WHERE id = ?");
                $stmt->execute([$actual_value, $id]);
                $message = 'KPI Lagging berhasil diperbarui!';
            }
        }
    } catch (Exception $e) {
        $message = 'Terjadi error: ' . $e->getMessage();
    }
}

$leadingKPIs = $pdo->query("SELECT * FROM kpi_leading ORDER BY indicator_name")->fetchAll();
$laggingKPIs = $pdo->query("SELECT * FROM kpi_lagging ORDER BY indicator_name")->fetchAll();
?>




<!-- HTML HEAD & CSS -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KPI Management</title>
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

<!-- Header mirip dashboard -->
<header class="bg-red-600 text-white px-6 py-3 shadow">

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

<!-- Hamburger Menu Navigation (single, clean) -->
<nav class="bg-white shadow mb-8">
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
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 max-w-4xl mx-auto">
    <?php echo $message; ?>
</div>
<?php endif; ?>


<div class="bg-gray-100 min-h-screen pb-16">
    <div class="max-w-7xl mx-auto px-6 py-8 font-sans">
    <!-- KPI Leading Section -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800 flex items-center"><i class="fas fa-chart-line text-blue-600 mr-2"></i>Leading Indicators</h2>
                    <form method="POST" class="flex flex-wrap gap-2 items-end">
                        <input type="hidden" name="action" value="create_leading">
                        <input type="text" name="indicator_name" placeholder="Indicator Name" required class="border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 w-48 px-2 py-1">
                        <input type="number" name="actual_value" placeholder="Actual" required class="border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 w-24 px-2 py-1">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"><i class="fas fa-plus mr-1"></i>Tambah</button>
                    </form>
        </div>
        <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-bold text-gray-700">Indicator</th>
                                <th class="px-6 py-3 text-left text-sm font-bold text-gray-700">Actual</th>
                                <th class="px-4 py-3 text-left text-sm font-bold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leadingKPIs as $kpi): ?>
                            <tr class="border-b hover:bg-blue-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900"><?php echo $kpi['indicator_name']; ?></td>
                                <td class="px-6 py-4 text-gray-500">
                                    <input type="number" value="<?php echo $kpi['actual_value']; ?>" class="border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 w-24 px-2 py-1" name="actual_value_<?php echo $kpi['id']; ?>">
                                </td>
                                <td class="px-4 py-4 flex gap-2">
                                    <button onclick="updateKPI('leading', <?php echo $kpi['id']; ?>)" type="button" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Update</button>
                                    <form method="POST" onsubmit="return confirm('Hapus KPI ini?');" style="display:inline;">
                                        <input type="hidden" name="action" value="delete_leading">
                                        <input type="hidden" name="id" value="<?php echo $kpi['id']; ?>">
                                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-700">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
        </div>
    </div>


    <!-- KPI Lagging Section -->
            <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800 flex items-center"><i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>Lagging Indicators</h2>
                    <form method="POST" class="flex flex-wrap gap-2 items-end">
                        <input type="hidden" name="action" value="create_lagging">
                        <input type="text" name="indicator_name" placeholder="Indicator Name" required class="border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 w-48 px-2 py-1">
                        <input type="number" name="actual_value" placeholder="Actual" required class="border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 w-24 px-2 py-1">
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"><i class="fas fa-plus mr-1"></i>Tambah</button>
                    </form>
        </div>
        <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-bold text-gray-700">Indicator</th>
                                <th class="px-6 py-3 text-left text-sm font-bold text-gray-700">Actual</th>
                                <th class="px-4 py-3 text-left text-sm font-bold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($laggingKPIs as $kpi): ?>
                            <tr class="border-b hover:bg-red-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900"><?php echo $kpi['indicator_name']; ?></td>
                                <td class="px-6 py-4 text-gray-500">
                                    <input type="number" value="<?php echo $kpi['actual_value']; ?>" class="border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 w-24 px-2 py-1" name="actual_value_lagging_<?php echo $kpi['id']; ?>">
                                </td>
                                <td class="px-4 py-4 flex gap-2">
                                    <button onclick="updateKPI('lagging', <?php echo $kpi['id']; ?>)" type="button" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Update</button>
                                    <form method="POST" onsubmit="return confirm('Hapus KPI ini?');" style="display:inline;">
                                        <input type="hidden" name="action" value="delete_lagging">
                                        <input type="hidden" name="id" value="<?php echo $kpi['id']; ?>">
                                        <button type="submit" class="bg-gray-500 text-white px-3 py-1 rounded hover:bg-gray-700">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
        </div>
    </div>
</div>

<!-- Footer mirip dashboard -->
<footer class="bg-gray-100 border-t border-gray-200 text-gray-600 text-center py-4 text-sm fixed bottom-0 w-full">
    &copy; <?php echo date('Y'); ?> Batamindo Investment Cakrawala. All rights reserved.
</footer>

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
function updateKPI(type, id) {
        let inputName = type === 'lagging' ? `actual_value_lagging_${id}` : `actual_value_${id}`;
        const actualValue = document.querySelector(`input[name="${inputName}"]`).value;
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
                <input type="hidden" name="action" value="update_${type}">
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="actual_value" value="${actualValue}">
        `;
        document.body.appendChild(form);
        form.submit();
}
</script>
