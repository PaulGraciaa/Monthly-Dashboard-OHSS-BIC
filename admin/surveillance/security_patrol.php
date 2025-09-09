<?php
$page_title = 'Security Patrol';
require_once 'template_header.php';

// Proses CRUD
$message = '';
$message_type = '';

// Create/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        // Sesuaikan dengan schema: team_name + jan..dec (varchar)
        $team_name = sanitize($_POST['team_name']);
        $jan = sanitize(isset($_POST['jan']) ? $_POST['jan'] : '');
        $feb = sanitize(isset($_POST['feb']) ? $_POST['feb'] : '');
        $mar = sanitize(isset($_POST['mar']) ? $_POST['mar'] : '');
        $apr = sanitize(isset($_POST['apr']) ? $_POST['apr'] : '');
        $may = sanitize(isset($_POST['may']) ? $_POST['may'] : '');
        $jun = sanitize(isset($_POST['jun']) ? $_POST['jun'] : '');
        $jul = sanitize(isset($_POST['jul']) ? $_POST['jul'] : '');
        $aug = sanitize(isset($_POST['aug']) ? $_POST['aug'] : '');
        $sep = sanitize(isset($_POST['sep']) ? $_POST['sep'] : '');
        $oct = sanitize(isset($_POST['oct']) ? $_POST['oct'] : '');
        $nov = sanitize(isset($_POST['nov']) ? $_POST['nov'] : '');
        $dec = sanitize(isset($_POST['dec']) ? $_POST['dec'] : '');

        if ($_POST['action'] == 'add') {
            $sql = "INSERT INTO surveillance_security_patrol (team_name, jan, feb, mar, apr, may, jun, jul, aug, sep, oct, nov, `dec`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            if ($stmt = $mysqli->prepare($sql)) {
                // semua varchar -> strings (13 params)
                if ($stmt->bind_param('sssssssssssss', $team_name, $jan, $feb, $mar, $apr, $may, $jun, $jul, $aug, $sep, $oct, $nov, $dec) && $stmt->execute()) {
                    $_SESSION['notif'] = "Data berhasil ditambahkan!";
                    $stmt->close();
                    header('Location: security_patrol.php');
                    exit();
                } else {
                    $message = "Gagal menambahkan data: " . $stmt->error;
                    $message_type = "error";
                    $stmt->close();
                }
            } else {
                $message = "Gagal menyiapkan query: " . $mysqli->error;
                $message_type = "error";
            }
        } elseif ($_POST['action'] == 'edit') {
            $id = (int) sanitize($_POST['id']);
            $sql = "UPDATE surveillance_security_patrol SET team_name = ?, jan = ?, feb = ?, mar = ?, apr = ?, may = ?, jun = ?, jul = ?, aug = ?, sep = ?, oct = ?, nov = ?, `dec` = ? WHERE id = ?";
            if ($stmt = $mysqli->prepare($sql)) {
                // 13 strings + id
                if ($stmt->bind_param('sssssssssssssi', $team_name, $jan, $feb, $mar, $apr, $may, $jun, $jul, $aug, $sep, $oct, $nov, $dec, $id) && $stmt->execute()) {
                    $_SESSION['notif'] = "Data berhasil diperbarui!";
                    $stmt->close();
                    header('Location: security_patrol.php');
                    exit();
                } else {
                    $message = "Gagal memperbarui data: " . $stmt->error;
                    $message_type = "error";
                    $stmt->close();
                }
            } else {
                $message = "Gagal menyiapkan query: " . $mysqli->error;
                $message_type = "error";
            }
        }
    }
}

// Delete
if (isset($_GET['delete'])) {
    $id = (int) sanitize($_GET['delete']);
    $sql = "DELETE FROM surveillance_security_patrol WHERE id = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        if ($stmt->bind_param('i', $id) && $stmt->execute()) {
            $_SESSION['notif'] = "Data berhasil dihapus!";
            $stmt->close();
            header('Location: security_patrol.php');
            exit();
        } else {
            $message = "Gagal menghapus data: " . $stmt->error;
            $message_type = "error";
            $stmt->close();
        }
    } else {
        $message = "Gagal menyiapkan query: " . $mysqli->error;
        $message_type = "error";
    }
}

// Ambil data untuk edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int) sanitize($_GET['edit']);
    $sql = "SELECT * FROM surveillance_security_patrol WHERE id = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        if ($stmt->bind_param('i', $id) && $stmt->execute()) {
            $result = $stmt->get_result();
            $edit_data = $result->fetch_assoc();
            $stmt->close();
        } else {
            $message = "Gagal mengambil data: " . $stmt->error;
            $message_type = "error";
            $stmt->close();
        }
    } else {
        $message = "Gagal menyiapkan query: " . $mysqli->error;
        $message_type = "error";
    }
}

// Ambil semua data
$data = array();
$result = $mysqli->query("SELECT * FROM surveillance_security_patrol ORDER BY id ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    $message = "Gagal mengambil daftar data: " . $mysqli->error;
    $message_type = "error";
}
?>

<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Security Patrol</h2>
            <p class="text-gray-600">Kelola data Security Patrol</p>
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
            <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
        <?php endif; ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Team Name</label>
                <input type="text" name="team_name" value="<?php echo $edit_data ? htmlspecialchars($edit_data['team_name']) : ''; ?>" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                       placeholder="Contoh: Team A" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jan</label>
                <input type="text" name="jan" value="<?php echo $edit_data ? htmlspecialchars($edit_data['jan']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Feb</label>
                <input type="text" name="feb" value="<?php echo $edit_data ? htmlspecialchars($edit_data['feb']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mar</label>
                <input type="text" name="mar" value="<?php echo $edit_data ? htmlspecialchars($edit_data['mar']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Apr</label>
                <input type="text" name="apr" value="<?php echo $edit_data ? htmlspecialchars($edit_data['apr']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">May</label>
                <input type="text" name="may" value="<?php echo $edit_data ? htmlspecialchars($edit_data['may']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jun</label>
                <input type="text" name="jun" value="<?php echo $edit_data ? htmlspecialchars($edit_data['jun']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jul</label>
                <input type="text" name="jul" value="<?php echo $edit_data ? htmlspecialchars($edit_data['jul']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Aug</label>
                <input type="text" name="aug" value="<?php echo $edit_data ? htmlspecialchars($edit_data['aug']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sep</label>
                <input type="text" name="sep" value="<?php echo $edit_data ? htmlspecialchars($edit_data['sep']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Oct</label>
                <input type="text" name="oct" value="<?php echo $edit_data ? htmlspecialchars($edit_data['oct']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nov</label>
                <input type="text" name="nov" value="<?php echo $edit_data ? htmlspecialchars($edit_data['nov']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Dec</label>
            <input type="text" name="dec" value="<?php echo $edit_data ? htmlspecialchars($edit_data['dec']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
        </div>
        
        <div class="flex space-x-3">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-150">
                <i class="fas <?php echo $edit_data ? 'fa-save' : 'fa-plus'; ?> mr-2"></i>
                <?php echo $edit_data ? 'Update' : 'Simpan'; ?>
            </button>
            
            <?php if ($edit_data): ?>
                <a href="security_patrol.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-150">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Data Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800">Data Security Patrol</h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Team Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Feb</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Apr</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">May</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jun</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jul</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aug</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sep</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Oct</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nov</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dec</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                <?php if (empty($data)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada data</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data as $index => $row): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $index + 1; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['team_name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['jan']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['feb']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['mar']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['apr']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['may']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['jun']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['jul']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['aug']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['sep']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['oct']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['nov']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['dec']); ?></td>
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

