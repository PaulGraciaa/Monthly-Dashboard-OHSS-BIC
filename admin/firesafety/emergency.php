<?php
session_start();
require_once '../auth.php';

// Pastikan user sudah login
if (!isAdminLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

require_once '../../config/database.php';

// Periksa koneksi database
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

$error = '';
$success = '';
$data = null;
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle different actions
switch ($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $category = trim($_POST['category']);
            $jan_value = max(0, (int)$_POST['jan_value']);
            $feb_value = max(0, (int)$_POST['feb_value']);
            $mar_value = max(0, (int)$_POST['mar_value']);
            $apr_value = max(0, (int)$_POST['apr_value']);
            $may_value = max(0, (int)$_POST['may_value']);
            $jun_value = max(0, (int)$_POST['jun_value']);
            $jul_value = max(0, (int)$_POST['jul_value']);
            $aug_value = max(0, (int)$_POST['aug_value']);
            $sep_value = max(0, (int)$_POST['sep_value']);
            $oct_value = max(0, (int)$_POST['oct_value']);
            $nov_value = max(0, (int)$_POST['nov_value']);
            $dec_value = max(0, (int)$_POST['dec_value']);
            $display_order = max(0, (int)$_POST['display_order']);
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            // guard session user id (not used in INSERT because table has no created_by column)
            $created_by = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

            // Hitung grand total
            $grand_total = $jan_value + $feb_value + $mar_value + $apr_value + $may_value + $jun_value + 
                           $jul_value + $aug_value + $sep_value + $oct_value + $nov_value + $dec_value;

            if (empty($category)) {
                $error = 'Category tidak boleh kosong';
            } else {
                // Note: DB table does not have `created_by` column; do not insert it
                $query = "INSERT INTO fire_safety_emergency_activation (category, jan_value, feb_value, mar_value, apr_value, may_value, jun_value, jul_value, aug_value, sep_value, oct_value, nov_value, dec_value, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);

                if ($stmt) {
                    // types: s + 14 integers
                    mysqli_stmt_bind_param($stmt, "siiiiiiiiiiiiii", $category, $jan_value, $feb_value, $mar_value, $apr_value, $may_value, $jun_value, $jul_value, $aug_value, $sep_value, $oct_value, $nov_value, $dec_value, $display_order, $is_active);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        $success = 'Data berhasil ditambahkan';
                        header('Location: index.php');
                        exit();
                    } else {
                        $error = 'Gagal menambahkan data: ' . mysqli_error($conn);
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $error = 'Gagal mempersiapkan statement: ' . mysqli_error($conn);
                }
            }
        }
        break;

    case 'edit':
        if ($id <= 0) {
            header('Location: index.php');
            exit();
        }

        // Ambil data berdasarkan ID
        $query = "SELECT * FROM fire_safety_emergency_activation WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $data = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if (!$data) {
                header('Location: index.php');
                exit();
            }

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $category = trim($_POST['category']);
                $jan_value = max(0, (int)$_POST['jan_value']);
                $feb_value = max(0, (int)$_POST['feb_value']);
                $mar_value = max(0, (int)$_POST['mar_value']);
                $apr_value = max(0, (int)$_POST['apr_value']);
                $may_value = max(0, (int)$_POST['may_value']);
                $jun_value = max(0, (int)$_POST['jun_value']);
                $jul_value = max(0, (int)$_POST['jul_value']);
                $aug_value = max(0, (int)$_POST['aug_value']);
                $sep_value = max(0, (int)$_POST['sep_value']);
                $oct_value = max(0, (int)$_POST['oct_value']);
                $nov_value = max(0, (int)$_POST['nov_value']);
                $dec_value = max(0, (int)$_POST['dec_value']);
                $display_order = max(0, (int)$_POST['display_order']);
                $is_active = isset($_POST['is_active']) ? 1 : 0;

                // Hitung grand total
                $grand_total = $jan_value + $feb_value + $mar_value + $apr_value + $may_value + $jun_value + 
                               $jul_value + $aug_value + $sep_value + $oct_value + $nov_value + $dec_value;

                if (empty($category)) {
                    $error = 'Category tidak boleh kosong';
                } else {
                    $query = "UPDATE fire_safety_emergency_activation SET category = ?, jan_value = ?, feb_value = ?, mar_value = ?, apr_value = ?, may_value = ?, jun_value = ?, jul_value = ?, aug_value = ?, sep_value = ?, oct_value = ?, nov_value = ?, dec_value = ?, display_order = ?, is_active = ? WHERE id = ?";
                    $stmt = mysqli_prepare($conn, $query);
                    
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "siiiiiiiiiiiiiii", $category, $jan_value, $feb_value, $mar_value, $apr_value, $may_value, $jun_value, $jul_value, $aug_value, $sep_value, $oct_value, $nov_value, $dec_value, $display_order, $is_active, $id);
                        
                        if (mysqli_stmt_execute($stmt)) {
                            $success = 'Data berhasil diperbarui';
                            // Update data yang ditampilkan
                            $data['category'] = $category;
                            $data['jan_value'] = $jan_value;
                            $data['feb_value'] = $feb_value;
                            $data['mar_value'] = $mar_value;
                            $data['apr_value'] = $apr_value;
                            $data['may_value'] = $may_value;
                            $data['jun_value'] = $jun_value;
                            $data['jul_value'] = $jul_value;
                            $data['aug_value'] = $aug_value;
                            $data['sep_value'] = $sep_value;
                            $data['oct_value'] = $oct_value;
                            $data['nov_value'] = $nov_value;
                            $data['dec_value'] = $dec_value;
                            $data['grand_total'] = $grand_total;
                            $data['display_order'] = $display_order;
                            $data['is_active'] = $is_active;
                        } else {
                            $error = 'Gagal memperbarui data: ' . mysqli_error($conn);
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        $error = 'Gagal mempersiapkan statement: ' . mysqli_error($conn);
                    }
                }
            }
        } else {
            $error = 'Gagal mempersiapkan statement: ' . mysqli_error($conn);
        }
        break;

    case 'delete':
        if ($id <= 0) {
            header('Location: index.php');
            exit();
        }

        // Hapus data (soft delete - set is_active = 0)
        $query = "UPDATE fire_safety_emergency_activation SET is_active = 0 WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success_message'] = 'Data berhasil dihapus';
            } else {
                $_SESSION['error_message'] = 'Gagal menghapus data: ' . mysqli_error($conn);
            }

            mysqli_stmt_close($stmt);
        } else {
            $_SESSION['error_message'] = 'Gagal mempersiapkan statement: ' . mysqli_error($conn);
        }
        
        header('Location: index.php');
        exit();
        break;

    default:
        // Default action is 'list' - redirect to main index
        header('Location: index.php');
        exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($action == 'create' ? 'Tambah' : 'Edit'); ?> Emergency Activation - Batamindo</title>
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
    <style>
        /* Notification Animation */
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
                    <a href="index.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-fire-extinguisher mr-1"></i> Fire Safety
                    </a>
                    <a href="performance.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-chart-line mr-1"></i> Performance
                    </a>
                    <a href="emergency.php" class="bg-red-800 text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Emergency
                    </a>
                    <a href="details.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-list mr-1"></i> Details
                    </a>
                    <a href="enforcement.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-gavel mr-1"></i> Enforcement
                    </a>
                    <a href="maintenance.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-tools mr-1"></i> Maintenance
                    </a>
                    <a href="statistics.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-chart-bar mr-1"></i> Statistics
                    </a>
                    <a href="repair.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-wrench mr-1"></i> Repair
                    </a>
                    <a href="repair_details.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-list-alt mr-1"></i> Repair Details
                    </a>
                    <a href="drills.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-running mr-1"></i> Drills
                    </a>
                </nav>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-<?php echo ($action == 'create' ? 'plus' : 'edit'); ?> text-red-600 mr-3"></i>
                    <?php echo ($action == 'create' ? 'Tambah' : 'Edit'); ?> Emergency Activation
                </h1>
                <p class="text-gray-600 mt-2"><?php echo ($action == 'create' ? 'Add new emergency activation data' : 'Edit existing emergency activation data'); ?></p>
            </div>
            <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                <i class="fas fa-arrow-left mr-1"></i> Back to List
            </a>
        </div>

        <!-- Notification System -->
        <?php if ($error): ?>
        <div id="errorNotifBox" class="fixed top-8 right-8 z-50 min-w-[260px] max-w-xs bg-white border border-red-400 shadow-2xl rounded-xl flex items-center px-5 py-4 gap-3 notif-animate-in" style="box-shadow:0 8px 32px 0 rgba(239,68,68,0.15);">
            <div class="flex-shrink-0">
                <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-red-100">
                    <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
                </span>
            </div>
            <div class="flex-1 text-red-800 font-semibold text-sm">
                <?php echo $error; ?>
            </div>
            <button onclick="closeErrorNotif()" class="ml-2 text-red-400 hover:text-red-700 focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <script>
        function closeErrorNotif() {
            var notif = document.getElementById('errorNotifBox');
            if (notif) {
                notif.classList.remove('notif-animate-in');
                notif.classList.add('notif-animate-out');
                setTimeout(function(){ notif.remove(); }, 500);
            }
        }
        setTimeout(closeErrorNotif, 5000);
        </script>
        <?php endif; ?>

        <?php if ($success): ?>
        <div id="successNotifBox" class="fixed top-8 right-8 z-50 min-w-[260px] max-w-xs bg-white border border-green-400 shadow-2xl rounded-xl flex items-center px-5 py-4 gap-3 notif-animate-in" style="box-shadow:0 8px 32px 0 rgba(34,197,94,0.15);">
            <div class="flex-shrink-0">
                <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-green-100">
                    <i class="fas fa-check text-green-600 text-xl"></i>
                </span>
            </div>
            <div class="flex-1 text-green-800 font-semibold text-sm">
                <?php echo $success; ?>
            </div>
            <button onclick="closeSuccessNotif()" class="ml-2 text-green-400 hover:text-green-700 focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <script>
        function closeSuccessNotif() {
            var notif = document.getElementById('successNotifBox');
            if (notif) {
                notif.classList.remove('notif-animate-in');
                notif.classList.add('notif-animate-out');
                setTimeout(function(){ notif.remove(); }, 500);
            }
        }
        setTimeout(closeSuccessNotif, 5000);
        </script>
        <?php endif; ?>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-<?php echo ($action == 'create' ? 'plus' : 'edit'); ?> text-red-600 mr-3"></i>
                    <?php echo ($action == 'create' ? 'Form Tambah Data' : 'Form Edit Data'); ?>
                </h2>
                <p class="text-sm text-gray-600 mt-1"><?php echo ($action == 'create' ? 'Fill in the form below to add new emergency activation data' : 'Update the emergency activation data below'); ?></p>
            </div>
            
            <div class="p-6">
                <form method="POST" class="space-y-6">
                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="category" name="category" 
                                   value="<?php echo ($action == 'edit' ? htmlspecialchars($data['category']) : (isset($_POST['category']) ? htmlspecialchars($_POST['category']) : '')); ?>" 
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition-colors duration-200"
                                   placeholder="Enter category name...">
                            <p class="text-sm text-gray-500 mt-1">Contoh: Fire Incident, Non-Rescue, Technical Call, dll</p>
                        </div>
                        <div>
                            <label for="display_order" class="block text-sm font-medium text-gray-700 mb-2">
                                Display Order
                            </label>
                            <input type="number" id="display_order" name="display_order" 
                                   value="<?php echo ($action == 'edit' ? $data['display_order'] : (isset($_POST['display_order']) ? $_POST['display_order'] : '0')); ?>" 
                                   min="0"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition-colors duration-200">
                            <p class="text-sm text-gray-500 mt-1">Urutan tampil data (0 = paling atas)</p>
                        </div>
                    </div>

                    <!-- Monthly Values Section -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-calendar-alt text-red-600 mr-2"></i>
                            Monthly Values 2025
                        </h3>
                        
                        <!-- First Row - Q1 & Q2 -->
                        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-4">
                            <div>
                                <label for="jan_value" class="block text-sm font-medium text-gray-700 mb-1">January</label>
                                <input type="number" id="jan_value" name="jan_value" 
                                       value="<?php echo ($action == 'edit' ? $data['jan_value'] : (isset($_POST['jan_value']) ? $_POST['jan_value'] : '0')); ?>" 
                                       min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition-colors duration-200 monthly-input">
                            </div>
                            <div>
                                <label for="feb_value" class="block text-sm font-medium text-gray-700 mb-1">February</label>
                                <input type="number" id="feb_value" name="feb_value" 
                                       value="<?php echo ($action == 'edit' ? $data['feb_value'] : (isset($_POST['feb_value']) ? $_POST['feb_value'] : '0')); ?>" 
                                       min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition-colors duration-200 monthly-input">
                            </div>
                            <div>
                                <label for="mar_value" class="block text-sm font-medium text-gray-700 mb-1">March</label>
                                <input type="number" id="mar_value" name="mar_value" 
                                       value="<?php echo ($action == 'edit' ? $data['mar_value'] : (isset($_POST['mar_value']) ? $_POST['mar_value'] : '0')); ?>" 
                                       min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition-colors duration-200 monthly-input">
                            </div>
                            <div>
                                <label for="apr_value" class="block text-sm font-medium text-gray-700 mb-1">April</label>
                                <input type="number" id="apr_value" name="apr_value" 
                                       value="<?php echo ($action == 'edit' ? $data['apr_value'] : (isset($_POST['apr_value']) ? $_POST['apr_value'] : '0')); ?>" 
                                       min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition-colors duration-200 monthly-input">
                            </div>
                            <div>
                                <label for="may_value" class="block text-sm font-medium text-gray-700 mb-1">May</label>
                                <input type="number" id="may_value" name="may_value" 
                                       value="<?php echo ($action == 'edit' ? $data['may_value'] : (isset($_POST['may_value']) ? $_POST['may_value'] : '0')); ?>" 
                                       min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition-colors duration-200 monthly-input">
                            </div>
                            <div>
                                <label for="jun_value" class="block text-sm font-medium text-gray-700 mb-1">June</label>
                                <input type="number" id="jun_value" name="jun_value" 
                                       value="<?php echo ($action == 'edit' ? $data['jun_value'] : (isset($_POST['jun_value']) ? $_POST['jun_value'] : '0')); ?>" 
                                       min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition-colors duration-200 monthly-input">
                            </div>
                        </div>

                        <!-- Second Row - Q3 & Q4 -->
                        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-4">
                            <div>
                                <label for="jul_value" class="block text-sm font-medium text-gray-700 mb-1">July</label>
                    <input type="number" id="jul_value" name="jul_value" 
                        value="<?php echo ($action == 'edit' && isset($data['jul_value']) ? $data['jul_value'] : (isset($_POST['jul_value']) ? $_POST['jul_value'] : '0')); ?>" 
                                       min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition-colors duration-200 monthly-input">
                            </div>
                            <div>
                                <label for="aug_value" class="block text-sm font-medium text-gray-700 mb-1">August</label>
                    <input type="number" id="aug_value" name="aug_value" 
                        value="<?php echo ($action == 'edit' && isset($data['aug_value']) ? $data['aug_value'] : (isset($_POST['aug_value']) ? $_POST['aug_value'] : '0')); ?>" 
                                       min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition-colors duration-200 monthly-input">
                            </div>
                            <div>
                                <label for="sep_value" class="block text-sm font-medium text-gray-700 mb-1">September</label>
                    <input type="number" id="sep_value" name="sep_value" 
                        value="<?php echo ($action == 'edit' && isset($data['sep_value']) ? $data['sep_value'] : (isset($_POST['sep_value']) ? $_POST['sep_value'] : '0')); ?>" 
                                       min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition-colors duration-200 monthly-input">
                            </div>
                            <div>
                                <label for="oct_value" class="block text-sm font-medium text-gray-700 mb-1">October</label>
                    <input type="number" id="oct_value" name="oct_value" 
                        value="<?php echo ($action == 'edit' && isset($data['oct_value']) ? $data['oct_value'] : (isset($_POST['oct_value']) ? $_POST['oct_value'] : '0')); ?>" 
                                       min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition-colors duration-200 monthly-input">
                            </div>
                            <div>
                                <label for="nov_value" class="block text-sm font-medium text-gray-700 mb-1">November</label>
                    <input type="number" id="nov_value" name="nov_value" 
                        value="<?php echo ($action == 'edit' && isset($data['nov_value']) ? $data['nov_value'] : (isset($_POST['nov_value']) ? $_POST['nov_value'] : '0')); ?>" 
                                       min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition-colors duration-200 monthly-input">
                            </div>
                            <div>
                                <label for="dec_value" class="block text-sm font-medium text-gray-700 mb-1">December</label>
                    <input type="number" id="dec_value" name="dec_value" 
                        value="<?php echo ($action == 'edit' && isset($data['dec_value']) ? $data['dec_value'] : (isset($_POST['dec_value']) ? $_POST['dec_value'] : '0')); ?>" 
                                       min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition-colors duration-200 monthly-input">
                            </div>
                        </div>
                    </div>

                    <!-- Grand Total Display -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Grand Total</label>
                                <input type="text" id="grand_total_display" 
                                       value="<?php echo ($action == 'edit' ? $data['grand_total'] : (isset($_POST['grand_total']) ? $_POST['grand_total'] : '0')); ?>" 
                                       readonly
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 text-gray-700 font-semibold">
                                <p class="text-sm text-gray-500 mt-1">Total otomatis dihitung dari semua bulan</p>
                            </div>
                        </div>
                    </div>

                    <!-- Active Status -->
                    <div class="flex items-center">
                        <input type="checkbox" id="is_active" name="is_active" 
                            <?php echo ( ($action == 'edit' && $data['is_active']) || ($action == 'create' && (!isset($_POST['is_active']) || $_POST['is_active'])) ) ? 'checked' : ''; ?>
                            class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">
                            Aktif
                        </label>
                        <p class="text-sm text-gray-500 ml-4">Centang untuk menampilkan data ini</p>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <a href="index.php" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                            <i class="fas fa-save mr-2"></i><?php echo ($action == 'create' ? 'Save' : 'Save Changes'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Add animation to form elements
        document.querySelectorAll('input').forEach(element => {
            element.classList.add('transition-all', 'duration-200');
        });

        // Auto-hide notification messages after 5 seconds
        const notifications = document.querySelectorAll('[id$="NotifBox"]');
        notifications.forEach(notif => {
            setTimeout(() => {
                if (notif) {
                    notif.style.opacity = '0';
                    notif.style.transition = 'opacity 0.5s ease-out';
                    setTimeout(() => notif.remove(), 500);
                }
            }, 5000);
        });

        // Auto-calculate grand total
        function calculateTotal() {
            const inputs = ['jan_value', 'feb_value', 'mar_value', 'apr_value', 'may_value', 'jun_value',
                          'jul_value', 'aug_value', 'sep_value', 'oct_value', 'nov_value', 'dec_value'];
            let total = 0;
            inputs.forEach(inputId => {
                const value = parseInt(document.getElementById(inputId).value) || 0;
                total += value;
            });
            return total;
        }

        // Update grand total display
        function updateGrandTotalDisplay() {
            const total = calculateTotal();
            document.getElementById('grand_total_display').value = total;
        }

        // Add event listeners to monthly inputs for auto-calculation
        const monthlyInputs = document.querySelectorAll('.monthly-input');
        monthlyInputs.forEach(input => {
            input.addEventListener('input', function() {
                updateGrandTotalDisplay();
            });
        });

        // Initialize grand total on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateGrandTotalDisplay();
        });
    </script>
</body>
</html>