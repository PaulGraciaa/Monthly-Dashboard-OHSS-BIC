<?php
require_once 'template_header.php';
$error = '';
$success = '';
$edit_data = null;
// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = isset($_POST['category']) ? mysqli_real_escape_string($conn, $_POST['category']) : '';
    $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $form_action = isset($_POST['action']) ? $_POST['action'] : 'add';
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $months = array('jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec');
    $month_values = array();
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
                    <i class="fas fa-bolt text-white text-xl"></i>
                </div>
                <h1 class="text-xl font-bold text-gray-800">Emergency Activation</h1>
            </div>
            <button id="btnAddEmergency" class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded transition duration-200 flex items-center gap-2 shadow-sm">
                <i class="fas fa-plus"></i>
                <span>Tambah Data</span>
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
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full table-fixed">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="w-10 py-3 px-2 text-left text-[11px] font-semibold text-gray-600">No</th>
                            <th class="w-32 py-3 px-2 text-left text-[11px] font-semibold text-gray-600">Category</th>
                            <?php foreach (["jan","feb","mar","apr","may","jun","jul","aug","sep","oct","nov","dec"] as $m): ?>
                            <th class="w-12 py-3 px-2 text-center text-[11px] font-semibold text-gray-600"><?php echo ucfirst($m); ?></th>
                            <?php endforeach; ?>
                            <th class="w-16 py-3 px-2 text-center text-[11px] font-semibold text-gray-600">Grand Total</th>
                            <th class="w-16 py-3 px-2 text-center text-[11px] font-semibold text-gray-600">Order</th>
                            <th class="w-24 py-3 px-2 text-center text-[11px] font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="16" class="px-2 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p>Tidak ada data emergency activation.</p>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($data as $row): ?>
                        <?php $grand_total = 0; foreach (["jan","feb","mar","apr","may","jun","jul","aug","sep","oct","nov","dec"] as $m) { $grand_total += (int)$row[$m.'_value']; } ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-2 px-2 text-[11px] text-gray-700 text-center"><?php echo $row['id']; ?></td>
                            <td class="py-2 px-2 text-[11px] text-gray-700"><?php echo htmlspecialchars($row['category'], ENT_QUOTES); ?></td>
                            <?php foreach (["jan","feb","mar","apr","may","jun","jul","aug","sep","oct","nov","dec"] as $m): ?>
                            <td class="py-2 px-2 text-[11px] text-gray-700 text-center"><?php echo $row[$m.'_value']; ?></td>
                            <?php endforeach; ?>
                            <td class="py-2 px-2 text-[11px] text-gray-700 text-center font-bold"><?php echo $grand_total; ?></td>
                            <td class="py-2 px-2 text-[11px] text-gray-600 text-center"><?php echo $row['display_order']; ?></td>
                            <td class="py-2 px-2 text-center flex justify-center space-x-1">
                                <button class="p-1 text-gray-500 hover:text-red-500 transition-colors"
                                        data-id="<?php echo $row['id']; ?>"
                                        data-category="<?php echo htmlspecialchars($row['category']); ?>"
                                        <?php foreach (["jan","feb","mar","apr","may","jun","jul","aug","sep","oct","nov","dec"] as $m): ?>
                                        data-<?php echo $m; ?>_value="<?php echo $row[$m.'_value']; ?>"
                                        <?php endforeach; ?>
                                        data-display_order="<?php echo $row['display_order']; ?>"
                                        data-is_active="<?php echo $row['is_active']; ?>">
                                    <i class="fas fa-edit text-[11px]"></i>
                                </button>
                                <a href="emergency.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Yakin hapus data ini?')" class="p-1 text-gray-500 hover:text-red-500 transition-colors">
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
        <div id="modalForm" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 overflow-hidden h-full w-full z-50">
            <div class="w-full max-w-xl shadow-xl rounded-lg bg-white mx-4" id="modalContent">
                <form id="formModal" method="POST">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id" id="formId" value="">
                    <div class="flex justify-between items-center border-b border-gray-200 p-4">
                        <div class="flex items-center space-x-3">
                            <div class="bg-red-500 p-2 rounded">
                                <i class="fas fa-plus text-white text-sm" id="modalIcon"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800"><span id="modalAction">Tambah</span> Emergency Activation</h3>
                        </div>
                        <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-500 transition-colors"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-600 text-sm mb-2">Category</label>
                            <input type="text" name="category" id="modal_category" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" required>
                        </div>
                        <?php foreach (["jan","feb","mar","apr","may","jun","jul","aug","sep","oct","nov","dec"] as $m): ?>
                        <div>
                            <label class="block text-gray-600 text-sm mb-2"><?php echo ucfirst($m); ?> Value</label>
                            <input type="number" name="<?php echo $m; ?>_value" id="modal_<?php echo $m; ?>_value" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" required>
                        </div>
                        <?php endforeach; ?>
                        <div>
                            <label class="block text-gray-600 text-sm mb-2">Display Order</label>
                            <input type="number" name="display_order" id="modal_display_order" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" value="0">
                        </div>
                    </div>
                    <div class="px-6 flex items-center mt-2">
                        <input type="checkbox" name="is_active" id="modal_is_active" class="w-4 h-4 rounded border-gray-300 text-red-500 focus:ring-red-500" checked>
                        <label class="ml-2 block text-sm text-gray-600" for="modal_is_active">Set sebagai data aktif</label>
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
<script>
        function openModal(type, dataset) {
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
                document.getElementById('modal_category').value = dataset.category || '';
                <?php foreach (["jan","feb","mar","apr","may","jun","jul","aug","sep","oct","nov","dec"] as $m): ?>
                document.getElementById('modal_<?php echo $m; ?>_value').value = dataset['<?php echo $m; ?>_value'] || 0;
                <?php endforeach; ?>
                document.getElementById('modal_display_order').value = dataset.display_order || '';
                document.getElementById('modal_is_active').checked = dataset.is_active == '1' ? true : false;
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
        // Event listener tombol edit
        document.querySelectorAll('button[data-id][data-category]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                openModal('edit', this.dataset);
            });
        });
        // Event listener tombol tambah data
        document.addEventListener('DOMContentLoaded', function() {
            var btnAdd = document.getElementById('btnAddEmergency');
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