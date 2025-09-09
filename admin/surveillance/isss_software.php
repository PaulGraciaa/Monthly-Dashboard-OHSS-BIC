<?php
$page_title = 'ISSS Software';
require_once 'template_header.php';

// Inisialisasi variabel
$message = '';
$message_type = '';
$edit_data = null;
$data = array();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action == 'add') {
        $metric_name = sanitize($_POST['metric_name']);
        $jan = sanitize($_POST['jan']);
        $feb = sanitize($_POST['feb']);
        $mar = sanitize($_POST['mar']);
        $apr = sanitize($_POST['apr']);
        $may = sanitize($_POST['may']);
        $jun = sanitize($_POST['jun']);
        $jul = sanitize($_POST['jul']);
        $aug = sanitize($_POST['aug']);
        $sep = sanitize($_POST['sep']);
        $oct = sanitize($_POST['oct']);
        $nov = sanitize($_POST['nov']);
        $dec = sanitize($_POST['dec']);
        
        try {
            // Cek apakah tabel exists sebelum insert
            $stmt = $pdo->query("SHOW TABLES LIKE 'surveillance_isss_software'");
            if ($stmt->rowCount() == 0) {
                // Buat tabel jika tidak ada - menggunakan backticks untuk reserved keyword
                $sql = "CREATE TABLE IF NOT EXISTS `surveillance_isss_software` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `metric_name` varchar(255) NOT NULL,
                    `jan` varchar(50) DEFAULT NULL,
                    `feb` varchar(50) DEFAULT NULL,
                    `mar` varchar(50) DEFAULT NULL,
                    `apr` varchar(50) DEFAULT NULL,
                    `may` varchar(50) DEFAULT NULL,
                    `jun` varchar(50) DEFAULT NULL,
                    `jul` varchar(50) DEFAULT NULL,
                    `aug` varchar(50) DEFAULT NULL,
                    `sep` varchar(50) DEFAULT NULL,
                    `oct` varchar(50) DEFAULT NULL,
                    `nov` varchar(50) DEFAULT NULL,
                    `dec` varchar(50) DEFAULT NULL,
                    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
                $pdo->exec($sql);
            }
            
            $stmt = $pdo->prepare("INSERT INTO surveillance_isss_software (metric_name, jan, feb, mar, apr, may, jun, jul, aug, sep, oct, nov, `dec`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$metric_name, $jan, $feb, $mar, $apr, $may, $jun, $jul, $aug, $sep, $oct, $nov, $dec]);
            $message = "Data ISSS Software berhasil ditambahkan!";
            $message_type = "success";
        } catch (PDOException $e) {
            $message = "Error Database: " . $e->getMessage();
            $message_type = "error";
        }
    } elseif ($action == 'edit') {
        $id = $_POST['id'];
        $metric_name = sanitize($_POST['metric_name']);
        $jan = sanitize($_POST['jan']);
        $feb = sanitize($_POST['feb']);
        $mar = sanitize($_POST['mar']);
        $apr = sanitize($_POST['apr']);
        $may = sanitize($_POST['may']);
        $jun = sanitize($_POST['jun']);
        $jul = sanitize($_POST['jul']);
        $aug = sanitize($_POST['aug']);
        $sep = sanitize($_POST['sep']);
        $oct = sanitize($_POST['oct']);
        $nov = sanitize($_POST['nov']);
        $dec = sanitize($_POST['dec']);
        
        try {
            $stmt = $pdo->prepare("UPDATE surveillance_isss_software SET metric_name = ?, jan = ?, feb = ?, mar = ?, apr = ?, may = ?, jun = ?, jul = ?, aug = ?, sep = ?, oct = ?, nov = ?, `dec` = ? WHERE id = ?");
            $stmt->execute([$metric_name, $jan, $feb, $mar, $apr, $may, $jun, $jul, $aug, $sep, $oct, $nov, $dec, $id]);
            $message = "Data ISSS Software berhasil diupdate!";
            $message_type = "success";
        } catch (PDOException $e) {
            $message = "Error Database: " . $e->getMessage();
            $message_type = "error";
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM surveillance_isss_software WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Data ISSS Software berhasil dihapus!";
        $message_type = "success";
    } catch (PDOException $e) {
        $message = "Error Database: " . $e->getMessage();
        $message_type = "error";
    }
}

// Handle edit mode
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM surveillance_isss_software WHERE id = ?");
        $stmt->execute([$id]);
        $edit_data = $stmt->fetch();
    } catch (PDOException $e) {
        $message = "Error Database: " . $e->getMessage();
        $message_type = "error";
    }
}

// Handle cancel edit
if (isset($_GET['cancel'])) {
    $edit_data = null;
}

// Fetch all data
try {
    // Cek apakah tabel exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'surveillance_isss_software'");
    if ($stmt->rowCount() == 0) {
        // Tabel tidak ada, buat tabel - menggunakan backticks untuk reserved keyword
        $sql = "CREATE TABLE IF NOT EXISTS `surveillance_isss_software` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `metric_name` varchar(255) NOT NULL,
            `jan` varchar(50) DEFAULT NULL,
            `feb` varchar(50) DEFAULT NULL,
            `mar` varchar(50) DEFAULT NULL,
            `apr` varchar(50) DEFAULT NULL,
            `may` varchar(50) DEFAULT NULL,
            `jun` varchar(50) DEFAULT NULL,
            `jul` varchar(50) DEFAULT NULL,
            `aug` varchar(50) DEFAULT NULL,
            `sep` varchar(50) DEFAULT NULL,
            `oct` varchar(50) DEFAULT NULL,
            `nov` varchar(50) DEFAULT NULL,
            `dec` varchar(50) DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $pdo->exec($sql);
        
        // Insert data awal
        $insertData = array(
            array('Total Number Of Patrol Session Conducted', '335', '299', '283', '306', '327', '311', '', '', '', '', '', ''),
            array('Total Patrol Duration Conducted (Hours)', '732', '687', '673', '749', '769', '833', '', '', '', '', '', ''),
            array('Total QR Checkpoints Scanned', '5693', '5392', '4704', '6102', '5616', '5741', '', '', '', '', '', '')
        );

        $stmt = $pdo->prepare("INSERT INTO surveillance_isss_software (metric_name, jan, feb, mar, apr, may, jun, jul, aug, sep, oct, nov, `dec`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($insertData as $dataRow) {
            $stmt->execute($dataRow);
        }
    }
    
    $stmt = $pdo->query("SELECT * FROM surveillance_isss_software ORDER BY id ASC");
    $data = $stmt->fetchAll();
} catch (PDOException $e) {
    $message = "Error Database: " . $e->getMessage();
    $message_type = "error";
    $data = [];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - ISSS Software Utilization Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'header-footer-bg': '#e53935',
                    }
                }
            }
        }
    </script>
    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">
        <!-- Message Display -->
        <?php if ($message): ?>
        <div class="mb-6 p-4 rounded-lg <?php echo $message_type == 'success' ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300'; ?>">
            <i class="fas <?php echo $message_type == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> mr-2"></i>
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <!-- Form for Add/Edit -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 text-gray-800">
                <?php echo $edit_data ? 'Edit Data ISSS Software' : 'Tambah Data ISSS Software Baru'; ?>
            </h2>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="<?php echo $edit_data ? 'edit' : 'add'; ?>">
                <?php if ($edit_data): ?>
                <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                <?php endif; ?>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Metric Name</label>
                    <input type="text" name="metric_name" value="<?php echo $edit_data ? htmlspecialchars($edit_data['metric_name']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-header-footer-bg" placeholder="Contoh: Total Number Of Patrol Session Conducted" required>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jan</label>
                        <input type="text" name="jan" value="<?php echo $edit_data ? htmlspecialchars($edit_data['jan']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-header-footer-bg" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Feb</label>
                        <input type="text" name="feb" value="<?php echo $edit_data ? htmlspecialchars($edit_data['feb']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-header-footer-bg" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mar</label>
                        <input type="text" name="mar" value="<?php echo $edit_data ? htmlspecialchars($edit_data['mar']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-header-footer-bg" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Apr</label>
                        <input type="text" name="apr" value="<?php echo $edit_data ? htmlspecialchars($edit_data['apr']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-header-footer-bg" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">May</label>
                        <input type="text" name="may" value="<?php echo $edit_data ? htmlspecialchars($edit_data['may']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-header-footer-bg" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jun</label>
                        <input type="text" name="jun" value="<?php echo $edit_data ? htmlspecialchars($edit_data['jun']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-header-footer-bg" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jul</label>
                        <input type="text" name="jul" value="<?php echo $edit_data ? htmlspecialchars($edit_data['jul']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-header-footer-bg" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Aug</label>
                        <input type="text" name="aug" value="<?php echo $edit_data ? htmlspecialchars($edit_data['aug']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-header-footer-bg" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sep</label>
                        <input type="text" name="sep" value="<?php echo $edit_data ? htmlspecialchars($edit_data['sep']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-header-footer-bg" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Oct</label>
                        <input type="text" name="oct" value="<?php echo $edit_data ? htmlspecialchars($edit_data['oct']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-header-footer-bg" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nov</label>
                        <input type="text" name="nov" value="<?php echo $edit_data ? htmlspecialchars($edit_data['nov']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-header-footer-bg" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dec</label>
                        <input type="text" name="dec" value="<?php echo $edit_data ? htmlspecialchars($edit_data['dec']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-header-footer-bg" placeholder="0">
                    </div>
                </div>
                
                <div class="flex space-x-4">
                    <button type="submit" class="bg-header-footer-bg hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium">
                        <i class="fas <?php echo $edit_data ? 'fa-save' : 'fa-plus'; ?> mr-2"></i>
                        <?php echo $edit_data ? 'Update' : 'Tambah'; ?>
                    </button>
                    <?php if ($edit_data): ?>
                    <a href="?cancel" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Data ISSS Software Utilization</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metric Name</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jan</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Feb</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Mar</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Apr</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">May</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jun</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jul</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aug</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sep</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Oct</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nov</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Dec</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="15" class="px-6 py-4 text-center text-gray-500">Tidak ada data</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($data as $index => $row): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $index + 1; ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($row['metric_name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center"><?php echo htmlspecialchars($row['jan']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center"><?php echo htmlspecialchars($row['feb']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center"><?php echo htmlspecialchars($row['mar']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center"><?php echo htmlspecialchars($row['apr']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center"><?php echo htmlspecialchars($row['may']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center"><?php echo htmlspecialchars($row['jun']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center"><?php echo htmlspecialchars($row['jul']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center"><?php echo htmlspecialchars($row['aug']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center"><?php echo htmlspecialchars($row['sep']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center"><?php echo htmlspecialchars($row['oct']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center"><?php echo htmlspecialchars($row['nov']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center"><?php echo htmlspecialchars($row['dec']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="?edit=<?php echo $row['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="?delete=<?php echo $row['id']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Auto hide message after 5 seconds
        setTimeout(function() {
            const message = document.querySelector('.mb-6');
            if (message) {
                message.style.display = 'none';
            }
        }, 5000);
    </script>

<?php require_once 'template_footer.php'; ?>