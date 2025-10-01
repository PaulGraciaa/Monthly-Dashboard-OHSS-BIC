<?php
require_once 'template_header.php';
$error = '';
$success = '';

// Proses tambah data
if (isset($_POST['action']) && $_POST['action'] == 'create') {
    $serial_number = isset($_POST['serial_number']) ? (int)$_POST['serial_number'] : 0;
    $maintenance_date = isset($_POST['maintenance_date']) ? $_POST['maintenance_date'] : '';
    $location = isset($_POST['location']) ? $_POST['location'] : '';
    $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    if (empty($location)) {
        $error = 'Location tidak boleh kosong';
    } else {
        $query = "INSERT INTO fire_equipment_maintenance (serial_number, maintenance_date, location, display_order, is_active) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "issii", $serial_number, $maintenance_date, $location, $display_order, $is_active);
        if (mysqli_stmt_execute($stmt)) {
            $success = 'Data berhasil ditambahkan';
        } else {
            $error = 'Gagal menambahkan data: ' . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

// Proses edit data
if (isset($_POST['action']) && $_POST['action'] == 'edit' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $serial_number = isset($_POST['serial_number']) ? (int)$_POST['serial_number'] : 0;
    $maintenance_date = isset($_POST['maintenance_date']) ? $_POST['maintenance_date'] : '';
    $location = isset($_POST['location']) ? $_POST['location'] : '';
    $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    if (empty($location)) {
        $error = 'Location tidak boleh kosong';
    } else {
        $query = "UPDATE fire_equipment_maintenance SET serial_number=?, maintenance_date=?, location=?, display_order=?, is_active=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "issiii", $serial_number, $maintenance_date, $location, $display_order, $is_active, $id);
        if (mysqli_stmt_execute($stmt)) {
            $success = 'Data berhasil diupdate';
        } else {
            $error = 'Gagal update data: ' . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

// Proses hapus data
if (isset($_POST['action']) && $_POST['action'] == 'delete' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $query = "UPDATE fire_equipment_maintenance SET is_active=0 WHERE id=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        $success = 'Data berhasil dihapus';
    } else {
        $error = 'Gagal hapus data: ' . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

// Ambil data maintenance
$maintenance = array();
$result = mysqli_query($conn, "SELECT * FROM fire_equipment_maintenance WHERE is_active=1 ORDER BY display_order ASC, maintenance_date DESC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $maintenance[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance List - Batamindo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
    <div class="min-h-screen p-6">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center space-x-3">
                    <div class="bg-red-500 p-2 rounded-lg">
                        <i class="fas fa-tools text-white text-xl"></i>
                    </div>
                    <h1 class="text-xl font-bold text-gray-800">Maintenance List</h1>
                </div>
                <button onclick="openModal('add')" class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded transition duration-200 flex items-center gap-2 shadow-sm">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Maintenance</span>
                </button>
            </div>
            <?php if (!empty($error)): ?>
            <div class="mb-4 px-6 py-4 rounded-lg shadow-md border bg-red-50 border-red-200 text-red-800">
                <i class="fas fa-exclamation-circle mr-2"></i> <?php echo htmlspecialchars((string)$error, ENT_QUOTES); ?>
            </div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
            <div class="mb-4 px-6 py-4 rounded-lg shadow-md border bg-green-50 border-green-200 text-green-800">
                <i class="fas fa-check-circle mr-2"></i> <?php echo htmlspecialchars((string)$success, ENT_QUOTES); ?>
            </div>
            <?php endif; ?>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="w-full table-fixed">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="w-20 py-3 px-2 text-left text-[11px] font-semibold text-gray-600">Serial</th>
                                <th class="w-24 py-3 px-2 text-left text-[11px] font-semibold text-gray-600">Maintenance Date</th>
                                <th class="w-32 py-3 px-2 text-left text-[11px] font-semibold text-gray-600">Location</th>
                                <th class="w-12 py-3 px-2 text-center text-[11px] font-semibold text-gray-600">Order</th>
                                <th class="w-24 py-3 px-2 text-center text-[11px] font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($maintenance)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>Tidak ada data maintenance.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($maintenance as $row): ?>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-2 px-2 text-[11px] text-gray-700"><?php echo $row['serial_number']; ?></td>
                                <td class="py-2 px-2 text-[11px] text-gray-700"><?php echo $row['maintenance_date']; ?></td>
                                <td class="py-2 px-2 text-[11px] text-gray-700"><?php echo htmlspecialchars($row['location'], ENT_QUOTES); ?></td>
                                <td class="py-2 px-2 text-[11px] text-gray-600 text-center"><?php echo $row['display_order']; ?></td>
                                <td class="py-2 px-2 text-center flex justify-center space-x-1">
                                    <button onclick="openModal('edit', this.dataset)"
                                            data-id="<?php echo $row['id']; ?>"
                                            data-serial_number="<?php echo $row['serial_number']; ?>"
                                            data-maintenance_date="<?php echo $row['maintenance_date']; ?>"
                                            data-location="<?php echo htmlspecialchars($row['location'], ENT_QUOTES); ?>"
                                            data-display_order="<?php echo $row['display_order']; ?>"
                                            class="p-1 text-gray-500 hover:text-red-500 transition-colors">
                                        <i class="fas fa-edit text-[11px]"></i>
                                    </button>
                                    <button onclick="openDeleteModal(<?php echo $row['id']; ?>)" class="p-1 text-gray-500 hover:text-red-500 transition-colors">
                                        <i class="fas fa-trash text-[11px]"></i>
                                    </button>
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
                    <span id="modalTitleText">Tambah</span> Maintenance
                </h2>
                <form id="formMaintenance" method="POST" class="space-y-4">
                    <input type="hidden" name="action" id="modalAction" value="create">
                    <input type="hidden" name="id" id="modalId" value="">
                    
                    <div>
                        <label for="modalSerial" class="block text-sm font-medium text-gray-700 mb-1">Serial Number</label>
                        <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                               id="modalSerial" name="serial_number" required>
                    </div>
                    
                    <div>
                        <label for="modalDate" class="block text-sm font-medium text-gray-700 mb-1">Maintenance Date</label>
                        <input type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                               id="modalDate" name="maintenance_date" required>
                    </div>
                    
                    <div>
                        <label for="modalLocation" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                        <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                               id="modalLocation" name="location" required>
                    </div>
                    
                    <div>
                        <label for="modalOrder" class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                        <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                               id="modalOrder" name="display_order" min="0" value="0">
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500" 
                               id="modalActive" name="is_active" checked>
                        <label for="modalActive" class="ml-2 text-sm text-gray-700">Aktif</label>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeModal()" 
                                class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Modal Popup for Delete -->
        <div id="modalDelete" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden transition-opacity duration-300">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-8 relative transform transition-all duration-300 scale-95 opacity-0" id="deleteModalContent">
                <button onclick="closeModalDelete()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 transition-colors duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-trash text-red-600 mr-2"></i>
                    Konfirmasi Hapus
                </h2>
                <form id="formDelete" method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="deleteId" value="">
                    <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModalDelete()" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 flex items-center">
                            <i class="fas fa-trash mr-2"></i> Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            // Fungsi untuk modal Add/Edit
            function openModal(action, data = null) {
                const modal = document.getElementById('modalForm');
                const modalContent = document.getElementById('modalContent');
                const titleText = document.getElementById('modalTitleText');
                const actionInput = document.getElementById('modalAction');
                const modalIcon = document.getElementById('modalIcon');

                // Reset form
                document.getElementById('formMaintenance').reset();

                // Configure modal based on action
                if (action === 'edit' && data) {
                    titleText.textContent = 'Edit';
                    actionInput.value = 'edit';
                    modalIcon.className = 'fas fa-edit text-red-600 mr-2';
                    
                    // Fill form with data
                    document.getElementById('modalId').value = data.id;
                    document.getElementById('modalSerial').value = data.serial_number;
                    document.getElementById('modalDate').value = data.maintenance_date;
                    document.getElementById('modalLocation').value = data.location;
                    document.getElementById('modalOrder').value = data.display_order;
                    document.getElementById('modalActive').checked = true;
                } else {
                    titleText.textContent = 'Tambah';
                    actionInput.value = 'create';
                    modalIcon.className = 'fas fa-plus text-red-600 mr-2';
                    document.getElementById('modalActive').checked = true;
                }

                // Show modal with animation
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
            }

            function closeModal() {
                const modal = document.getElementById('modalForm');
                const modalContent = document.getElementById('modalContent');

                // Hide modal with animation
                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }

            // Fungsi untuk modal Delete
            function openDeleteModal(id) {
                const modal = document.getElementById('modalDelete');
                const modalContent = document.getElementById('deleteModalContent');
                const deleteIdInput = document.getElementById('deleteId');

                // Set the ID to be deleted
                deleteIdInput.value = id;

                // Show modal with animation
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
            }

            function closeModalDelete() {
                const modal = document.getElementById('modalDelete');
                const modalContent = document.getElementById('deleteModalContent');

                // Hide modal with animation
                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }
        </script>
    </div>
</body>
</html>