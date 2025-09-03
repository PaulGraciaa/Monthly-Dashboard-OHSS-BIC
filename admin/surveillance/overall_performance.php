<?php
session_start();
require_once '../../config/database.php';

// Cek login admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

// Proses CRUD
$message = '';
$message_type = '';

// Create/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $indicator = sanitize($_POST['indicator']);
        $current_month = sanitize($_POST['current_month']);
        $cumulative = sanitize($_POST['cumulative']);
        
        if ($_POST['action'] == 'add') {
            $stmt = $pdo->prepare("INSERT INTO surveillance_overall_performance (indicator, current_month, cumulative) VALUES (?, ?, ?)");
            if ($stmt->execute([$indicator, $current_month, $cumulative])) {
                $message = "Data berhasil ditambahkan!";
                $message_type = "success";
            } else {
                $message = "Gagal menambahkan data!";
                $message_type = "error";
            }
        } elseif ($_POST['action'] == 'edit') {
            $id = sanitize($_POST['id']);
            $stmt = $pdo->prepare("UPDATE surveillance_overall_performance SET indicator = ?, current_month = ?, cumulative = ? WHERE id = ?");
            if ($stmt->execute([$indicator, $current_month, $cumulative, $id])) {
                $message = "Data berhasil diperbarui!";
                $message_type = "success";
            } else {
                $message = "Gagal memperbarui data!";
                $message_type = "error";
            }
        }
    }
}

// Delete
if (isset($_GET['delete'])) {
    $id = sanitize($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM surveillance_overall_performance WHERE id = ?");
    if ($stmt->execute([$id])) {
        $message = "Data berhasil dihapus!";
        $message_type = "success";
    } else {
        $message = "Gagal menghapus data!";
        $message_type = "error";
    }
}

// Ambil data untuk edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = sanitize($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM surveillance_overall_performance WHERE id = ?");
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch();
}

// Ambil semua data
$stmt = $pdo->query("SELECT * FROM surveillance_overall_performance ORDER BY id ASC");
$data = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Surveillance Overall Performance</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .bg-header-footer-bg { background-color: #e53935; }
        .text-header-footer-bg { color: #e53935; }
        .border-header-footer-bg { border-color: #e53935; }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-header-footer-bg text-white px-6 py-4 shadow-lg">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="../dashboard.php" class="text-white hover:text-gray-200">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <h1 class="text-2xl font-bold">Surveillance Overall Performance</h1>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm">Admin: <?php echo $_SESSION['admin_username'] ?? 'Admin'; ?></span>
                <a href="../logout.php" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">
        <!-- Message -->
        <?php if ($message): ?>
        <div class="mb-6 p-4 rounded-lg <?php echo $message_type == 'success' ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300'; ?>">
            <i class="fas <?php echo $message_type == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> mr-2"></i>
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 text-gray-800">
                <?php echo $edit_data ? 'Edit Data' : 'Tambah Data Baru'; ?>
            </h2>
            
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="<?php echo $edit_data ? 'edit' : 'add'; ?>">
                <?php if ($edit_data): ?>
                <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                <?php endif; ?>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Performance Indicator</label>
                        <input type="text" name="indicator" value="<?php echo $edit_data ? htmlspecialchars($edit_data['indicator']) : ''; ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-header-footer-bg" 
                               placeholder="Contoh: Overall CCTV Operational Readiness Performance" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Month (%)</label>
                        <input type="text" name="current_month" value="<?php echo $edit_data ? htmlspecialchars($edit_data['current_month']) : ''; ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-header-footer-bg" 
                               placeholder="Contoh: 100%" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cumulative (%)</label>
                        <input type="text" name="cumulative" value="<?php echo $edit_data ? htmlspecialchars($edit_data['cumulative']) : ''; ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-header-footer-bg" 
                               placeholder="Contoh: 100%">
                    </div>
                </div>
                
                <div class="flex space-x-4">
                    <button type="submit" class="bg-header-footer-bg hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium">
                        <i class="fas <?php echo $edit_data ? 'fa-save' : 'fa-plus'; ?> mr-2"></i>
                        <?php echo $edit_data ? 'Update' : 'Tambah'; ?>
                    </button>
                    
                    <?php if ($edit_data): ?>
                    <a href="?cancel" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Data Surveillance Overall Performance</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance Indicator</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Month</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cumulative</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($data as $index => $row): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $index + 1; ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($row['indicator']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['current_month']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['cumulative']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="?edit=<?php echo $row['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="?delete=<?php echo $row['id']; ?>" class="text-red-600 hover:text-red-900" 
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </a>
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
        // Auto hide message after 5 seconds
        setTimeout(function() {
            const message = document.querySelector('.mb-6');
            if (message) {
                message.style.display = 'none';
            }
        }, 5000);
    </script>
</body>
</html>
