<?php
session_start();
require_once '../auth.php';

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

// CRUD logic for fire training
switch ($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $serial_number = (int)$_POST['serial_number'];
            $training_date = $_POST['training_date'];
            $location = mysqli_real_escape_string($conn, $_POST['location']);
            $subject = mysqli_real_escape_string($conn, $_POST['subject']);
            $display_order = (int)$_POST['display_order'];
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            if (empty($location) || empty($subject)) {
                $error = 'Location dan Subject tidak boleh kosong';
            } else {
                $query = "INSERT INTO fire_safety_training (serial_number, training_date, location, subject, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "isssii", $serial_number, $training_date, $location, $subject, $display_order, $is_active);
                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['notif'] = 'Data berhasil ditambahkan';
                    header('Location: training.php');
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
            header('Location: training.php');
            exit();
        }
        $query = "SELECT * FROM fire_safety_training WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        if (!$data) {
            header('Location: training.php');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $serial_number = (int)$_POST['serial_number'];
            $training_date = $_POST['training_date'];
            $location = mysqli_real_escape_string($conn, $_POST['location']);
            $subject = mysqli_real_escape_string($conn, $_POST['subject']);
            $display_order = (int)$_POST['display_order'];
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            if (empty($location) || empty($subject)) {
                $error = 'Location dan Subject tidak boleh kosong';
            } else {
                $query = "UPDATE fire_safety_training SET serial_number = ?, training_date = ?, location = ?, subject = ?, display_order = ?, is_active = ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "isssiii", $serial_number, $training_date, $location, $subject, $display_order, $is_active, $id);
                if (mysqli_stmt_execute($stmt)) {
                    $success = 'Data berhasil diperbarui';
                    $data['serial_number'] = $serial_number;
                    $data['training_date'] = $training_date;
                    $data['location'] = $location;
                    $data['subject'] = $subject;
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
            header('Location: training.php');
            exit();
        }
        $query = "UPDATE fire_safety_training SET is_active = 0 WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = 'Data berhasil dihapus';
        } else {
            $_SESSION['error_message'] = 'Gagal menghapus data: ' . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
        header('Location: training.php');
        exit();
        break;
}

// Fetch all data for listing
$query = "SELECT * FROM fire_safety_training WHERE is_active = 1 ORDER BY display_order, training_date DESC";
$result = mysqli_query($conn, $query);
$training_data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $training_data[] = $row;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fire Safety Training</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0A4D9E',
                        accent: '#EF4444',
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
<body>

<div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-chalkboard-teacher text-yellow-600 mr-3"></i>
                Fire Safety Training
            </h2>
            <?php if ($action == 'create' || $action == 'edit'): ?>
                <a href="training.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            <?php else: ?>
                <a href="training.php?action=create" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                    <i class="fas fa-plus mr-1"></i> Add New
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="p-6">
        <?php if ($error): ?>
            <div id="errorNotifBox" class="fixed top-8 right-8 z-50 min-w-[260px] max-w-xs bg-white border border-red-400 shadow-2xl rounded-xl flex items-center px-5 py-4 gap-3 notif-animate-in" style="box-shadow:0 8px 32px 0 rgba(239,68,68,0.15);">
                <div class="flex-shrink-0">
                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-red-100">
                        <i class="fas fa-exclamation text-red-600"></i>
                    </span>
                </div>
                <div class="flex-1 text-red-800 font-semibold text-sm">
                    <?= $error ?>
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
                        <i class="fas fa-check text-green-600"></i>
                    </span>
                </div>
                <div class="flex-1 text-green-800 font-semibold text-sm">
                    <?= $success ?>
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
        <?php if ($action == 'create' || $action == 'edit'): ?>
            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="serial_number" class="block text-sm font-medium text-gray-700">Serial Number</label>
                        <input type="number" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" id="serial_number" name="serial_number" value="<?= $action == 'edit' ? $data['serial_number'] : '' ?>" required min="1">
                    </div>
                    <div>
                        <label for="training_date" class="block text-sm font-medium text-gray-700">Training Date</label>
                        <input type="date" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" id="training_date" name="training_date" value="<?= $action == 'edit' ? $data['training_date'] : '' ?>" required>
                    </div>
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                        <input type="text" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" id="location" name="location" value="<?= $action == 'edit' ? htmlspecialchars($data['location']) : '' ?>" required>
                    </div>
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                        <input type="text" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" id="subject" name="subject" value="<?= $action == 'edit' ? htmlspecialchars($data['subject']) : '' ?>" required>
                    </div>
                    <div>
                        <label for="display_order" class="block text-sm font-medium text-gray-700">Display Order</label>
                        <input type="number" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" id="display_order" name="display_order" value="<?= $action == 'edit' ? $data['display_order'] : '0' ?>" min="0">
                    </div>
                    <div class="flex items-center mt-6">
                        <input class="h-4 w-4 text-yellow-500 focus:ring-yellow-500 border-gray-300 rounded" type="checkbox" id="is_active" name="is_active" <?= ($action == 'edit' && $data['is_active']) ? 'checked' : '' ?>>
                        <label class="ml-2 block text-sm text-gray-700" for="is_active">Active</label>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg font-semibold shadow transition-colors duration-200">
                        <i class="fas fa-save mr-1"></i> Save
                    </button>
                    <a href="training.php" class="bg-gray-400 hover:bg-gray-500 text-white px-6 py-2 rounded-lg font-semibold shadow transition-colors duration-200">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                </div>
            </form>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">S/No</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Date</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Location</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Subject</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Display Order</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($training_data)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>No training data available</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($training_data as $item): ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= $item['serial_number'] ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?= date('d-M-y', strtotime($item['training_date'])) ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($item['location']) ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($item['subject']) ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?= $item['display_order'] ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="training.php?action=edit&id=<?= $item['id'] ?>" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm transition-colors duration-200">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </a>
                                            <a href="training.php?action=delete&id=<?= $item['id'] ?>" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition-colors duration-200" onclick="return confirm('Are you sure you want to delete this item?')">
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
        <?php endif; ?>
    </div>
</div>
</body>
</html>
