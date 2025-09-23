<?php
require_once 'template_header.php';

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
</head>
<body class="bg-gray-100 font-sans">
<div class="min-h-screen p-6">
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-3">
                <div class="bg-red-500 p-2 rounded-lg">
                    <i class="fas fa-trophy text-white text-xl"></i>
                </div>
                <h1 class="text-xl font-bold text-gray-800">Fire Safety Performance</h1>
            </div>
            <button onclick="openModal('add')" class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded transition duration-200 flex items-center gap-2 shadow-sm">
                <i class="fas fa-plus"></i>
                <span>Tambah Data</span>
            </button>
            <script>
            </script>
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
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full table-fixed">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="w-10 py-3 px-2 text-left text-[11px] font-semibold text-gray-600">No</th>
                            <th class="w-64 py-3 px-2 text-left text-[11px] font-semibold text-gray-600">Summary</th>
                            <th class="w-12 py-3 px-2 text-center text-[11px] font-semibold text-gray-600">Order</th>
                            <th class="w-24 py-3 px-2 text-center text-[11px] font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="4" class="px-2 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p>Tidak ada data performance.</p>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($data as $i => $row): ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-2 px-2 text-[11px] text-gray-700"><?php echo $i + 1; ?></td>
                            <td class="py-2 px-2 text-[11px] text-gray-700 truncate" title="<?php echo htmlspecialchars($row['summary_text']); ?>"><?php echo htmlspecialchars($row['summary_text']); ?></td>
                            <td class="py-2 px-2 text-[11px] text-gray-600 text-center"><?php echo $row['display_order']; ?></td>
                            <td class="py-2 px-2 text-center flex justify-center space-x-1">
                                <button onclick="openModal('edit', this.dataset)"
                                        data-id="<?php echo $row['id']; ?>"
                                        data-summary_text="<?php echo htmlspecialchars($row['summary_text']); ?>"
                                        data-display_order="<?php echo $row['display_order']; ?>"
                                        data-is_active="<?php echo $row['is_active']; ?>"
                                        class="p-1 text-gray-500 hover:text-red-500 transition-colors">
                                    <i class="fas fa-edit text-[11px]"></i>
                                </button>
                                <a href="performance.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Yakin hapus data ini?')" class="p-1 text-gray-500 hover:text-red-500 transition-colors">
                                    <i class="fas fa-trash text-[11px]"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Modal Add/Edit -->
        <div id="modalForm" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-hidden h-full w-full z-50">
            <div class="flex items-center justify-center min-h-screen">
                <div class="w-full max-w-xl shadow-xl rounded-lg bg-white mx-4" id="modalContent">
                <form id="formModal" method="POST">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id" id="formId" value="">
                    <div class="flex justify-between items-center border-b border-gray-200 p-4">
                        <div class="flex items-center space-x-3">
                            <div class="bg-red-500 p-2 rounded">
                                <i class="fas fa-plus text-white text-sm" id="modalIcon"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800"><span id="modalAction">Tambah</span> Performance</h3>
                        </div>
                        <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-500 transition-colors"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="p-6">
                        <label class="block text-gray-600 text-sm mb-2">Summary Text</label>
                        <textarea name="summary_text" id="modal_summary_text" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" required></textarea>
                        <label class="block text-gray-600 text-sm mb-2 mt-4">Display Order</label>
                        <input type="number" name="display_order" id="modal_display_order" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" value="0">
                        <div class="mt-4 flex items-center">
                            <input type="checkbox" name="is_active" id="modal_is_active" class="w-4 h-4 rounded border-gray-300 text-red-500 focus:ring-red-500" checked>
                            <label class="ml-2 block text-sm text-gray-600" for="modal_is_active">Set sebagai data aktif</label>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 rounded-b-lg flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"><i class="fas fa-save mr-2"></i><span id="modalBtnText">Simpan</span></button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
        function openModal(type, dataset) {
            // dataset default ke null jika tidak ada
            if (typeof dataset === 'undefined') dataset = null;
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
            if (type === 'edit' && dataset) {
                document.getElementById('formId').value = dataset.id || '';
                document.getElementById('modal_summary_text').value = dataset.summary_text || '';
                document.getElementById('modal_display_order').value = dataset.display_order || '';
                document.getElementById('modal_is_active').checked = dataset.is_active == '1' ? true : false;
            } else {
                document.getElementById('formId').value = '';
                document.getElementById('modal_summary_text').value = '';
                document.getElementById('modal_display_order').value = '';
                document.getElementById('modal_is_active').checked = true;
            }
        }
        // Perbaiki tombol edit agar kirim dataset
        // Selector tombol edit: gunakan [data-id] dan [data-summary_text]
        document.querySelectorAll('button[data-id][data-summary_text]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                openModal('edit', this.dataset);
            });
        });
        // Tambahkan id pada tombol tambah data dan gunakan event listener
        // Tambahkan event listener pada tombol tambah data dengan id
        document.addEventListener('DOMContentLoaded', function() {
            var btnAdd = document.getElementById('btnAddPerformance');
            if (btnAdd) {
                btnAdd.addEventListener('click', function(e) {
                    e.preventDefault();
                    openModal('add');
                });
            }
        });
        function closeModal() {
            const modal = document.getElementById('modalForm');
            const modalContent = document.getElementById('modalContent');
            modalContent.style.opacity = '0';
            modalContent.style.transform = 'scale(0.95)';
            modal.style.opacity = '0';
            setTimeout(() => {
                modal.classList.add('hidden');
                // Reset form agar data edit tidak bocor
                document.getElementById('formModal').reset();
                document.getElementById('formId').value = '';
                document.getElementById('formAction').value = 'add';
                document.getElementById('modalAction').innerText = 'Tambah';
                document.getElementById('modalIcon').className = 'fas fa-plus text-red-600 mr-2';
                document.getElementById('modalBtnText').innerText = 'Simpan';
            }, 300);
        }
        </script>
</body>
</html>
