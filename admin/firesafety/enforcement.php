<?php
require_once 'template_header.php';
$error = '';
$success = '';

// Proses tambah data
if (isset($_POST['action']) && $_POST['action'] == 'create') {
    $category = isset($_POST['category']) ? $_POST['category'] : '';
    $year = isset($_POST['year']) ? (int)$_POST['year'] : date('Y');
    $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
    $Jan = isset($_POST['Jan']) ? (int)$_POST['Jan'] : 0;
    $Feb = isset($_POST['Feb']) ? (int)$_POST['Feb'] : 0;
    $Mar = isset($_POST['Mar']) ? (int)$_POST['Mar'] : 0;
    $Apr = isset($_POST['Apr']) ? (int)$_POST['Apr'] : 0;
    $May = isset($_POST['May']) ? (int)$_POST['May'] : 0;
    $Jun = isset($_POST['Jun']) ? (int)$_POST['Jun'] : 0;
    $Jul = isset($_POST['Jul']) ? (int)$_POST['Jul'] : 0;
    $Aug = isset($_POST['Aug']) ? (int)$_POST['Aug'] : 0;
    $Sep = isset($_POST['Sep']) ? (int)$_POST['Sep'] : 0;
    $Oct = isset($_POST['Oct']) ? (int)$_POST['Oct'] : 0;
    $Nov = isset($_POST['Nov']) ? (int)$_POST['Nov'] : 0;
    $Dec = isset($_POST['Dec']) ? (int)$_POST['Dec'] : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (empty($category)) {
        $error = 'Category tidak boleh kosong';
    } else {
        $query = "INSERT INTO fire_safety_enforcement (category, year, display_order, Jan, Feb, Mar, Apr, May, Jun, Jul, Aug, Sep, Oct, Nov, Dec, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "siiiiiiiiiiiiiii", $category, $year, $display_order, $Jan, $Feb, $Mar, $Apr, $May, $Jun, $Jul, $Aug, $Sep, $Oct, $Nov, $Dec, $is_active);
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
    $category = isset($_POST['category']) ? $_POST['category'] : '';
    $year = isset($_POST['year']) ? (int)$_POST['year'] : date('Y');
    $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
    $Jan = isset($_POST['Jan']) ? (int)$_POST['Jan'] : 0;
    $Feb = isset($_POST['Feb']) ? (int)$_POST['Feb'] : 0;
    $Mar = isset($_POST['Mar']) ? (int)$_POST['Mar'] : 0;
    $Apr = isset($_POST['Apr']) ? (int)$_POST['Apr'] : 0;
    $May = isset($_POST['May']) ? (int)$_POST['May'] : 0;
    $Jun = isset($_POST['Jun']) ? (int)$_POST['Jun'] : 0;
    $Jul = isset($_POST['Jul']) ? (int)$_POST['Jul'] : 0;
    $Aug = isset($_POST['Aug']) ? (int)$_POST['Aug'] : 0;
    $Sep = isset($_POST['Sep']) ? (int)$_POST['Sep'] : 0;
    $Oct = isset($_POST['Oct']) ? (int)$_POST['Oct'] : 0;
    $Nov = isset($_POST['Nov']) ? (int)$_POST['Nov'] : 0;
    $Dec = isset($_POST['Dec']) ? (int)$_POST['Dec'] : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (empty($category)) {
        $error = 'Category tidak boleh kosong';
    } else {
        $query = "UPDATE fire_safety_enforcement SET category=?, year=?, display_order=?, Jan=?, Feb=?, Mar=?, Apr=?, May=?, Jun=?, Jul=?, Aug=?, Sep=?, Oct=?, Nov=?, Dec=?, is_active=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "siiiiiiiiiiiiiiii", $category, $year, $display_order, $Jan, $Feb, $Mar, $Apr, $May, $Jun, $Jul, $Aug, $Sep, $Oct, $Nov, $Dec, $is_active, $id);
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
    $query = "UPDATE fire_safety_enforcement SET is_active=0 WHERE id=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        $success = 'Data berhasil dihapus';
    } else {
        $error = 'Gagal hapus data: ' . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

// Ambil data enforcement
$enforcement = array();
$result = mysqli_query($conn, "SELECT * FROM fire_safety_enforcement WHERE is_active=1 ORDER BY display_order ASC, year DESC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $enforcement[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fire Safety Enforcement</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
<div class="min-h-screen p-6">
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-3">
                <div class="bg-red-500 p-2 rounded-lg">
                    <i class="fas fa-gavel text-white text-xl"></i>
                </div>
                <h1 class="text-xl font-bold text-gray-800">Fire Safety Enforcement</h1>
            </div>
            <button onclick="openModal('add')" class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded transition duration-200 flex items-center gap-2 shadow-sm">
                <i class="fas fa-plus"></i>
                <span>Tambah Data</span>
            </button>
        </div>
        <?php if (!empty($error)): ?>
        <div class="mb-4 px-6 py-4 rounded-lg shadow-md border bg-red-50 border-red-200 text-red-800">
            <i class="fas fa-exclamation-circle mr-2"></i> <?php echo htmlspecialchars($error, ENT_QUOTES); ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
        <div class="mb-4 px-6 py-4 rounded-lg shadow-md border bg-green-50 border-green-200 text-green-800">
            <i class="fas fa-check-circle mr-2"></i> <?php echo htmlspecialchars($success, ENT_QUOTES); ?>
        </div>
        <?php endif; ?>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full table-fixed">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="w-32 py-3 px-2 text-left text-[11px] font-semibold text-gray-600">Category</th>
                            <th class="w-16 py-3 px-2 text-left text-[11px] font-semibold text-gray-600">Year</th>
                            <th class="w-12 py-3 px-2 text-center text-[11px] font-semibold text-gray-600">Order</th>
                            <?php foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $m): ?>
                            <th class="w-8 py-3 px-1 text-center text-[11px] font-semibold text-gray-600"><?php echo $m; ?></th>
                            <?php endforeach; ?>
                            <th class="w-16 py-3 px-2 text-center text-[11px] font-semibold text-gray-600">Total</th>
                            <th class="w-20 py-3 px-2 text-center text-[11px] font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($enforcement)): ?>
                        <tr>
                            <td colspan="17" class="px-2 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p>Tidak ada data enforcement.</p>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($enforcement as $row): ?>
                        <?php $total = $row['Jan'] + $row['Feb'] + $row['Mar'] + $row['Apr'] + $row['May'] + $row['Jun'] + $row['Jul'] + $row['Aug'] + $row['Sep'] + $row['Oct'] + $row['Nov'] + $row['Dec']; ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-2 px-2 text-[11px] text-gray-700"><?php echo htmlspecialchars($row['category'], ENT_QUOTES); ?></td>
                            <td class="py-2 px-2 text-[11px] text-gray-700"><?php echo $row['year']; ?></td>
                            <td class="py-2 px-2 text-[11px] text-gray-600 text-center"><?php echo $row['display_order']; ?></td>
                            <?php foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $m): ?>
                            <td class="py-2 px-1 text-[11px] text-gray-700 text-center"><?php echo $row[$m]; ?></td>
                            <?php endforeach; ?>
                            <td class="py-2 px-2 text-[11px] font-bold text-gray-900 text-center"><?php echo $total; ?></td>
                            <td class="py-2 px-2 text-center flex justify-center space-x-1">
                                <button onclick="openModal('edit', this.dataset)"
                                        data-id="<?php echo $row['id']; ?>"
                                        data-category="<?php echo htmlspecialchars($row['category'], ENT_QUOTES); ?>"
                                        data-year="<?php echo $row['year']; ?>"
                                        data-display_order="<?php echo $row['display_order']; ?>"
                                        <?php foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $m): ?>
                                        data-<?php echo strtolower($m); ?>="<?php echo $row[$m]; ?>"
                                        <?php endforeach; ?>
                                        data-is_active="<?php echo $row['is_active']; ?>"
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
        <!-- Modal Add/Edit -->
        <div id="modalForm" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-hidden h-full w-full z-50">
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[700px] shadow-xl rounded-lg bg-white" id="modalContent">
                <form id="formEnforcement" method="POST">
                    <input type="hidden" name="action" id="modalAction" value="create">
                    <input type="hidden" name="id" id="modalId" value="">
                    <div class="flex justify-between items-center border-b border-gray-200 p-4">
                        <div class="flex items-center space-x-3">
                            <div class="bg-red-500 p-2 rounded">
                                <i class="fas fa-plus text-white text-sm" id="modalIcon"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800"><span id="modalTitleText">Tambah</span> Enforcement</h3>
                        </div>
                        <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-500 transition-colors"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="p-6 grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-600 text-sm mb-2">Category</label>
                            <input type="text" name="category" id="modalCategory" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm mb-2">Year</label>
                            <input type="number" name="year" id="modalYear" min="2020" max="2030" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-gray-600 text-sm mb-2">Display Order</label>
                            <input type="number" name="display_order" id="modalOrder" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" value="0">
                        </div>
                        <div class="col-span-2 bg-gray-50 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">Monthly Values</h3>
                            <div class="grid grid-cols-6 gap-3">
                                <?php foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $m): ?>
                                <div>
                                    <label for="modal<?php echo $m; ?>" class="block text-xs font-medium text-gray-600 mb-1"><?php echo $m; ?></label>
                                    <input type="number" id="modal<?php echo $m; ?>" name="<?php echo $m; ?>" min="0" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm">
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="flex items-center col-span-2 mt-2">
                            <input type="checkbox" id="modalActive" name="is_active" class="w-4 h-4 rounded border-gray-300 text-red-500 focus:ring-red-500" checked>
                            <label class="ml-2 block text-sm text-gray-600" for="modalActive">Set sebagai data aktif</label>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 rounded-b-lg flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"><i class="fas fa-save mr-2"></i><span id="modalBtnText">Simpan</span></button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Modal Delete -->
        <div id="modalDelete" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-hidden h-full w-full z-50">
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[400px] shadow-xl rounded-lg bg-white" id="deleteModalContent">
                <form id="formDelete" method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="deleteId" value="">
                    <div class="flex justify-between items-center border-b border-gray-200 p-4">
                        <div class="flex items-center space-x-3">
                            <div class="bg-red-500 p-2 rounded">
                                <i class="fas fa-trash text-white text-sm"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800">Konfirmasi Hapus</h3>
                        </div>
                        <button type="button" onclick="closeModalDelete()" class="text-gray-400 hover:text-gray-500 transition-colors"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeModalDelete()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">Batal</button>
                            <button type="submit" id="deleteSubmitBtn" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"><i class="fas fa-trash mr-2"></i> Delete</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set initial year in add form to current year
    const currentYear = new Date().getFullYear();
    document.getElementById('modalYear').value = currentYear;

    // Set initial values to 0 for all month inputs
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    months.forEach(function(month) {
        document.getElementById('modal' + month).value = 0;
    });

    // Ensure modals start hidden using Tailwind
    const modalForm = document.getElementById('modalForm');
    const modalDelete = document.getElementById('modalDelete');
    modalForm.classList.add('hidden');
    modalDelete.classList.add('hidden');

    // Prevent form submission if category is empty
    document.getElementById('formEnforcement').addEventListener('submit', function(e) {
        const category = document.getElementById('modalCategory').value.trim();
        if (!category) {
            e.preventDefault();
            alert('Category tidak boleh kosong');
        }
    });

    // Debug delete form submission
    document.getElementById('formDelete').addEventListener('submit', function(e) {
        console.log('Delete form submitted');
    });
});

function openModal(type, btnOrDataset) {
    const modal = document.getElementById('modalForm');
    const modalContent = document.getElementById('modalContent');
    const form = document.getElementById('formEnforcement');
    const titleText = document.getElementById('modalTitleText');
    const modalIcon = document.getElementById('modalIcon');
    // Reset form
    form.reset();
    document.getElementById('modalAction').value = type === 'edit' ? 'edit' : 'create';
    document.getElementById('modalId').value = '';
    document.getElementById('modalYear').value = new Date().getFullYear();
    document.getElementById('modalActive').checked = true;
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    months.forEach(function(month) {
        document.getElementById('modal' + month).value = 0;
    });
    if (type === 'edit' && btnOrDataset) {
        let dataset = btnOrDataset.dataset ? btnOrDataset.dataset : btnOrDataset;
        titleText.textContent = 'Edit';
        modalIcon.className = 'fas fa-edit text-red-600 mr-2';
        document.getElementById('modalId').value = dataset.id || '';
        document.getElementById('modalCategory').value = dataset.category || '';
        document.getElementById('modalYear').value = dataset.year || new Date().getFullYear();
        document.getElementById('modalOrder').value = dataset.display_order || '';
        months.forEach(function(month) {
            document.getElementById('modal' + month).value = dataset[month.toLowerCase()] || 0;
        });
        document.getElementById('modalActive').checked = dataset.is_active == '1';
    } else {
        titleText.textContent = 'Tambah';
        modalIcon.className = 'fas fa-plus text-red-600 mr-2';
    }
    document.getElementById('modalDelete').classList.add('hidden');
    document.getElementById('deleteModalContent').classList.remove('show');
    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        modalContent.classList.add('show');
    });
}
// Event listener tombol edit agar modal edit dapat dataset

document.querySelectorAll('button[data-id][data-category]').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        openModal('edit', this.dataset);
    });
});

function closeModal() {
    const modal = document.getElementById('modalForm');
    const modalContent = document.getElementById('modalContent');
    modalContent.classList.remove('show');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

function openDeleteModal(id) {
    // Hide add/edit modal if open
    document.getElementById('modalForm').classList.add('hidden');
    document.getElementById('modalContent').classList.remove('show');
    
    const modal = document.getElementById('modalDelete');
    const modalContent = document.getElementById('deleteModalContent');
    
    // Set the ID to be deleted
    document.getElementById('deleteId').value = id;
    
    // Show modal
    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        modalContent.classList.add('show');
    });
}

function closeModalDelete() {
    const modal = document.getElementById('modalDelete');
    const modalContent = document.getElementById('deleteModalContent');
    modalContent.classList.remove('show');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

// Close modals when clicking outside
window.addEventListener('click', function(event) {
    const modalForm = document.getElementById('modalForm');
    const modalDelete = document.getElementById('modalDelete');

    if (event.target === modalForm) {
        closeModal();
    }
    if (event.target === modalDelete) {
        closeModalDelete();
    }
});

// Close modals when pressing ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
        closeModalDelete();
    }
});

// Prevent clicks inside modal from propagating
document.getElementById('modalContent').addEventListener('click', function(e) {
    e.stopPropagation();
});
document.getElementById('deleteModalContent').addEventListener('click', function(e) {
    e.stopPropagation();
});

// Add transitions to form elements
document.querySelectorAll('input, select').forEach(function(element) {
    element.classList.add('transition-all', 'duration-200');
});

// Add hover effects to interactive elements
document.querySelectorAll('button, input[type="checkbox"]').forEach(function(element) {
    element.classList.add('transform', 'hover:scale-105', 'transition-transform', 'duration-200');
});
</script>
</body>
</html>
