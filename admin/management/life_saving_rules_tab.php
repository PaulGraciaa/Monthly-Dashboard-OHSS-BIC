<?php
// File: admin/management/life_saving_rules_tab.php
// CRUD untuk Life Saving Rules & BASCOM
require_once '../../config/database.php';

// Pastikan koneksi database $pdo tersedia
if (!isset($pdo) || !$pdo) {
    die('Koneksi database gagal. Pastikan file database.php menginisialisasi $pdo.');
}

// --- Handle CRUD ---
$notif = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $judul = $_POST['judul'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    $id = $_POST['id'] ?? '';
    $current_image = $_POST['current_image'] ?? '';
    $gambar = $current_image;
    $target_dir = '../../uploads/life_saving_rules/';
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $file_extension = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $file_name = 'lsr_' . time() . '_' . rand(1000,9999) . '.' . $file_extension;
        $upload_path = $target_dir . $file_name;
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
            $gambar = 'uploads/life_saving_rules/' . $file_name;
        }
    }
    if ($action == 'add') {
        $stmt = $pdo->prepare("INSERT INTO life_saving_rules (judul, deskripsi, gambar) VALUES (?, ?, ?)");
        $stmt->execute([$judul, $deskripsi, $gambar]);
        $_SESSION['notif'] = 'Data berhasil ditambah!';
        header('Location: life_saving_rules_tab.php');
        exit;
    } elseif ($action == 'edit') {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE life_saving_rules SET judul=?, deskripsi=?, gambar=? WHERE id=?");
            $stmt->execute([$judul, $deskripsi, $gambar, $id]);
            $_SESSION['notif'] = 'Data berhasil diubah!';
        }
        header('Location: life_saving_rules_tab.php');
        exit;
    } elseif ($action == 'delete') {
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM life_saving_rules WHERE id=?");
            $stmt->execute([$id]);
            $_SESSION['notif'] = 'Data berhasil dihapus!';
        }
        header('Location: life_saving_rules_tab.php');
        exit;
    }
}
// Hapus
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM life_saving_rules WHERE id=?");
    $stmt->execute([$id]);
    $_SESSION['notif'] = 'Data berhasil dihapus!';
    header('Location: life_saving_rules_tab.php');
    exit;
}
// --- Ambil Data ---
$data = $pdo->query("SELECT * FROM life_saving_rules ORDER BY id DESC");

// Insert BASCOM data jika belum ada
$bascom_judul = 'BASCOM Guidelines';
$bascom_deskripsi = 'Kartu komunikasi untuk memastikan standar keselamatan tertinggi di Kawasan Batamindo';
$bascom_gambar = 'bascom_card.png'; // Pastikan file ini ada di uploads/life_saving_rules/
$cekBascom = $pdo->prepare("SELECT COUNT(*) FROM life_saving_rules WHERE judul = ?");
$cekBascom->execute([$bascom_judul]);
if ($cekBascom->fetchColumn() == 0) {
    $stmt = $pdo->prepare("INSERT INTO life_saving_rules (judul, deskripsi, gambar) VALUES (?, ?, ?)");
    $stmt->execute([$bascom_judul, $bascom_deskripsi, $bascom_gambar]);
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Life Saving Rules & BASCOM Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FF0000',
                        secondary: '#1a1a1a',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 font-sans">
    <!-- Red Header Section -->
    <div class="bg-gradient-to-r from-red-600 to-red-800">
        <header class="text-white py-4">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-4">
                        <img src="../../img/batamindo.png" alt="Batamindo" class="h-12 w-auto bg-white p-1 rounded">
                        <div>
                            <h1 class="text-2xl font-bold text-white">Batamindo Industrial Park</h1>
                            <p class="text-red-200">OHS Security System Management</p>
                        </div>
                    </div>
                    <div class="hidden md:flex items-center space-x-3">
                        <div class="text-right">
                            <p class="text-sm text-white">Welcome, Admin</p>
                            <p class="text-xs text-red-200"><?php echo date('l, d F Y'); ?></p>
                        </div>
                        <a href="../logout.php" class="bg-white hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-150">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>
        <!-- Navigation -->
        <div class="border-t border-red-500/30">
            <div class="max-w-7xl mx-auto px-4 py-2">
                <nav class="flex space-x-4">
                    <a href="../dashboard.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-chart-line mr-1"></i> Dashboard
                    </a>
                    <a href="activities_tab.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-tasks mr-1"></i> Activities
                    </a>
                    <a href="life_saving_rules_tab.php" class="bg-red-700 text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-shield-alt mr-1"></i> Life Saving Rules & BASCOM
                    </a>
                    <a href="kpi_tab.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-chart-bar mr-1"></i> KPI
                    </a>
                    <a href="news_tab.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-newspaper mr-1"></i> News
                    </a>
                    <a href="config_tab.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-cog mr-1"></i> Config
                    </a>
                    <a href="dashboard_stats_tab.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-chart-pie mr-1"></i> Stats
                    </a>
                </nav>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <?php if (!isset($_SESSION)) { session_start(); } ?>
        <?php if (!empty($_SESSION['notif'])): ?>
        <style>
        @keyframes notifSlideIn {
            0% { opacity: 0; transform: translateY(-30px) scale(0.95); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes notifFadeOut {
            to { opacity: 0; transform: translateY(-10px) scale(0.98); }
        }
        .notif-animate-in { animation: notifSlideIn 0.5s cubic-bezier(.4,0,.2,1); }
        .notif-animate-out { animation: notifFadeOut 0.5s cubic-bezier(.4,0,.2,1) forwards; }
        </style>
        <div id="notifBox" class="fixed top-8 right-8 z-50 min-w-[260px] max-w-xs bg-white border border-green-400 shadow-2xl rounded-xl flex items-center px-5 py-4 gap-3 notif-animate-in" style="box-shadow:0 8px 32px 0 rgba(34,197,94,0.15);">
            <div class="flex-shrink-0">
                <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-green-100">
                    <i class="fas fa-check text-green-600 text-xl"></i>
                </span>
            </div>
            <div class="flex-1 text-green-800 font-semibold text-sm">
                <?php echo $_SESSION['notif']; unset($_SESSION['notif']); ?>
            </div>
            <button onclick="closeNotif()" class="ml-2 text-green-400 hover:text-green-700 focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <script>
        function closeNotif() {
            var notif = document.getElementById('notifBox');
            if (notif) {
                notif.classList.remove('notif-animate-in');
                notif.classList.add('notif-animate-out');
                setTimeout(function(){ notif.remove(); }, 500);
            }
        }
        setTimeout(closeNotif, 3000);
        </script>
        <?php endif; ?>

        <!-- Form Tambah/Edit -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-3xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-plus text-red-600 mr-3"></i>
                    Tambah / Edit Life Saving Rules & BASCOM
                </h2>
            </div>
            <form method="post" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="id" id="form-id">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Judul</label>
                        <input type="text" name="judul" id="form-judul" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" required>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea name="deskripsi" id="form-deskripsi" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" required></textarea>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gambar</label>
                        <input type="file" name="gambar" id="form-gambar" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="submit" name="tambah" class="flex items-center gap-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-8 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5">
                        <i class="fas fa-plus"></i>
                        <span class="font-medium">Tambah</span>
                    </button>
                    <button type="submit" name="edit" class="flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white px-8 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5">
                        <i class="fas fa-edit"></i>
                        <span class="font-medium">Edit</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- List Data Card/Grid -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-3xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-list text-red-600 mr-3"></i>
                    List Life Saving Rules & BASCOM
                </h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if ($data->rowCount() == 0): ?>
                <div class="col-span-full text-center py-12">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-clipboard-list text-6xl"></i>
                    </div>
                    <p class="text-gray-500 text-lg font-semibold">Kolom Life Saving Rules & BASCOM tidak tersedia.</p>
                </div>
                <?php else: ?>
                    <?php while ($row = $data->fetch(PDO::FETCH_ASSOC)): ?>
                    <div class="bg-gray-50 rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300">
                        <div class="relative">
                            <?php if ($row['gambar']): ?>
                                <img src="../../uploads/life_saving_rules/<?= htmlspecialchars($row['gambar']) ?>" alt="img" class="w-full h-48 object-cover transform hover:scale-105 transition-transform duration-500">
                            <?php endif; ?>
                            <div class="absolute top-4 right-4 flex space-x-2">
                                <button type="button" onclick="editData(<?= $row['id'] ?>, '<?= addslashes($row['kategori']) ?>', '<?= addslashes($row['judul']) ?>', '<?= addslashes($row['deskripsi']) ?>')"
                                        class="bg-white text-red-600 p-2 rounded-lg shadow-md hover:shadow-lg hover:bg-red-50 transition-all duration-300">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Hapus data?')" class="bg-white text-red-600 p-2 rounded-lg shadow-md hover:shadow-lg hover:bg-red-50 transition-all duration-300">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-xl font-bold text-gray-800 truncate" title="<?= htmlspecialchars($row['judul']) ?>">
                                    <?= htmlspecialchars($row['judul']) ?>
                                </h3>
                            </div>
                            <p class="text-gray-600 mb-4 line-clamp-3">
                                <?= nl2br(htmlspecialchars($row['deskripsi'])) ?>
                            </p>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
        function editData(id, kategori, judul, deskripsi) {
            document.getElementById('form-id').value = id;
            document.getElementById('form-kategori').value = kategori;
            document.getElementById('form-judul').value = judul;
            document.getElementById('form-deskripsi').value = deskripsi;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>
</body>
</html>
