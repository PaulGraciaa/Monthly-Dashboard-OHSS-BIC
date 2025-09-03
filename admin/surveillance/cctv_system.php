<?php
$page_title = 'CCTV System';
require_once 'template_header.php';

// Proses CRUD
$message = '';
$message_type = '';

// Create/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $location = sanitize($_POST['location']);
        $camera_type = sanitize($_POST['camera_type']);
        $readiness_percentage = sanitize($_POST['readiness_percentage']);
        $status = sanitize($_POST['status']);
        
        if ($_POST['action'] == 'add') {
            $stmt = $mysqli->prepare("INSERT INTO surveillance_cctv_system (location, camera_type, readiness_percentage, status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('ssss', $location, $camera_type, $readiness_percentage, $status);
            if ($stmt->execute()) {
                $_SESSION['notif'] = "Data berhasil ditambahkan!";
                header('Location: cctv_system.php');
                exit();
            } else {
                $message = "Gagal menambahkan data!";
                $message_type = "error";
            }
        } elseif ($_POST['action'] == 'edit') {
            $id = sanitize($_POST['edit']);
            $stmt = $mysqli->prepare("UPDATE surveillance_cctv_system SET location = ?, camera_type = ?, readiness_percentage = ?, status = ? WHERE id = ?");
            $stmt->bind_param('ssssi', $location, $camera_type, $readiness_percentage, $status, $id);
            if ($stmt->execute()) {
                $_SESSION['notif'] = "Data berhasil diperbarui!";
                header('Location: cctv_system.php');
                exit();
            } else {
                $message = "Gagal memperbarui data!";
                $message_type = "error";
            }
        }
    }
}

// Delete
if (isset($_GET['delete'])) {
    $id = sanitize($_GET['delete']);
    $stmt = $mysqli->prepare("DELETE FROM surveillance_cctv_system WHERE id = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $_SESSION['notif'] = "Data berhasil dihapus!";
        header('Location: cctv_system.php');
        exit();
    } else {
        $message = "Gagal menghapus data!";
        $message_type = "error";
    }
}

// Ambil data untuk edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = sanitize($_GET['edit']);
    $stmt = $mysqli->prepare("SELECT * FROM surveillance_cctv_system WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        $edit_data = $result->fetch_assoc();
    }
}

// Ambil semua data
$data = array();
$result = $mysqli->query("SELECT * FROM surveillance_cctv_system ORDER BY id ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}
?>

<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">CCTV System</h2>
            <p class="text-gray-600">Kelola data CCTV System</p>
        </div>
        <a href="index.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-150">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>
</div>

<!-- Form Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <?php echo $edit_data ? 'Edit Data' : 'Tambah Data Baru'; ?>
    </h3>
    
    <form method="POST" class="space-y-4">
        <input type="hidden" name="action" value="<?php echo $edit_data ? 'edit' : 'add'; ?>">
        <?php if ($edit_data): ?>
            <input type="hidden" name="edit" value="<?php echo $edit_data['id']; ?>">
        <?php endif; ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                <input type="text" name="location" value="<?php echo $edit_data ? htmlspecialchars($edit_data['location']) : ''; ?>" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                       placeholder="Contoh: Commercial Area" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Camera Type</label>
                <input type="text" name="camera_type" value="<?php echo $edit_data ? htmlspecialchars($edit_data['camera_type']) : ''; ?>" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                       placeholder="Contoh: IP Camera" required>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Readiness Percentage (%)</label>
                <input type="number" name="readiness_percentage" min="0" max="100" step="0.01" 
                       value="<?php echo $edit_data ? htmlspecialchars($edit_data['readiness_percentage']) : ''; ?>" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" required>
                    <option value="">Pilih Status</option>
                    <option value="Operational" <?php echo ($edit_data && $edit_data['status'] == 'Operational') ? 'selected' : ''; ?>>Operational</option>
                    <option value="Maintenance" <?php echo ($edit_data && $edit_data['status'] == 'Maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                    <option value="Offline" <?php echo ($edit_data && $edit_data['status'] == 'Offline') ? 'selected' : ''; ?>>Offline</option>
                    <option value="Installation" <?php echo ($edit_data && $edit_data['status'] == 'Installation') ? 'selected' : ''; ?>>Installation</option>
                </select>
            </div>
        </div>
        
        <div class="flex space-x-3">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-150">
                <i class="fas <?php echo $edit_data ? 'fa-save' : 'fa-plus'; ?> mr-2"></i>
                <?php echo $edit_data ? 'Update' : 'Simpan'; ?>
            </button>
            
            <?php if ($edit_data): ?>
                <a href="cctv_system.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-150">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Data Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800">Data CCTV System</h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Camera Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Readiness (%)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                <?php if (empty($data)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data as $index => $row): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $index + 1; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo isset($row['location']) ? htmlspecialchars($row['location']) : '-'; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo isset($row['camera_type']) ? htmlspecialchars($row['camera_type']) : '-'; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['readiness_percentage']); ?>%</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    <?php 
                                    $status = isset($row['status']) ? $row['status'] : '';
                                    switch($status) {
                                        case 'Operational': echo 'bg-green-100 text-green-800'; break;
                                        case 'Maintenance': echo 'bg-yellow-100 text-yellow-800'; break;
                                        case 'Offline': echo 'bg-red-100 text-red-800'; break;
                                        case 'Installation': echo 'bg-blue-100 text-blue-800'; break;
                                        default: echo 'bg-gray-100 text-gray-800';
                                    }
                                    ?>">
                                    <?php echo htmlspecialchars($status); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="?edit=<?php echo $row['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="?delete=<?php echo $row['id']; ?>" class="text-red-600 hover:text-red-900" 
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'template_footer.php'; ?>
