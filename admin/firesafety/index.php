<?php
session_start();
require_once '../auth.php';

// Pastikan user sudah login
if (!isAdminLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

require_once '../../config/database.php';

// Ambil data Fire Safety Performance Summary
$query = "SELECT * FROM fire_safety_performance WHERE is_active = 1 ORDER BY display_order ASC";
$result = mysqli_query($conn, $query);
$performance_data = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Ambil data Emergency Activation
$query_emergency = "SELECT * FROM fire_safety_emergency_activation WHERE is_active = 1 ORDER BY display_order ASC";
$result_emergency = mysqli_query($conn, $query_emergency);
$emergency_data = mysqli_fetch_all($result_emergency, MYSQLI_ASSOC);

// Ambil data Emergency Details
$query_details = "SELECT * FROM fire_safety_emergency_details WHERE is_active = 1 ORDER BY display_order ASC";
$result_details = mysqli_query($conn, $query_details);
$details_data = mysqli_fetch_all($result_details, MYSQLI_ASSOC);

// Ambil data Fire Safety Enforcement
$query_enforcement = "SELECT * FROM fire_safety_enforcement WHERE is_active = 1 ORDER BY display_order ASC";
$result_enforcement = mysqli_query($conn, $query_enforcement);
$enforcement_data = mysqli_fetch_all($result_enforcement, MYSQLI_ASSOC);

// Ambil data Fire Equipment Maintenance
$query_maintenance = "SELECT * FROM fire_equipment_maintenance WHERE is_active = 1 ORDER BY display_order ASC";
$result_maintenance = mysqli_query($conn, $query_maintenance);
$maintenance_data = mysqli_fetch_all($result_maintenance, MYSQLI_ASSOC);

// Ambil data Fire Equipment Statistics
$query_statistics = "SELECT * FROM fire_equipment_statistics WHERE is_active = 1 ORDER BY display_order ASC";
$result_statistics = mysqli_query($conn, $query_statistics);
$statistics_data = mysqli_fetch_all($result_statistics, MYSQLI_ASSOC);

// Ambil data Fire Safety Repair Impairment
$query_repair = "SELECT * FROM fire_safety_repair_impairment WHERE is_active = 1 ORDER BY display_order ASC";
$result_repair = mysqli_query($conn, $query_repair);
$repair_data = mysqli_fetch_all($result_repair, MYSQLI_ASSOC);

// Ambil data Fire Safety Repair Details
$query_repair_details = "SELECT * FROM fire_safety_repair_details WHERE is_active = 1 ORDER BY display_order ASC";
$result_repair_details = mysqli_query($conn, $query_repair_details);
$repair_details_data = mysqli_fetch_all($result_repair_details, MYSQLI_ASSOC);

// Ambil data Fire Safety Drills
$query_drills = "SELECT * FROM fire_safety_drills WHERE is_active = 1 ORDER BY display_order ASC";
$result_drills = mysqli_query($conn, $query_drills);
$drills_data = mysqli_fetch_all($result_drills, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fire Safety Management - Batamindo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FF0000',
                        secondary: '#1a1a1a',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 font-sans">
    <!-- Red Header Section -->
    <div class="bg-gradient-to-r from-red-600 to-red-800">
        <header class="text-white py-4">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-4">
                        <img src="../../img/batamindo.png" alt="Batamindo" class="h-12 w-auto bg-white p-1 rounded">
                        <div>
                            <h1 class="text-2xl font-bold text-white">Batamindo Industrial Park</h1>
                            <p class="text-red-200">Fire Safety Management System</p>
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
                    <a href="../management/activities_tab.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-tasks mr-1"></i> Activities
                    </a>
                    <a href="../OHS/index.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-shield-alt mr-1"></i> OHS
                    </a>
                    <a href="../security/index.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-user-shield mr-1"></i> Security
                    </a>
                    <a href="index.php" class="bg-red-700 text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-fire-extinguisher mr-1"></i> Fire Safety
                    </a>
                    <a href="../surveillance/index.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-video mr-1"></i> Surveillance
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

        <!-- Quick Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <?php 
            $total_performance = count($performance_data);
            $total_emergency = count($emergency_data);
            $total_details = count($details_data);
            $total_enforcement = count($enforcement_data);
            ?>
            <!-- Performance Summary -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Performance</h3>
                    <i class="fas fa-chart-line text-2xl opacity-75"></i>
                </div>
                <div class="text-3xl font-bold"><?php echo $total_performance; ?></div>
                <div class="text-blue-100 text-sm mt-2">Performance summaries</div>
            </div>

            <!-- Emergency Activations -->
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Emergency</h3>
                    <i class="fas fa-exclamation-triangle text-2xl opacity-75"></i>
                </div>
                <div class="text-3xl font-bold"><?php echo $total_emergency; ?></div>
                <div class="text-red-100 text-sm mt-2">Emergency activations</div>
            </div>

            <!-- Equipment Maintenance -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Maintenance</h3>
                    <i class="fas fa-tools text-2xl opacity-75"></i>
                </div>
                <div class="text-3xl font-bold"><?php echo count($maintenance_data); ?></div>
                <div class="text-green-100 text-sm mt-2">Maintenance records</div>
                </div>

            <!-- Safety Drills -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Drills</h3>
                    <i class="fas fa-running text-2xl opacity-75"></i>
                </div>
                <div class="text-3xl font-bold"><?php echo count($drills_data); ?></div>
                <div class="text-purple-100 text-sm mt-2">Safety drills conducted</div>
            </div>
                </div>

                <!-- Fire Safety Performance Summary -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-chart-line text-red-600 mr-3"></i>
                            Fire Safety Performance Summary
                    </h2>
                    <a href="performance.php?action=create" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-plus mr-1"></i> Add New
                            </a>
                </div>
                    </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">No</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Summary Text</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Display Order</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Actions</th>
                                    </tr>
                                </thead>
                    <tbody class="divide-y divide-gray-200">
                                    <?php if (empty($performance_data)): ?>
                                        <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>No performance data available</p>
                                </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($performance_data as $index => $item): ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= $index + 1 ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($item['summary_text']) ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['display_order'] ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="performance.php?action=edit&id=<?= $item['id'] ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition-colors duration-200">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </a>
                                            <a href="performance.php?action=delete&id=<?= $item['id'] ?>" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition-colors duration-200" onclick="return confirm('Are you sure you want to delete this item?')">
                                                <i class="fas fa-trash mr-1"></i> Delete
                                            </a>
                                        </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                    </div>
                </div>

                <!-- Emergency Activation -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-600 mr-3"></i>
                            Emergency Activation
                    </h2>
                    <a href="emergency.php?action=create" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-plus mr-1"></i> Add New
                            </a>
                </div>
                    </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Category</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Jan</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Feb</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Mar</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Apr</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">May</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Jun</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Total</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Actions</th>
                                    </tr>
                                </thead>
                    <tbody class="divide-y divide-gray-200">
                                    <?php if (empty($emergency_data)): ?>
                                        <tr>
                                <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>No emergency activation data available</p>
                                </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($emergency_data as $item): ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= htmlspecialchars($item['category']) ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['jan_value'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['feb_value'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['mar_value'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['apr_value'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['may_value'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['jun_value'] ?></td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900 text-center"><?= $item['grand_total'] ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="emergency.php?action=edit&id=<?= $item['id'] ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition-colors duration-200">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </a>
                                            <a href="emergency.php?action=delete&id=<?= $item['id'] ?>" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition-colors duration-200" onclick="return confirm('Are you sure you want to delete this item?')">
                                                <i class="fas fa-trash mr-1"></i> Delete
                                            </a>
                                        </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                    </div>
                </div>

                <!-- Emergency Details -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-list text-red-600 mr-3"></i>
                            Emergency Details
                    </h2>
                    <a href="details.php?action=create" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-plus mr-1"></i> Add New
                            </a>
                </div>
                    </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">S/N</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Date</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Category</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Sub Category</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Description</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Location</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Actions</th>
                                    </tr>
                                </thead>
                    <tbody class="divide-y divide-gray-200">
                                    <?php if (empty($details_data)): ?>
                                        <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>No emergency details available</p>
                                </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($details_data as $item): ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= $item['serial_number'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?= date('d-M-y', strtotime($item['incident_date'])) ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($item['category']) ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($item['sub_category']) ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars(substr($item['description'], 0, 50)) ?>...</td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($item['location']) ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="details.php?action=edit&id=<?= $item['id'] ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition-colors duration-200">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </a>
                                            <a href="details.php?action=delete&id=<?= $item['id'] ?>" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition-colors duration-200" onclick="return confirm('Are you sure you want to delete this item?')">
                                                <i class="fas fa-trash mr-1"></i> Delete
                                            </a>
                                        </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                    </div>
                </div>

                <!-- Fire Safety Enforcement -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-gavel text-red-600 mr-3"></i>
                        Fire Safety Enforcement
                    </h2>
                    <a href="enforcement.php?action=create" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-plus mr-1"></i> Add New
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Category</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Jan</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Feb</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Mar</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Apr</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">May</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Jun</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Jul</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Aug</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Sep</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Oct</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Nov</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Dec</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Total</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($enforcement_data)): ?>
                            <tr>
                                <td colspan="15" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>No enforcement data available</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($enforcement_data as $item): ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= htmlspecialchars($item['category']) ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['Jan'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['Feb'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['Mar'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['Apr'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['May'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['Jun'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['Jul'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['Aug'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['Sep'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['Oct'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['Nov'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['Dec'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['Total'] ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="enforcement.php?action=edit&id=<?= $item['id'] ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition-colors duration-200">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </a>
                                            <a href="enforcement.php?action=delete&id=<?= $item['id'] ?>" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition-colors duration-200" onclick="return confirm('Are you sure you want to delete this item?')">
                                                <i class="fas fa-trash mr-1"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

                <!-- Fire Equipment Maintenance -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-tools text-red-600 mr-3"></i>
                            Fire Equipment Maintenance
                    </h2>
                    <a href="maintenance.php?action=create" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-plus mr-1"></i> Add New
                            </a>
                </div>
                    </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">S/N</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Date</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Location</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Actions</th>
                                    </tr>
                                </thead>
                    <tbody class="divide-y divide-gray-200">
                                    <?php if (empty($maintenance_data)): ?>
                                        <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>No maintenance data available</p>
                                </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($maintenance_data as $item): ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= $item['serial_number'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?= date('d-M-y', strtotime($item['maintenance_date'])) ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($item['location']) ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="maintenance.php?action=edit&id=<?= $item['id'] ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition-colors duration-200">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </a>
                                            <a href="maintenance.php?action=delete&id=<?= $item['id'] ?>" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition-colors duration-200" onclick="return confirm('Are you sure you want to delete this item?')">
                                                <i class="fas fa-trash mr-1"></i> Delete
                                            </a>
                                        </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                    </div>
                </div>

                <!-- Fire Equipment Statistics -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-chart-bar text-red-600 mr-3"></i>
                            Fire Equipment Statistics
                    </h2>
                    <a href="statistics.php?action=create" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-plus mr-1"></i> Add New
                            </a>
                </div>
                    </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Equipment Type</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Jan</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Feb</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Mar</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Apr</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">May</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Jun</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Total</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Actions</th>
                                    </tr>
                                </thead>
                    <tbody class="divide-y divide-gray-200">
                                    <?php if (empty($statistics_data)): ?>
                                        <tr>
                                <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>No equipment statistics available</p>
                                </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($statistics_data as $item): ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= htmlspecialchars($item['equipment_type']) ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['jan_count'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['feb_count'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['mar_count'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['apr_count'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['may_count'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['jun_count'] ?></td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900 text-center"><?= $item['grand_total'] ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="statistics.php?action=edit&id=<?= $item['id'] ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition-colors duration-200">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </a>
                                            <a href="statistics.php?action=delete&id=<?= $item['id'] ?>" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition-colors duration-200" onclick="return confirm('Are you sure you want to delete this item?')">
                                                <i class="fas fa-trash mr-1"></i> Delete
                                            </a>
                                        </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                    </div>
                </div>

                <!-- Fire Safety Repair Impairment -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-wrench text-red-600 mr-3"></i>
                            Fire Safety Repair Impairment
                    </h2>
                    <a href="repair.php?action=create" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-plus mr-1"></i> Add New
                            </a>
                </div>
                    </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Month</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Repair Count</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Year</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Actions</th>
                                    </tr>
                                </thead>
                    <tbody class="divide-y divide-gray-200">
                                    <?php if (empty($repair_data)): ?>
                                        <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>No repair data available</p>
                                </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($repair_data as $item): ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= $item['month_name'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['repair_count'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?= $item['year'] ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="repair.php?action=edit&id=<?= $item['id'] ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition-colors duration-200">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </a>
                                            <a href="repair.php?action=delete&id=<?= $item['id'] ?>" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition-colors duration-200" onclick="return confirm('Are you sure you want to delete this item?')">
                                                <i class="fas fa-trash mr-1"></i> Delete
                                            </a>
                                        </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                    </div>
                </div>

                <!-- Fire Safety Repair Details -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-list-alt text-red-600 mr-3"></i>
                            Fire Safety Repair Details
                    </h2>
                    <a href="repair_details.php?action=create" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-plus mr-1"></i> Add New
                            </a>
                </div>
                    </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">S/No</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Date</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Project Name</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Location</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Actions</th>
                                    </tr>
                                </thead>
                    <tbody class="divide-y divide-gray-200">
                                    <?php if (empty($repair_details_data)): ?>
                                        <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>No repair details available</p>
                                </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($repair_details_data as $item): ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= $item['serial_number'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?= date('d-M-y', strtotime($item['repair_date'])) ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars(substr($item['project_name'], 0, 50)) ?>...</td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($item['location']) ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars(substr($item['status'], 0, 30)) ?>...</td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="repair_details.php?action=edit&id=<?= $item['id'] ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition-colors duration-200">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </a>
                                            <a href="repair_details.php?action=delete&id=<?= $item['id'] ?>" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition-colors duration-200" onclick="return confirm('Are you sure you want to delete this item?')">
                                                <i class="fas fa-trash mr-1"></i> Delete
                                            </a>
                                        </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                    </div>
                </div>

                <!-- Fire Safety Drills -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-running text-red-600 mr-3"></i>
                            Fire Safety Drills & Training
                    </h2>
                    <a href="drills.php?action=create" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-plus mr-1"></i> Add New
                            </a>
                </div>
                    </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">S/No</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Date</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Location</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Subject</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Type</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Actions</th>
                                    </tr>
                                </thead>
                    <tbody class="divide-y divide-gray-200">
                                    <?php if (empty($drills_data)): ?>
                                        <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>No drills data available</p>
                                </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($drills_data as $item): ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= $item['serial_number'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?= date('d-M-y', strtotime($item['drill_date'])) ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($item['location']) ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($item['subject']) ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $item['drill_type'] == 'drill' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' ?>">
                                                        <?= ucfirst($item['drill_type']) ?>
                                                    </span>
                                                </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="drills.php?action=edit&id=<?= $item['id'] ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition-colors duration-200">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </a>
                                            <a href="drills.php?action=delete&id=<?= $item['id'] ?>" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition-colors duration-200" onclick="return confirm('Are you sure you want to delete this item?')">
                                                <i class="fas fa-trash mr-1"></i> Delete
                                            </a>
                                        </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

    <script>
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
