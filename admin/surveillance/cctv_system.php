<?php
$page_title = 'CCTV System';
require_once 'template_header.php'; // Pastikan file ini ada atau sesuaikan path-nya

// --- Proses CRUD ---
$message = '';
$message_type = '';

// Fungsi sanitize (asumsi Anda sudah punya, jika tidak, tambahkan ini)
if (!function_exists('sanitize')) {
    function sanitize($data) {
        // Implementasi sederhana, sesuaikan dengan kebutuhan
        return htmlspecialchars(strip_tags(trim($data)));
    }
}

// Create/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    // Ambil dan sanitasi semua data dari form
    $category = sanitize($_POST['category']);
    $description = sanitize($_POST['description']);
    $operational = sanitize($_POST['operational']);
    $non_operational = sanitize($_POST['non_operational']);
    $readiness_percentage = sanitize($_POST['readiness_percentage']);
    $notes = sanitize($_POST['notes']);

    if ($_POST['action'] == 'add') {
        $stmt = $mysqli->prepare("INSERT INTO surveillance_cctv_system (category, description, operational, non_operational, readiness_percentage, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssss', $category, $description, $operational, $non_operational, $readiness_percentage, $notes);
        if ($stmt->execute()) {
            $_SESSION['notif'] = "Data berhasil ditambahkan!";
        } else {
            $_SESSION['error'] = "Gagal menambahkan data: " . $stmt->error;
        }
        header('Location: cctv_system.php');
        exit();
    } elseif ($_POST['action'] == 'edit') {
        $id = sanitize($_POST['edit_id']);
        $stmt = $mysqli->prepare("UPDATE surveillance_cctv_system SET category = ?, description = ?, operational = ?, non_operational = ?, readiness_percentage = ?, notes = ? WHERE id = ?");
        $stmt->bind_param('ssssssi', $category, $description, $operational, $non_operational, $readiness_percentage, $notes, $id);
        if ($stmt->execute()) {
            $_SESSION['notif'] = "Data berhasil diperbarui!";
        } else {
            $_SESSION['error'] = "Gagal memperbarui data: " . $stmt->error;
        }
        header('Location: cctv_system.php');
        exit();
    }
}

// Delete
if (isset($_GET['delete'])) {
    $id = sanitize($_GET['delete']);
    $stmt = $mysqli->prepare("DELETE FROM surveillance_cctv_system WHERE id = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $_SESSION['notif'] = "Data berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus data: " . $stmt->error;
    }
    header('Location: cctv_system.php');
    exit();
}

// Ambil data untuk form edit
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

// Ambil semua data dari database, diurutkan berdasarkan 'category'
$data = array();
$result = $mysqli->query("SELECT * FROM surveillance_cctv_system ORDER BY category ASC, id ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}
?>

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

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <?php echo $edit_data ? 'Edit Data' : 'Tambah Data Baru'; ?>
    </h3>
    
    <form method="POST" action="cctv_system.php" class="space-y-4">
        <input type="hidden" name="action" value="<?php echo $edit_data ? 'edit' : 'add'; ?>">
        <?php if ($edit_data): ?>
            <input type="hidden" name="edit_id" value="<?php echo $edit_data['id']; ?>">
        <?php endif; ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <input type="text" name="category" value="<?php echo $edit_data ? htmlspecialchars($edit_data['category']) : ''; ?>" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                       placeholder="Contoh: Deployed CCTV Cameras Readiness" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <input type="text" name="description" value="<?php echo $edit_data ? htmlspecialchars($edit_data['description']) : ''; ?>" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                       placeholder="Contoh: CCTV Camera (IP Type)" required>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Operational</label>
                <input type="text" name="operational" value="<?php echo $edit_data ? htmlspecialchars($edit_data['operational']) : ''; ?>" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Contoh: 152">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Non-Operational</label>
                <input type="text" name="non_operational" value="<?php echo $edit_data ? htmlspecialchars($edit_data['non_operational']) : ''; ?>" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Contoh: 00">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Readiness Percentage (%)</label>
                <input type="text" name="readiness_percentage" value="<?php echo $edit_data ? htmlspecialchars($edit_data['readiness_percentage']) : ''; ?>" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Contoh: 100%">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
            <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                      placeholder="Tambahkan catatan jika perlu"><?php echo $edit_data ? htmlspecialchars($edit_data['notes']) : ''; ?></textarea>
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

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800">Data CCTV System</h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operational</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Non-Op</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Readiness</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                <?php if (empty($data)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">Tidak ada data</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data as $index => $row): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $index + 1; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['category']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['description']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['operational']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['non_operational']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['readiness_percentage']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['notes']); ?></td>
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