<?php
require_once 'template_header.php';


// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = isset($_POST['category']) ? mysqli_real_escape_string($conn, $_POST['category']) : '';
    $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $form_action = isset($_POST['action']) ? $_POST['action'] : 'add';
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    // Monthly values
    $months = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
    $month_values = [];
    foreach ($months as $m) {
        $key = $m . '_value';
        $month_values[$key] = isset($_POST[$key]) ? (int)$_POST[$key] : 0;
    }

    if (empty($category)) {
        $error = 'Category tidak boleh kosong';
    } else {
        if ($form_action == 'add') {
            $sql = "INSERT INTO fire_safety_emergency_activation (category, jan_value, feb_value, mar_value, apr_value, may_value, jun_value, jul_value, aug_value, sep_value, oct_value, nov_value, dec_value, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "siiiiiiiiiiiiii", $category,
                $month_values['jan_value'], $month_values['feb_value'], $month_values['mar_value'], $month_values['apr_value'],
                $month_values['may_value'], $month_values['jun_value'], $month_values['jul_value'], $month_values['aug_value'],
                $month_values['sep_value'], $month_values['oct_value'], $month_values['nov_value'], $month_values['dec_value'],
                $display_order, $is_active);
            if (mysqli_stmt_execute($stmt)) {
                $success = 'Data berhasil ditambahkan!';
            } else {
                $error = 'Gagal menambahkan data: ' . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } elseif ($form_action == 'edit' && $id > 0) {
            $sql = "UPDATE fire_safety_emergency_activation SET category = ?, jan_value = ?, feb_value = ?, mar_value = ?, apr_value = ?, may_value = ?, jun_value = ?, jul_value = ?, aug_value = ?, sep_value = ?, oct_value = ?, nov_value = ?, dec_value = ?, display_order = ?, is_active = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "siiiiiiiiiiiiiii", $category,
                $month_values['jan_value'], $month_values['feb_value'], $month_values['mar_value'], $month_values['apr_value'],
                $month_values['may_value'], $month_values['jun_value'], $month_values['jul_value'], $month_values['aug_value'],
                $month_values['sep_value'], $month_values['oct_value'], $month_values['nov_value'], $month_values['dec_value'],
                $display_order, $is_active, $id);
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
    $stmt = mysqli_prepare($conn, "SELECT * FROM fire_safety_emergency_activation WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $edit_data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = mysqli_prepare($conn, "UPDATE fire_safety_emergency_activation SET is_active = 0 WHERE id = ?");
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
$result = mysqli_query($conn, "SELECT * FROM fire_safety_emergency_activation WHERE is_active = 1 ORDER BY display_order ASC, id ASC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}
$page_title = 'Emergency Activation';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Batamindo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-bolt text-red-600 mr-3"></i>
                    Emergency Activation
                </h1>
                <p class="text-gray-600 mt-2">Manage Emergency Activation records.</p>
            </div>
            <button onclick="openModal('add')" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 hover:shadow-lg flex items-center">
                <i class="fas fa-plus mr-2"></i> Tambah Data
            </button>
        </div>

        <?php if (!empty($error)): ?>
        <div class="mb-4 px-6 py-4 rounded-lg shadow-md border bg-red-50 border-red-200 text-red-800">
            <i class="fas fa-exclamation-circle mr-2"></i> <?php echo htmlspecialchars((string)$error); ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
        <div class="mb-4 px-6 py-4 rounded-lg shadow-md border bg-green-50 border-green-200 text-green-800">
            <i class="fas fa-check-circle mr-2"></i> <?php echo htmlspecialchars((string)$success); ?>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                            <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-left">No</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-left">Category</th>
                            <th class="px-2 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Jan</th>
                            <th class="px-2 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Feb</th>
                            <th class="px-2 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Mar</th>
                            <th class="px-2 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Apr</th>
                            <th class="px-2 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">May</th>
                            <th class="px-2 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Jun</th>
                            <th class="px-2 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Jul</th>
                            <th class="px-2 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Aug</th>
                            <th class="px-2 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Sep</th>
                            <th class="px-2 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Oct</th>
                            <th class="px-2 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Nov</th>
                            <th class="px-2 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Dec</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Grand Total</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-left">Display Order</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($data)): ?>
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>Tidak ada data emergency activation.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($data as $i => $row): ?>
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 text-center"><?php echo $i + 1; ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($row['category'] ?? ''); ?></td>
                                <?php foreach (["jan","feb","mar","apr","may","jun","jul","aug","sep","oct","nov","dec"] as $m): ?>
                                <td class="px-2 py-3 text-sm text-gray-900 text-center"><?php echo $row[$m.'_value'] ?? 0; ?></td>
                                <?php endforeach; ?>
                                <td class="px-2 py-3 text-sm text-gray-900 text-center font-bold"><?php echo $row['grand_total'] ?? 0; ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-center"><?php echo htmlspecialchars($row['display_order'] ?? ''); ?></td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button 
                                            onclick="openModal('edit', this.dataset)"
                                            data-id="<?php echo $row['id'] ?? ''; ?>"
                                            data-category="<?php echo htmlspecialchars($row['category'] ?? ''); ?>"
                                            <?php foreach (["jan","feb","mar","apr","may","jun","jul","aug","sep","oct","nov","dec"] as $m): ?>
                                            data-<?php echo $m; ?>_value="<?php echo $row[$m.'_value'] ?? 0; ?>"
                                            <?php endforeach; ?>
                                            data-display_order="<?php echo htmlspecialchars($row['display_order'] ?? ''); ?>"
                                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 transform hover:scale-105">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </button>
                                        <a href="emergency.php?delete=<?php echo $row['id']; ?>" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 transform hover:scale-105" onclick="return confirm('Yakin ingin menghapus data ini?')">
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
                    <span id="modalAction">Tambah</span> Emergency Activation
                </h2>
                <form id="formModal" method="POST" class="space-y-6">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id" id="formId" value="">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="modal_category" class="block text-sm font-medium text-gray-700">Category <span class="text-red-500">*</span></label>
                            <input type="text" id="modal_category" name="category" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                        <?php foreach (["jan","feb","mar","apr","may","jun","jul","aug","sep","oct","nov","dec"] as $m): ?>
                        <div>
                            <label for="modal_<?php echo $m; ?>_value" class="block text-sm font-medium text-gray-700"><?php echo ucfirst($m); ?> Value</label>
                            <input type="number" id="modal_<?php echo $m; ?>_value" name="<?php echo $m; ?>_value" min="0" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                        <?php endforeach; ?>
                        <div>
                            <label for="modal_display_order" class="block text-sm font-medium text-gray-700">Display Order</label>
                            <input type="number" id="modal_display_order" name="display_order" min="0" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        </div>
                    </div>
                    <!-- Description and Location fields removed -->
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
        function openModal(type, data) {
            // If called from Add button, data may be undefined or an event
            if (type === 'add' || !data || typeof data !== 'object') {
                data = {};
            }
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
            if (type === 'edit' && data) {
                document.getElementById('formId').value = data.id || '';
                document.getElementById('modal_category').value = data.category || '';
                <?php foreach (["jan","feb","mar","apr","may","jun","jul","aug","sep","oct","nov","dec"] as $m): ?>
                document.getElementById('modal_<?php echo $m; ?>_value').value = data['<?php echo $m; ?>_value'] || 0;
                <?php endforeach; ?>
                document.getElementById('modal_display_order').value = data.display_order || '';
                document.getElementById('modal_is_active').checked = true;
            } else {
                document.getElementById('formId').value = '';
                document.getElementById('modal_category').value = '';
                <?php foreach (["jan","feb","mar","apr","may","jun","jul","aug","sep","oct","nov","dec"] as $m): ?>
                document.getElementById('modal_<?php echo $m; ?>_value').value = 0;
                <?php endforeach; ?>
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