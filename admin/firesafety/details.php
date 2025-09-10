<?php
session_start();
require_once '../auth.php';

// Pastikan user sudah login
if (!isAdminLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

require_once '../../config/database.php';

$error = '';
$success = '';
$data = null;
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// helper: normalize/validate date input and return Y-m-d or false
function normalize_date_for_db($input)
{
    $input = trim($input);
    if ($input === '') {
        return false;
    }

    // numeric timestamps
    if (ctype_digit($input)) {
        $ts = (int)$input;
        // handle possible milliseconds
        if ($ts > 9999999999) {
            $ts = (int)($ts / 1000);
        }
        try {
            $d = new DateTime('@' . $ts);
            $d->setTimezone(new DateTimeZone(date_default_timezone_get()));
        } catch (Exception $e) {
            $d = false;
        }
    } else {
        // try Y-m-d
        $d = DateTime::createFromFormat('Y-m-d', $input);
        if (!$d) {
            // try d/m/Y
            $d = DateTime::createFromFormat('d/m/Y', $input);
        }
        if (!$d) {
            // fallback to strtotime
            $ts = strtotime($input);
            if ($ts !== false) {
                $d = new DateTime('@' . $ts);
                $d->setTimezone(new DateTimeZone(date_default_timezone_get()));
            } else {
                $d = false;
            }
        }
    }

    if (!$d) {
        return false;
    }

    $year = (int)$d->format('Y');
    if ($year < 1900 || $year > 2100) {
        return false;
    }

    return $d->format('Y-m-d');
}

// Handle different actions
switch ($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $serial_number = (int)$_POST['serial_number'];
            $incident_date_raw = isset($_POST['incident_date']) ? $_POST['incident_date'] : '';
            $incident_date = normalize_date_for_db($incident_date_raw);
            $category = mysqli_real_escape_string($conn, $_POST['category']);
            $sub_category = mysqli_real_escape_string($conn, $_POST['sub_category']);
            $description = mysqli_real_escape_string($conn, $_POST['description']);
            $location = mysqli_real_escape_string($conn, $_POST['location']);
            $display_order = (int)$_POST['display_order'];
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            // Ensure session user id is available; default to 0 if not set
            $created_by = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

            if (empty($category) || empty($sub_category) || empty($description) || empty($location)) {
                $error = 'Semua field wajib diisi';
            } elseif ($incident_date === false) {
                $error = 'Tanggal tidak valid. Gunakan format YYYY-MM-DD.';
            } else {
                // Note: DB schema for fire_safety_emergency_details does not include created_by column
                $query = "INSERT INTO fire_safety_emergency_details (serial_number, incident_date, category, sub_category, description, location, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                // types: i (serial_number), s (incident_date), s (category), s (sub_category), s (description), s (location), i (display_order), i (is_active)
                mysqli_stmt_bind_param($stmt, "isssssii", $serial_number, $incident_date, $category, $sub_category, $description, $location, $display_order, $is_active);
                
                if (mysqli_stmt_execute($stmt)) {
                    $success = 'Data berhasil ditambahkan';
                    header('Location: index.php');
                    exit();
                } else {
                    $error = 'Gagal menambahkan data: ' . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            }
        }
        break;

    case 'edit':
        if ($id <= 0) {
            header('Location: index.php');
            exit();
        }

        // Ambil data berdasarkan ID
        $query = "SELECT * FROM fire_safety_emergency_details WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
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
            $serial_number = (int)$_POST['serial_number'];
            $incident_date_raw = isset($_POST['incident_date']) ? $_POST['incident_date'] : '';
            $incident_date = normalize_date_for_db($incident_date_raw);
            $category = mysqli_real_escape_string($conn, $_POST['category']);
            $sub_category = mysqli_real_escape_string($conn, $_POST['sub_category']);
            $description = mysqli_real_escape_string($conn, $_POST['description']);
            $location = mysqli_real_escape_string($conn, $_POST['location']);
            $display_order = (int)$_POST['display_order'];
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            if (empty($category) || empty($sub_category) || empty($description) || empty($location)) {
                $error = 'Semua field wajib diisi';
            } elseif ($incident_date === false) {
                $error = 'Tanggal tidak valid. Gunakan format YYYY-MM-DD.';
            } else {
                $query = "UPDATE fire_safety_emergency_details SET serial_number = ?, incident_date = ?, category = ?, sub_category = ?, description = ?, location = ?, display_order = ?, is_active = ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "isssssiii", $serial_number, $incident_date, $category, $sub_category, $description, $location, $display_order, $is_active, $id);
                
                if (mysqli_stmt_execute($stmt)) {
                    $success = 'Data berhasil diperbarui';
                    // Update data yang ditampilkan
                    $data['serial_number'] = $serial_number;
                    $data['incident_date'] = $incident_date;
                    $data['category'] = $category;
                    $data['sub_category'] = $sub_category;
                    $data['description'] = $description;
                    $data['location'] = $location;
                    $data['display_order'] = $display_order;
                    $data['is_active'] = $is_active;
                } else {
                    $error = 'Gagal memperbarui data: ' . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            }
        }
        break;

    case 'delete':
        if ($id <= 0) {
            header('Location: index.php');
            exit();
        }

        // Hapus data (soft delete - set is_active = 0)
        $query = "UPDATE fire_safety_emergency_details SET is_active = 0 WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = 'Data berhasil dihapus';
        } else {
            $_SESSION['error_message'] = 'Gagal menghapus data: ' . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
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
    <title><?php echo ($action == 'create' ? 'Tambah' : 'Edit'); ?> Emergency Details - Batamindo</title>
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
        <div class="border-t border-red-500/30">
            <div class="max-w-7xl mx-auto px-4 py-2">
                <nav class="flex space-x-4">
                    <a href="index.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-fire-extinguisher mr-1"></i> Fire Safety
                    </a>
                    <a href="performance.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-chart-line mr-1"></i> Performance
                    </a>
                    <a href="emergency.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Emergency
                    </a>
                    <a href="details.php" class="bg-red-800 text-white px-3 py-2 rounded-md text-sm font-medium">
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
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-<?php echo ($action == 'create' ? 'plus' : 'edit'); ?> text-red-600 mr-3"></i>
                    <?php echo ($action == 'create' ? 'Tambah' : 'Edit'); ?> Emergency Details
                </h1>
                <p class="text-gray-600 mt-2"><?php echo ($action == 'create' ? 'Add new emergency details' : 'Edit emergency details'); ?></p>
            </div>
            <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                <i class="fas fa-arrow-left mr-1"></i> Back to List
            </a>
        </div>

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

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-<?php echo ($action == 'create' ? 'plus' : 'edit'); ?> text-red-600 mr-3"></i>
                    <?php echo ($action == 'create' ? 'Form Tambah Data' : 'Form Edit Data'); ?>
                </h2>
                <p class="text-sm text-gray-600 mt-1"><?php echo ($action == 'create' ? 'Fill in the form below to add new emergency detail' : 'Update the emergency detail below'); ?></p>
            </div>
            <div class="p-6">
                <form method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="serial_number" class="block text-sm font-medium text-gray-700 mb-1">Serial Number <span class="text-red-500">*</span></label>
                            <input type="number" id="serial_number" name="serial_number" required min="1"
                                   value="<?php echo ($action == 'edit' ? $data['serial_number'] : (isset($_POST['serial_number']) ? $_POST['serial_number'] : '')); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                        </div>
                        <div>
                            <label for="incident_date" class="block text-sm font-medium text-gray-700 mb-1">Incident Date <span class="text-red-500">*</span></label>
                            <input type="date" id="incident_date" name="incident_date" required
                                   value="<?php echo ($action == 'edit' ? $data['incident_date'] : (isset($_POST['incident_date']) ? $_POST['incident_date'] : '')); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                        </div>
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                            <select id="category" name="category" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                                <option value="">Pilih Category</option>
                                <option value="Fire Incident" <?php echo (($action == 'edit' && $data['category'] == 'Fire Incident') || (isset($_POST['category']) && $_POST['category'] == 'Fire Incident')) ? 'selected' : ''; ?>>Fire Incident</option>
                                <option value="Non-Rescue" <?php echo (($action == 'edit' && $data['category'] == 'Non-Rescue') || (isset($_POST['category']) && $_POST['category'] == 'Non-Rescue')) ? 'selected' : ''; ?>>Non-Rescue</option>
                                <option value="Technical Call" <?php echo (($action == 'edit' && $data['category'] == 'Technical Call') || (isset($_POST['category']) && $_POST['category'] == 'Technical Call')) ? 'selected' : ''; ?>>Technical Call</option>
                                <option value="Fire Call" <?php echo (($action == 'edit' && $data['category'] == 'Fire Call') || (isset($_POST['category']) && $_POST['category'] == 'Fire Call')) ? 'selected' : ''; ?>>Fire Call</option>
                                <option value="Operational Standby" <?php echo (($action == 'edit' && $data['category'] == 'Operational Standby') || (isset($_POST['category']) && $_POST['category'] == 'Operational Standby')) ? 'selected' : ''; ?>>Operational Standby</option>
                                <option value="Spillage" <?php echo (($action == 'edit' && $data['category'] == 'Spillage') || (isset($_POST['category']) && $_POST['category'] == 'Spillage')) ? 'selected' : ''; ?>>Spillage</option>
                            </select>
                        </div>
                        <div>
                            <label for="display_order" class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                            <input type="number" id="display_order" name="display_order" min="0"
                                   value="<?php echo ($action == 'edit' ? $data['display_order'] : (isset($_POST['display_order']) ? $_POST['display_order'] : '0')); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                        </div>
                    </div>

                    <div>
                        <label for="sub_category" class="block text-sm font-medium text-gray-700 mb-1">Sub Category <span class="text-red-500">*</span></label>
                        <input type="text" id="sub_category" name="sub_category" required
                               value="<?php echo ($action == 'edit' ? htmlspecialchars($data['sub_category']) : (isset($_POST['sub_category']) ? htmlspecialchars($_POST['sub_category']) : '')); ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                        <p class="text-sm text-gray-500 mt-1">Contoh: May Day Activity, Snake Catching, Hydrant Leakage, dll</p>
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location <span class="text-red-500">*</span></label>
                        <input type="text" id="location" name="location" required
                               value="<?php echo ($action == 'edit' ? htmlspecialchars($data['location']) : (isset($_POST['location']) ? htmlspecialchars($_POST['location']) : '')); ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                        <p class="text-sm text-gray-500 mt-1">Lokasi kejadian</p>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                        <textarea id="description" name="description" rows="4" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500"><?php echo ($action == 'edit' ? htmlspecialchars($data['description']) : (isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '')); ?></textarea>
                        <p class="text-sm text-gray-500 mt-1">Deskripsi detail kejadian</p>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="is_active" name="is_active" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500" <?php echo (($action == 'edit' && $data['is_active']) || ($action == 'create' && (!isset($_POST['is_active']) || $_POST['is_active'])) ) ? 'checked' : ''; ?>>
                        <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">Aktif</label>
                        <p class="text-sm text-gray-500 ml-4">Centang untuk menampilkan data ini</p>
                    </div>

                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <a href="index.php" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                            <i class="fas fa-save mr-2"></i><?php echo ($action == 'create' ? 'Simpan' : 'Simpan Perubahan'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
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
    </script>
</body>
</html>
