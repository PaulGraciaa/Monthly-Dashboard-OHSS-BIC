<?php
session_start();
require_once '../auth.php';
require_once '../../config/database.php';
requireAdminLogin();

$error = '';
$success = '';
$edit_data = null;

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $summary_text = isset($_POST['summary_text']) ? mysqli_real_escape_string($conn, $_POST['summary_text']) : '';
    $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $form_action = isset($_POST['action']) ? $_POST['action'] : 'add';
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if (empty($summary_text)) {
        $error = 'Summary text tidak boleh kosong';
    } else {
        if ($form_action == 'add') {
            $stmt = mysqli_prepare($conn, "INSERT INTO fire_safety_performance (summary_text, display_order, is_active) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sii", $summary_text, $display_order, $is_active);
            if (mysqli_stmt_execute($stmt)) {
                $success = 'Data berhasil ditambahkan!';
            } else {
                $error = 'Gagal menambahkan data: ' . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } elseif ($form_action == 'edit' && $id > 0) {
            $stmt = mysqli_prepare($conn, "UPDATE fire_safety_performance SET summary_text = ?, display_order = ?, is_active = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "siii", $summary_text, $display_order, $is_active, $id);
            if (mysqli_stmt_execute($stmt)) {
                $success = 'Data berhasil diperbarui!';
            } else {
                $error = 'Gagal memperbarui data: ' . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Handle Edit Modal Data
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM fire_safety_performance WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $edit_data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = mysqli_prepare($conn, "UPDATE fire_safety_performance SET is_active = 0 WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        $success = 'Data berhasil dihapus!';
    } else {
        $error = 'Gagal menghapus data: ' . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

// Get all data
$data = array();
$result = mysqli_query($conn, "SELECT * FROM fire_safety_performance WHERE is_active = 1 ORDER BY display_order ASC, id ASC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}
$page_title = 'Fire Safety Performance';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Batamindo</title>
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
                    <a href="index.php" class="bg-red-700 text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-fire-extinguisher mr-1"></i> Fire Safety
                    </a>
                    <a href="performance.php" class="bg-red-800 text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-chart-line mr-1"></i> Performance
                    </a>
                    <a href="emergency.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
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
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-trophy text-red-600 mr-3"></i>
                    Fire Safety Performance
                </h1>
                <p class="text-gray-600 mt-2">Manage Fire Safety Performance summaries.</p>
            </div>
            <button onclick="openModal('add')" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 hover:shadow-lg flex items-center">
                <i class="fas fa-plus mr-2"></i> Tambah Data
            </button>
        </div>

        <?php if ($error): ?>
        <div class="mb-4 px-6 py-4 rounded-lg shadow-md border bg-red-50 border-red-200 text-red-800">
            <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $error; ?>
        </div>
        <?php endif; ?>
        <?php if ($success): ?>
        <div class="mb-4 px-6 py-4 rounded-lg shadow-md border bg-green-50 border-green-200 text-green-800">
            <i class="fas fa-check-circle mr-2"></i> <?php echo $success; ?>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                            <th class="px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider text-left">No</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider text-left">Summary</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider text-left">Display Order</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($data)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>Tidak ada data performance.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($data as $i => $row): ?>
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900"><?php echo $i + 1; ?></td>
                                <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($row['summary_text']); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-900"><?php echo $row['display_order']; ?></td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button onclick="openModal('edit', <?php echo $row['id']; ?>)" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 transform hover:scale-105">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </button>
                                        <a href="performance.php?delete=<?php echo $row['id']; ?>" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 transform hover:scale-105" onclick="return confirm('Yakin ingin menghapus data ini?')">
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

        <!-- Modal Popup for Add/Edit -->
        <div id="modalForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden transition-opacity duration-300">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-xl p-8 relative transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
                <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 transition-colors duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
                <h2 id="modalTitle" class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-plus text-red-600 mr-2" id="modalIcon"></i>
                    <span id="modalAction">Tambah</span> Performance
                </h2>
                <form id="formModal" method="POST" class="space-y-6">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id" id="formId" value="">
                    <div>
                        <label for="modal_summary_text" class="block text-sm font-medium text-gray-700">Summary Text <span class="text-red-500">*</span></label>
                        <textarea id="modal_summary_text" name="summary_text" rows="4" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"></textarea>
                    </div>
                    <div>
                        <label for="modal_display_order" class="block text-sm font-medium text-gray-700">Display Order</label>
                        <input type="number" id="modal_display_order" name="display_order" min="0" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    </div>
                    <div class="flex items-center mt-2">
                        <input type="checkbox" id="modal_is_active" name="is_active" class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500" checked>
                        <label for="modal_is_active" class="ml-2 text-sm text-gray-700">Aktif</label>
                        <span class="text-xs text-gray-500 ml-4">Centang untuk menampilkan data ini</span>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold shadow transition-colors duration-200">
                            <i class="fas fa-save mr-1"></i> <span id="modalBtnText">Simpan</span>
                        </button>
                        <button type="button" onclick="closeModal()" class="bg-gray-400 hover:bg-gray-500 text-white px-6 py-2 rounded-lg font-semibold shadow transition-colors duration-200">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <script>
        function openModal(type, id) {
            const modal = document.getElementById('modalForm');
            const modalContent = document.getElementById('modalContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.style.opacity = '1';
                modalContent.style.opacity = '1';
                modalContent.style.transform = 'scale(1)';
            }, 10);
            document.getElementById('modalAction').innerText = type === 'edit' ? 'Edit' : 'Tambah';
            document.getElementById('modalIcon').className = type === 'edit' ? 'fas fa-edit text-yellow-500 mr-2' : 'fas fa-plus text-red-600 mr-2';
            document.getElementById('modalBtnText').innerText = type === 'edit' ? 'Simpan Perubahan' : 'Simpan';
            document.getElementById('formAction').value = type === 'edit' ? 'edit' : 'add';
            if (type === 'edit' && id) {
                <?php if ($edit_data): ?>
                document.getElementById('formId').value = <?php echo json_encode($edit_data['id']); ?>;
                document.getElementById('modal_summary_text').value = <?php echo json_encode($edit_data['summary_text']); ?>;
                document.getElementById('modal_display_order').value = <?php echo json_encode($edit_data['display_order']); ?>;
                document.getElementById('modal_is_active').checked = <?php echo ($edit_data['is_active'] ? 'true' : 'false'); ?>;
                <?php endif; ?>
            } else {
                document.getElementById('formId').value = '';
                document.getElementById('modal_summary_text').value = '';
                document.getElementById('modal_display_order').value = '';
                document.getElementById('modal_is_active').checked = true;
            }
        }
        function closeModal() {
            const modal = document.getElementById('modalForm');
            const modalContent = document.getElementById('modalContent');
            modalContent.style.opacity = '0';
            modalContent.style.transform = 'scale(0.95)';
            modal.style.opacity = '0';
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
        </script>
    </div>
</body>
</html>
