<?php
session_start();
require_once '../auth.php';
if (!isAdminLoggedIn()) {
    header('Location: ../login.php');
    exit();
}
require_once '../../config/database.php';
require_once 'template_header.php';

// Prevent undefined variable warnings

$action = isset($_GET['action']) ? $_GET['action'] : '';
$data = array();
$error = '';
$success = '';
$message = '';
$message_type = '';

// Create/Update

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $serial_number = isset($_POST['serial_number']) ? (int)$_POST['serial_number'] : 0;
    $incident_date = isset($_POST['incident_date']) ? $_POST['incident_date'] : '';
    $category = isset($_POST['category']) ? sanitize($_POST['category']) : '';
    $sub_category = isset($_POST['sub_category']) ? sanitize($_POST['sub_category']) : '';
    $description = isset($_POST['description']) ? sanitize($_POST['description']) : '';
    $location = isset($_POST['location']) ? sanitize($_POST['location']) : '';
    $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $form_action = isset($_POST['action']) ? $_POST['action'] : 'add';

    if ($form_action == 'add') {
        $stmt = mysqli_prepare($conn, "INSERT INTO fire_safety_emergency_details (serial_number, incident_date, category, sub_category, description, location, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "issssssi", $serial_number, $incident_date, $category, $sub_category, $description, $location, $display_order, $is_active);
        if (mysqli_stmt_execute($stmt)) {
            $message = 'Data berhasil ditambahkan!';
            $message_type = 'success';
        } else {
            $message = 'Gagal menambahkan data!';
            $message_type = 'error';
        }
        mysqli_stmt_close($stmt);
    } elseif ($form_action == 'edit' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $stmt = mysqli_prepare($conn, "UPDATE fire_safety_emergency_details SET serial_number = ?, incident_date = ?, category = ?, sub_category = ?, description = ?, location = ?, display_order = ?, is_active = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "issssssii", $serial_number, $incident_date, $category, $sub_category, $description, $location, $display_order, $is_active, $id);
        if (mysqli_stmt_execute($stmt)) {
            $message = 'Data berhasil diperbarui!';
            $message_type = 'success';
        } else {
            $message = 'Gagal memperbarui data!';
            $message_type = 'error';
        }
        mysqli_stmt_close($stmt);
    }
}

// Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM fire_safety_emergency_details WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        $message = 'Data berhasil dihapus!';
        $message_type = 'success';
    } else {
        $message = 'Gagal menghapus data!';
        $message_type = 'error';
    }
    mysqli_stmt_close($stmt);
}

// Edit data
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM fire_safety_emergency_details WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $edit_data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

// Get all data
$data = array();
$result = mysqli_query($conn, "SELECT * FROM fire_safety_emergency_details ORDER BY display_order ASC, id ASC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}
?>
        <div class="container mx-auto px-4 py-8">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 space-y-4 md:space-y-0">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-list text-red-600 mr-3"></i>
                        Emergency Details
                    </h1>
                    <p class="text-gray-600 mt-2">Manage emergency details and incidents</p>
                </div>
                <button onclick="openModal('add')" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 hover:shadow-lg flex items-center">
                    <i class="fas fa-plus mr-2"></i> Tambah Data
                </button>
            </div>

            <?php if ($message): ?>
            <div id="notificationAlert" class="mb-4 px-6 py-4 rounded-lg shadow-md border <?php echo ($message_type == 'success') ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'; ?> transform transition-all duration-300 opacity-0 translate-y-3">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-<?php echo ($message_type == 'success') ? 'check-circle text-green-400' : 'exclamation-circle text-red-400'; ?> text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium <?php echo ($message_type == 'success') ? 'text-green-800' : 'text-red-800'; ?>"><?php echo $message; ?></p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button onclick="this.closest('#notificationAlert').remove()" class="inline-flex rounded-md p-1.5 <?php echo ($message_type == 'success') ? 'text-green-500 hover:bg-green-100' : 'text-red-500 hover:bg-red-100'; ?> transition-colors duration-200">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                // Animate notification on page load
                setTimeout(() => {
                    const notification = document.getElementById('notificationAlert');
                    if (notification) {
                        notification.style.opacity = '1';
                        notification.style.transform = 'translateY(0)';
                    }
                }, 100);

                // Auto hide after 5 seconds
                setTimeout(() => {
                    const notification = document.getElementById('notificationAlert');
                    if (notification) {
                        notification.style.opacity = '0';
                        notification.style.transform = 'translateY(-10px)';
                        setTimeout(() => notification.remove(), 300);
                    }
                }, 5000);
            </script>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                                <th class="px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider text-left">S/No</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider text-left">Date</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider text-left">Category</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider text-left">Sub Category</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider text-left">Location</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider text-left">Description</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider text-left">Display Order</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Actions</th>
                            </tr>
                        </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($data)): ?>
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>Tidak ada data emergency detail.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($data as $item): ?>
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900"><?php echo $item['serial_number']; ?></td>
                                <td class="px-6 py-4 text-sm text-gray-900"><?php echo date('d-M-y', strtotime($item['incident_date'])); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($item['category']); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($item['sub_category']); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($item['location']); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($item['description']); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-900"><?php echo $item['display_order']; ?></td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button onclick="openModal('edit', <?php echo $item['id']; ?>)" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 transform hover:scale-105">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </button>
                                        <a href="details.php?delete=<?php echo $item['id']; ?>" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 transform hover:scale-105" 
                                           onclick="return confirm('Yakin ingin menghapus data ini?')"
                                           onmouseover="this.querySelector('i').classList.remove('fa-trash');this.querySelector('i').classList.add('fa-trash-alt')"
                                           onmouseout="this.querySelector('i').classList.remove('fa-trash-alt');this.querySelector('i').classList.add('fa-trash')">
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

            <!-- Modal Popup for Add/Edit -->
            <div id="modalForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden transition-opacity duration-300">
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl p-8 relative transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
                    <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 transition-colors duration-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                    <h2 id="modalTitle" class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-plus text-red-600 mr-2" id="modalIcon"></i>
                        <span id="modalAction">Tambah</span> Emergency Detail
                    </h2>
                    <form id="formModal" method="POST" class="space-y-6">
                        <input type="hidden" name="action" id="formAction" value="add">
                        <input type="hidden" name="id" id="formId" value="">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="modal_serial_number" class="block text-sm font-medium text-gray-700">Serial Number <span class="text-red-500">*</span></label>
                                <input type="number" id="modal_serial_number" name="serial_number" required min="1" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label for="modal_incident_date" class="block text-sm font-medium text-gray-700">Incident Date <span class="text-red-500">*</span></label>
                                <input type="date" id="modal_incident_date" name="incident_date" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label for="modal_category" class="block text-sm font-medium text-gray-700">Category <span class="text-red-500">*</span></label>
                                <select id="modal_category" name="category" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                    <option value="">Pilih Category</option>
                                    <option value="Fire Incident">Fire Incident</option>
                                    <option value="Non-Rescue">Non-Rescue</option>
                                    <option value="Technical Call">Technical Call</option>
                                    <option value="Fire Call">Fire Call</option>
                                    <option value="Operational Standby">Operational Standby</option>
                                    <option value="Spillage">Spillage</option>
                                </select>
                            </div>
                            <div>
                                <label for="modal_display_order" class="block text-sm font-medium text-gray-700">Display Order</label>
                                <input type="number" id="modal_display_order" name="display_order" min="0" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            </div>
                        </div>
                        <div>
                            <label for="modal_sub_category" class="block text-sm font-medium text-gray-700">Sub Category <span class="text-red-500">*</span></label>
                            <input type="text" id="modal_sub_category" name="sub_category" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            <p class="text-xs text-gray-500 mt-1">Contoh: May Day Activity, Snake Catching, Hydrant Leakage, dll</p>
                        </div>
                        <div>
                            <label for="modal_location" class="block text-sm font-medium text-gray-700">Location <span class="text-red-500">*</span></label>
                            <input type="text" id="modal_location" name="location" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            <p class="text-xs text-gray-500 mt-1">Lokasi kejadian</p>
                        </div>
                        <div>
                            <label for="modal_description" class="block text-sm font-medium text-gray-700">Description <span class="text-red-500">*</span></label>
                            <textarea id="modal_description" name="description" rows="3" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"></textarea>
                            <p class="text-xs text-gray-500 mt-1">Deskripsi detail kejadian</p>
                        </div>
                        <div class="flex items-center mt-2">
                            <input type="checkbox" id="modal_is_active" name="is_active" class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
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
                    var row = document.querySelector('button[onclick*="openModal(\'edit\', ' + id + ')"]').closest('tr');
                    document.getElementById('formId').value = id;
                    document.getElementById('modal_serial_number').value = row.children[0].innerText;
                    document.getElementById('modal_incident_date').value = row.children[1].innerText;
                    document.getElementById('modal_category').value = row.children[2].innerText;
                    document.getElementById('modal_sub_category').value = row.children[3].innerText;
                    document.getElementById('modal_location').value = row.children[4].innerText;
                    document.getElementById('modal_description').value = row.children[5].innerText;
                    document.getElementById('modal_display_order').value = row.children[6].innerText;
                    document.getElementById('modal_is_active').checked = true;
                } else {
                    document.getElementById('formId').value = '';
                    document.getElementById('modal_serial_number').value = '';
                    document.getElementById('modal_incident_date').value = '';
                    document.getElementById('modal_category').value = '';
                    document.getElementById('modal_sub_category').value = '';
                    document.getElementById('modal_location').value = '';
                    document.getElementById('modal_description').value = '';
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

            // Add loading animation for form submission
            document.getElementById('formModal').addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i>Processing...';
            });
            </script>
        </div>
    </div>
</body>
</html>
