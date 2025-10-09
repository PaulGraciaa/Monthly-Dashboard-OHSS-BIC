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
    <div class="min-h-screen p-6">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center space-x-3">
                    <div class="bg-red-500 p-2 rounded-lg">
                        <i class="fas fa-fire-extinguisher text-white text-xl"></i>
                    </div>
                    <h1 class="text-xl font-bold text-gray-800">Fire Safety Drills</h1>
                </div>
                <button onclick="openModal('add')" class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded transition duration-200 flex items-center gap-2 shadow-sm">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Data</span>
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
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="w-full table-fixed">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="w-10 py-3 px-2 text-left text-[11px] font-semibold text-gray-600">No</th>
                                <th class="w-20 py-3 px-2 text-left text-[11px] font-semibold text-gray-600">Serial</th>
                                <th class="w-24 py-3 px-2 text-left text-[11px] font-semibold text-gray-600">Drill Date</th>
                                <th class="w-32 py-3 px-2 text-left text-[11px] font-semibold text-gray-600">Location</th>
                                <th class="w-32 py-3 px-2 text-left text-[11px] font-semibold text-gray-600">Subject</th>
                                <th class="w-32 py-3 px-2 text-left text-[11px] font-semibold text-gray-600">Type</th>
                                <th class="w-12 py-3 px-2 text-center text-[11px] font-semibold text-gray-600">Order</th>
                                <th class="w-24 py-3 px-2 text-center text-[11px] font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($data)): ?>
                                <tr>
                                    <td colspan="7" class="px-2 py-8 text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-2"></i>
                                        <p>Tidak ada data fire safety drills.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no=1; foreach ($data as $item): ?>
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-2 px-2 text-[11px] text-gray-700"><?php echo $no++; ?></td>
                                    <td class="py-2 px-2 text-[11px] text-gray-700"><?php echo $item['serial_number']; ?></td>
                                    <td class="py-2 px-2 text-[11px] text-gray-700"><?php echo date('d-M-y', strtotime($item['drill_date'])); ?></td>
                                    <td class="py-2 px-2 text-[11px] text-gray-700 truncate" title="<?php echo htmlspecialchars($item['location'], ENT_QUOTES); ?>"><?php echo htmlspecialchars($item['location'], ENT_QUOTES); ?></td>
                                    <td class="py-2 px-2 text-[11px] text-gray-700 truncate" title="<?php echo htmlspecialchars($item['subject'], ENT_QUOTES); ?>"><?php echo htmlspecialchars($item['subject'], ENT_QUOTES); ?></td>
                                    <td class="py-2 px-2 text-[11px] text-gray-700 truncate" title="<?php echo htmlspecialchars($item['drill_type'], ENT_QUOTES); ?>"><?php echo htmlspecialchars($item['drill_type'], ENT_QUOTES); ?></td>
                                    <td class="py-2 px-2 text-[11px] text-gray-600 text-center"><?php echo $item['display_order']; ?></td>
                                    <td class="py-2 px-2 text-center flex justify-center space-x-1">
                                        <button onclick="openModal('edit', <?php echo $item['id']; ?>)" class="p-1 text-gray-500 hover:text-red-500 transition-colors">
                                            <i class="fas fa-edit text-[11px]"></i>
                                        </button>
                                        <a href="drills.php?delete=<?php echo $item['id']; ?>" onclick="return confirm('Yakin hapus data ini?')" class="p-1 text-gray-500 hover:text-red-500 transition-colors">
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

            <!-- Modal Form -->
            <!-- Modal Add/Edit -->
            <div id="modalForm" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-hidden h-full w-full z-50">
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] shadow-xl rounded-lg bg-white" id="modalContent">
                    <form id="formModal" method="POST">
                        <input type="hidden" name="action" id="formAction" value="add">
                        <input type="hidden" name="id" id="formId" value="">
                        <div class="flex justify-between items-center border-b border-gray-200 p-4">
                            <div class="flex items-center space-x-3">
                                <div class="bg-red-500 p-2 rounded">
                                    <i class="fas fa-plus text-white text-sm" id="modalIcon"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800"><span id="modalAction">Tambah</span> Data</h3>
                            </div>
                            <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-500 transition-colors"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="p-6">
                            <label class="block text-gray-600 text-sm mb-2">Serial Number</label>
                            <input type="number" name="serial_number" id="modal_serial_number" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" required>
                            <label class="block text-gray-600 text-sm mb-2 mt-4">Drill Date</label>
                            <input type="date" name="drill_date" id="modal_drill_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" required>
                            <label class="block text-gray-600 text-sm mb-2 mt-4">Location</label>
                            <input type="text" name="location" id="modal_location" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" required>
                            <label class="block text-gray-600 text-sm mb-2 mt-4">Subject</label>
                            <input type="text" name="subject" id="modal_subject" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" required>
                            <label class="block text-gray-600 text-sm mb-2 mt-4">Drill Type</label>
                            <select name="drill_type" id="modal_drill_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" required>
                                <option value="">Pilih Type</option>
                                <option value="drill">Drill</option>
                                <option value="training">Training</option>
                            </select>
                            <label class="block text-gray-600 text-sm mb-2 mt-4">Display Order</label>
                            <input type="number" name="display_order" id="modal_display_order" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" value="0">
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
