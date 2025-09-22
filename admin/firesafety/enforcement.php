<?php
require_once 'template_header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enforcement - Fire Safety</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .modal-transition {
            transition: all 0.3s ease-in-out;
        }
        .modal-content {
            transform: scale(0.95);
            opacity: 0;
            transition: all 0.2s ease-in-out;
        }
        .modal-content.show {
            transform: scale(1);
            opacity: 1;
        }
    </style>
</head>
<body class="bg-gray-100">
<?php
$error = '';
$success = '';
$data = null;

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
    $May = isset($_POST['May']) ? (int)$_Post['May'] : 0;
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
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-gavel text-red-600 mr-3"></i>
                Enforcement
            </h1>
            <p class="text-gray-600 mt-2">Manage fire safety enforcement records</p>
        </div>
        <button onclick="openModal('add')" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 hover:shadow-lg flex items-center">
            <i class="fas fa-plus mr-2"></i> Tambah Data
        </button>
    </div>

    <?php if (!empty($error)): ?>
    <div class="mb-4 px-6 py-4 rounded-lg shadow-md border bg-red-50 border-red-200 text-red-800 flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
    <div class="mb-4 px-6 py-4 rounded-lg shadow-md border bg-green-50 border-green-200 text-green-800 flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        <?php echo htmlspecialchars($success); ?>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                        <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-left">Category</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-left">Year</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-left">Order</th>
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
                        <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Total</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wider text-center">Actions</th>
                    </tr>
                </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($enforcement)): ?>
                    <tr>
                        <td colspan="17" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>Tidak ada data enforcement.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($enforcement as $row): ?>
                    <?php $total = $row['Jan'] + $row['Feb'] + $row['Mar'] + $row['Apr'] + $row['May'] + $row['Jun'] + $row['Jul'] + $row['Aug'] + $row['Sep'] + $row['Oct'] + $row['Nov'] + $row['Dec']; ?>
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($row['category']); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo $row['year']; ?></td>
                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo $row['display_order']; ?></td>
                        <td class="px-2 py-3 text-sm text-gray-900 text-center"><?php echo $row['Jan']; ?></td>
                        <td class="px-2 py-3 text-sm text-gray-900 text-center"><?php echo $row['Feb']; ?></td>
                        <td class="px-2 py-3 text-sm text-gray-900 text-center"><?php echo $row['Mar']; ?></td>
                        <td class="px-2 py-3 text-sm text-gray-900 text-center"><?php echo $row['Apr']; ?></td>
                        <td class="px-2 py-3 text-sm text-gray-900 text-center"><?php echo $row['May']; ?></td>
                        <td class="px-2 py-3 text-sm text-gray-900 text-center"><?php echo $row['Jun']; ?></td>
                        <td class="px-2 py-3 text-sm text-gray-900 text-center"><?php echo $row['Jul']; ?></td>
                        <td class="px-2 py-3 text-sm text-gray-900 text-center"><?php echo $row['Aug']; ?></td>
                        <td class="px-2 py-3 text-sm text-gray-900 text-center"><?php echo $row['Sep']; ?></td>
                        <td class="px-2 py-3 text-sm text-gray-900 text-center"><?php echo $row['Oct']; ?></td>
                        <td class="px-2 py-3 text-sm text-gray-900 text-center"><?php echo $row['Nov']; ?></td>
                        <td class="px-2 py-3 text-sm text-gray-900 text-center"><?php echo $row['Dec']; ?></td>
                        <td class="px-2 py-3 text-sm font-bold text-gray-900 text-center"><?php echo $total; ?></td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <button onclick="openModal('edit', <?php echo $row['id']; ?>)" 
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 transform hover:scale-105">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </button>
                                <button onclick="openDeleteModal(<?php echo $row['id']; ?>)"
                                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 transform hover:scale-105">
                                    <i class="fas fa-trash mr-1"></i> Delete
                                </button>
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
<div id="modalForm" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black bg-opacity-40" onclick="closeModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4" style="pointer-events:none;">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl p-8 relative modal-content" id="modalContent" style="pointer-events:auto;">
            <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 transition-colors duration-200">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h2 id="modalTitle" class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-plus text-red-600 mr-2" id="modalIcon"></i>
                <span id="modalTitleText">Tambah</span> Enforcement
            </h2>
            <form id="formEnforcement" method="POST" class="space-y-6">
            <input type="hidden" name="action" id="modalAction" value="create">
            <input type="hidden" name="id" id="modalId" value="">
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label for="modalCategory" class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                    <input type="text" id="modalCategory" name="category" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>
                <div>
                    <label for="modalYear" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <input type="number" id="modalYear" name="year" min="2020" max="2030" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>
            </div>
            
            <div>
                <label for="modalOrder" class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                <input type="number" id="modalOrder" name="display_order" min="0" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Monthly Values</h3>
                <div class="grid grid-cols-6 gap-3">
                    <?php $months = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'); foreach($months as $m): ?>
                    <div>
                        <label for="modal<?php echo $m; ?>" class="block text-xs font-medium text-gray-600 mb-1"><?php echo $m; ?></label>
                        <input type="number" id="modal<?php echo $m; ?>" name="<?php echo $m; ?>" min="0" 
                               class="w-full px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" id="modalActive" name="is_active" 
                       class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500" checked>
                <label for="modalActive" class="ml-2 text-sm text-gray-700">Aktif</label>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeModal()" 
                        class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Delete -->
<div id="modalDelete" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black bg-opacity-40" onclick="closeModalDelete()"></div>
    <div class="flex items-center justify-center min-h-screen p-4" style="pointer-events:none;">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-8 relative modal-content" id="deleteModalContent" style="pointer-events:auto;">
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
                    <button type="button" onclick="closeModalDelete()" 
                            class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                        Batal
                    </button>
                    <button type="submit" id="deleteSubmitBtn"
                            class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 flex items-center">
                        <i class="fas fa-trash mr-2"></i> Delete
                    </button>
                </div>
            </form>
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

function openModal(type, id) {
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

    // Reset all month values to 0
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    months.forEach(function(month) {
        document.getElementById('modal' + month).value = 0;
    });

    // Configure modal based on action
    if (type === 'edit' && id) {
        titleText.textContent = 'Edit';
        modalIcon.className = 'fas fa-edit text-red-600 mr-2';
        const data = <?php echo json_encode($enforcement); ?>;
        const record = data.find(item => item.id == id);
        if (record) {
            document.getElementById('modalId').value = record.id;
            document.getElementById('modalCategory').value = record.category;
            document.getElementById('modalYear').value = record.year;
            document.getElementById('modalOrder').value = record.display_order;
            months.forEach(function(month) {
                document.getElementById('modal' + month).value = record[month] || 0;
            });
            document.getElementById('modalActive').checked = record.is_active == '1';
        }
    } else {
        titleText.textContent = 'Tambah';
        modalIcon.className = 'fas fa-plus text-red-600 mr-2';
    }

    // Hide delete modal if open
    document.getElementById('modalDelete').classList.add('hidden');
    document.getElementById('deleteModalContent').classList.remove('show');
    // Show modal using Tailwind class
    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        modalContent.classList.add('show');
    });
}

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