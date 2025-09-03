<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug: Cek path
$config_path = __DIR__ . '/../../config/database.php';
if (!file_exists($config_path)) {
    die("Error: Database config file tidak ditemukan di: " . $config_path);
}
require_once $config_path;

// Cek login admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit();
}

$message_type = '';
$edit_data = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'add') {
        $team_name = sanitize($_POST['team_name']);
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
            $stmt = $pdo->query("SHOW TABLES LIKE 'surveillance_qr_scanned'");
            if ($stmt->rowCount() == 0) {
                // Buat tabel jika tidak ada
                $sql = "CREATE TABLE IF NOT EXISTS `surveillance_qr_scanned` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `team_name` varchar(255) NOT NULL,
                    `jan` varchar(50) DEFAULT NULL,
                    `feb` varchar(50) DEFAULT NULL,
                    `mar` varchar(50) DEFAULT NULL,
                    `apr` varchar(50) DEFAULT NULL,
                    `may` varchar(50) DEFAULT NULL,
                    `jun` varchar(50) DEFAULT NULL,
                    `jul` varchar(50) DEFAULT NULL,
                    `aug` varchar(50) DEFAULT NULL,
                    `sep` varchar(50) DEFAULT NULL,
<?php
                    `oct` varchar(50) DEFAULT NULL,
                    `nov` varchar(50) DEFAULT NULL,
                    `dec` varchar(50) DEFAULT NULL,
                    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
                $pdo->exec($sql);
            }

            $stmt = $pdo->prepare("INSERT INTO surveillance_qr_scanned (team_name, jan, feb, mar, apr, may, jun, jul, aug, sep, oct, nov, `dec`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$team_name, $jan, $feb, $mar, $apr, $may, $jun, $jul, $aug, $sep, $oct, $nov, $dec]);
            $message = "Data Security Team QR Scanned berhasil ditambahkan!";
            $message_type = "success";
        } catch (PDOException $e) {
            $message = "Error Database: " . $e->getMessage();
            $message_type = "error";
        }
    } elseif ($action == 'edit') {
        $id = $_POST['id'];
        $team_name = sanitize($_POST['team_name']);
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
            $stmt = $pdo->prepare("UPDATE surveillance_qr_scanned SET team_name = ?, jan = ?, feb = ?, mar = ?, apr = ?, may = ?, jun = ?, jul = ?, aug = ?, sep = ?, oct = ?, nov = ?, `dec` = ? WHERE id = ?");
            $stmt->execute([$team_name, $jan, $feb, $mar, $apr, $may, $jun, $jul, $aug, $sep, $oct, $nov, $dec, $id]);
            $message = "Data Security Team QR Scanned berhasil diupdate!";
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
        $stmt = $pdo->prepare("DELETE FROM surveillance_qr_scanned WHERE id = ?");
        $stmt->execute([$id]);
                    $message = "Data Security Team QR Scanned berhasil dihapus!";
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
        $stmt = $pdo->prepare("SELECT * FROM surveillance_qr_scanned WHERE id = ?");
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
            $stmt = $pdo->query("SHOW TABLES LIKE 'surveillance_qr_scanned'");
    if ($stmt->rowCount() == 0) {
        // Tabel tidak ada, buat tabel
                        $sql = "CREATE TABLE IF NOT EXISTS `surveillance_qr_scanned` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `team_name` varchar(255) NOT NULL,
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
        $insertData = [
            ['Team A – Patrol Truck', '69', '21', '44', '62', '52', '72', '', '', '', '', '', ''],
            ['Team A – Patrol Bike', '40', '41', '42', '47', '53', '41', '', '', '', '', '', ''],
            ['Team B – Patrol Truck', '83', '43', '58', '56', '42', '57', '', '', '', '', '', ''],
            ['Team B – Patrol Bike', '0', '0', '16', '36', '35', '50', '', '', '', '', '', ''],
            ['Team C – Patrol Truck', '86', '40', '69', '114', '90', '112', '', '', '', '', '', ''],
            ['Team C – Patrol Bike', '6', '1', '1', '11', '9', '5', '', '', '', '', '', ''],
            ['Team D – Patrol Truck', '62', '21', '74', '79', '94', '96', '', '', '', '', '', ''],
            ['Team D – Patrol Bike', '28', '39', '28', '20', '14', '53', '', '', '', '', '', ''],
            ['Powerhouse', '343', '332', '337', '320', '377', '342', '', '', '', '', '', ''],
            ['Total', '717', '538', '669', '745', '766', '828', '', '', '', '', '', '']
        ];

        $stmt = $pdo->prepare("INSERT INTO surveillance_security_patrol (team_name, jan, feb, mar, apr, may, jun, jul, aug, sep, oct, nov, `dec`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($insertData as $data) {
            $stmt->execute($data);
        }
    }

            $stmt = $pdo->query("SELECT * FROM surveillance_qr_scanned ORDER BY id ASC");
    $data = $stmt->fetchAll();
} catch (PDOException $e) {
    $message = "Error Database: " . $e->getMessage();
    $message_type = "error";
    $data = [];
}

function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Admin - Security Team Performance on QR Scanned Management</title>
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
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-header-footer-bg text-white px-6 py-4 shadow-lg">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="../dashboard.php" class="text-white hover:text-gray-200">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <h1 class="text-2xl font-bold">Security Team Performance on QR Scanned Management</h1>
            </div>
            <div class="flex items-center space-x-4">
                <a href="index.php" class="text-white hover:text-gray-200">
                    <i class="fas fa-home mr-2"></i>Surveillance Index
                </a>
                <a href="../../surveillance.php" target="_blank" class="text-white hover:text-gray-200">
                    <i class="fas fa-eye mr-2"></i>View Page
                </a>
            </div>
        </div>
    </header>

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
                <?php echo $edit_data ? 'Edit Data Security Team QR Scanned' : 'Tambah Data Security Team QR Scanned Baru'; ?>
            </h2>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="<?php echo $edit_data ? 'edit' : 'add'; ?>">
                <?php if ($edit_data): ?>
                <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                <?php endif; ?>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Team Name</label>
                    <input type="text" name="team_name" value="<?php echo $edit_data ? htmlspecialchars($edit_data['team_name']) : ''; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-header-footer-bg" placeholder="Contoh: Team A – Patrol Truck" required>
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
                <h3 class="text-lg font-semibold text-gray-800">Data Security Team Performance on QR Scanned</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Team Name</th>
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
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($row['team_name']); ?></td>
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
</body>
</html>
