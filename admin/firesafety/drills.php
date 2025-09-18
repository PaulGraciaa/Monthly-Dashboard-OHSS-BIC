<?php
require_once 'template_header.php';

$message = '';
$message_type = '';
$data = array();
$error = '';
$success = '';
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Handle form submission for create/update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $serial_number = isset($_POST['serial_number']) ? (int)$_POST['serial_number'] : 0;
    $drill_date = isset($_POST['drill_date']) ? $_POST['drill_date'] : '';
    $location = isset($_POST['location']) ? mysqli_real_escape_string($conn, $_POST['location']) : '';
    $subject = isset($_POST['subject']) ? mysqli_real_escape_string($conn, $_POST['subject']) : '';
    $drill_type = isset($_POST['drill_type']) ? $_POST['drill_type'] : '';
    $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $form_action = isset($_POST['action']) ? $_POST['action'] : 'add';

    if (empty($location) || empty($subject)) {
        $error = 'Location dan Subject tidak boleh kosong';
        $message = $error;
        $message_type = 'error';
    } else {
        if ($form_action == 'add') {
            $stmt = mysqli_prepare($conn, "INSERT INTO fire_safety_drills (serial_number, drill_date, location, subject, drill_type, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "issssii", $serial_number, $drill_date, $location, $subject, $drill_type, $display_order, $is_active);
            if (mysqli_stmt_execute($stmt)) {
                $message = 'Data berhasil ditambahkan!';
                $message_type = 'success';
                $success = $message;
            } else {
                $message = 'Gagal menambahkan data: ' . mysqli_error($conn);
                $message_type = 'error';
                $error = $message;
            }
            mysqli_stmt_close($stmt);
        } elseif ($form_action == 'edit' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $stmt = mysqli_prepare($conn, "UPDATE fire_safety_drills SET serial_number = ?, drill_date = ?, location = ?, subject = ?, drill_type = ?, display_order = ?, is_active = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "issssiii", $serial_number, $drill_date, $location, $subject, $drill_type, $display_order, $is_active, $id);
            if (mysqli_stmt_execute($stmt)) {
                $message = 'Data berhasil diperbarui!';
                $message_type = 'success';
                $success = $message;
            } else {
                $message = 'Gagal memperbarui data: ' . mysqli_error($conn);
                $message_type = 'error';
                $error = $message;
            }
            mysqli_stmt_close($stmt);
        }
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0) {
        $stmt = mysqli_prepare($conn, "UPDATE fire_safety_drills SET is_active = 0 WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = 'Data berhasil dihapus';
            $message = $_SESSION['success_message'];
            $message_type = 'success';
            $success = $message;
        } else {
            $_SESSION['error_message'] = 'Gagal menghapus data: ' . mysqli_error($conn);
            $message = $_SESSION['error_message'];
            $message_type = 'error';
            $error = $message;
        }
        mysqli_stmt_close($stmt);
        if (!isset($_GET['ajax'])) {
            header('Location: drills.php');
            exit();
        }
    }
}

$query = "SELECT * FROM fire_safety_drills WHERE is_active = 1 ORDER BY display_order ASC, drill_date DESC";
$result = mysqli_query($conn, $query);
$data = array();
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fire Safety Drills</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
        <div class="container mx-auto px-4 py-8">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 space-y-4 md:space-y-0">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-fire-extinguisher text-red-600 mr-3"></i>
                        Fire Safety Drills
                    </h1>
                    <p class="text-gray-600 mt-2">Manage fire safety drills and training sessions</p>
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
            <?php endif; ?>

            <!-- Table -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                                <th class="px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider text-left">S/No</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider text-left">Date</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider text-left">Location</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider text-left">Subject</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider text-left">Type</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider text-left">Display Order</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if (empty($data)): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-2"></i>
                                        <p>Tidak ada data fire safety drills.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($data as $item): ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900"><?php echo $item['serial_number']; ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?php echo date('d-M-y', strtotime($item['drill_date'])); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($item['location']); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($item['subject']); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($item['drill_type']); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?php echo $item['display_order']; ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <button onclick="openModal('edit', <?php echo $item['id']; ?>)" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 transform hover:scale-105">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </button>
                                            <a href="drills.php?delete=<?php echo $item['id']; ?>" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 transform hover:scale-105" 
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
            </div>

            <!-- Modal Form -->
            <div id="modalForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden transition-opacity duration-300">
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl p-8 relative transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
                    <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 transition-colors duration-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                    <h2 id="modalTitle" class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-plus text-red-600 mr-2" id="modalIcon"></i>
                        <span id="modalAction">Tambah</span> Fire Safety Drill
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
                                <label for="modal_drill_date" class="block text-sm font-medium text-gray-700">Drill Date <span class="text-red-500">*</span></label>
                                <input type="date" id="modal_drill_date" name="drill_date" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label for="modal_drill_type" class="block text-sm font-medium text-gray-700">Drill Type <span class="text-red-500">*</span></label>
                                <select id="modal_drill_type" name="drill_type" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                    <option value="">Pilih Type</option>
                                    <option value="drill">Drill</option>
                                    <option value="training">Training</option>
                                </select>
                            </div>
                            <div>
                                <label for="modal_display_order" class="block text-sm font-medium text-gray-700">Display Order</label>
                                <input type="number" id="modal_display_order" name="display_order" min="0" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            </div>
                        </div>
                        <div>
                            <label for="modal_location" class="block text-sm font-medium text-gray-700">Location <span class="text-red-500">*</span></label>
                            <input type="text" id="modal_location" name="location" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            <p class="text-xs text-gray-500 mt-1">Lokasi drill atau training</p>
                        </div>
                        <div>
                            <label for="modal_subject" class="block text-sm font-medium text-gray-700">Subject <span class="text-red-500">*</span></label>
                            <input type="text" id="modal_subject" name="subject" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            <p class="text-xs text-gray-500 mt-1">Subjek atau topik drill/training</p>
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
                // Animate notification on page load
                setTimeout(() => {
                    const notification = document.getElementById('notificationAlert');
                    if (notification) {
                        notification.style.opacity = '1';
                        notification.style.transform = 'translateY(0)';
                    }
                }, 100);

                // Auto hide notification after 5 seconds
                setTimeout(() => {
                    const notification = document.getElementById('notificationAlert');
                    if (notification) {
                        notification.style.opacity = '0';
                        notification.style.transform = 'translateY(-10px)';
                        setTimeout(() => notification.remove(), 300);
                    }
                }, 5000);

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
                        document.getElementById('modal_serial_number').value = row.cells[0].innerText;
                        document.getElementById('modal_drill_date').value = formatDateForInput(row.cells[1].innerText);
                        document.getElementById('modal_location').value = row.cells[2].innerText;
                        document.getElementById('modal_subject').value = row.cells[3].innerText;
                        document.getElementById('modal_drill_type').value = row.cells[4].innerText.toLowerCase();
                        document.getElementById('modal_display_order').value = row.cells[5].innerText;
                        document.getElementById('modal_is_active').checked = true;
                    } else {
                        document.getElementById('formId').value = '';
                        document.getElementById('modal_serial_number').value = '';
                        document.getElementById('modal_drill_date').value = '';
                        document.getElementById('modal_location').value = '';
                        document.getElementById('modal_subject').value = '';
                        document.getElementById('modal_drill_type').value = '';
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

                function formatDateForInput(dateStr) {
                    // Convert date from dd-MMM-yy to yyyy-mm-dd
                    const date = new Date(dateStr);
                    return date.toISOString().split('T')[0];
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