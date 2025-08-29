<?php
require_once '../auth.php';
require_once '../../config/database.php';
requireAdminLogin();

// Handle delete action
if (isset($_GET['delete_personnel']) && is_numeric($_GET['delete_personnel'])) {
    $id = (int)$_GET['delete_personnel'];
    $stmt = $pdo->prepare("DELETE FROM security_personnel WHERE id = ?");
    $stmt->execute(array($id));
    header("Location: security_management.php?success=deleted");
    exit();
}

if (isset($_GET['delete_gallery']) && is_numeric($_GET['delete_gallery'])) {
    $id = (int)$_GET['delete_gallery'];
    $stmt = $pdo->prepare("SELECT photo_path FROM security_gallery WHERE id = ?");
    $stmt->execute(array($id));
    $photo = $stmt->fetch();
    
    if ($photo && file_exists($photo['photo_path'])) {
        unlink($photo['photo_path']);
    }
    
    $stmt = $pdo->prepare("DELETE FROM security_gallery WHERE id = ?");
    $stmt->execute(array($id));
    header("Location: security_management.php?success=gallery_deleted");
    exit();
}

// Get all personnel data
$stmt = $pdo->query("SELECT * FROM security_personnel ORDER BY display_order, id");
$personnel = $stmt->fetchAll();

// Get all gallery data
$stmt = $pdo->query("SELECT * FROM security_gallery ORDER BY display_order, id");
$gallery = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Management - OHSS Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-blue': '#0A4D9E',
                        'header-footer-bg': '#e53935',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                },
            },
        };
    </script>
</head>
<body class="bg-gray-50 font-sans">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-header-footer-bg text-white px-6 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">Security Management</h1>
                <a href="../dashboard.php" class="bg-white text-header-footer-bg px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Dashboard
                </a>
            </div>
        </header>

        <!-- Success Message -->
        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mx-6 mt-4">
                <?php 
                switch($_GET['success']) {
                    case 'added': echo 'Data berhasil ditambahkan!'; break;
                    case 'updated': echo 'Data berhasil diperbarui!'; break;
                    case 'deleted': echo 'Data berhasil dihapus!'; break;
                    case 'gallery_deleted': echo 'Foto galeri berhasil dihapus!'; break;
                }
                ?>
            </div>
        <?php endif; ?>

        <div class="container mx-auto px-6 py-8">
            <!-- Security Personnel Section -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Data Security Personnel</h2>
                    <a href="add_security_personnel.php" class="bg-primary-blue text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-plus mr-2"></i>Tambah Personnel
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left">No</th>
                                <th class="px-4 py-3 text-left">Jabatan</th>
                                <th class="px-4 py-3 text-left">Jumlah</th>
                                <th class="px-4 py-3 text-left">Nama/Keterangan</th>
                                <th class="px-4 py-3 text-left">Foto</th>
                                <th class="px-4 py-3 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($personnel as $index => $item): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3"><?php echo $index + 1; ?></td>
                                <td class="px-4 py-3 font-semibold"><?php echo htmlspecialchars($item['position']); ?></td>
                                <td class="px-4 py-3"><?php echo $item['personnel_count']; ?> personel</td>
                                <td class="px-4 py-3">
                                    <div class="max-w-xs">
                                        <?php echo nl2br(htmlspecialchars($item['personnel_names'])); ?>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if ($item['photo_path']): ?>
                                        <img src="../<?php echo htmlspecialchars($item['photo_path']); ?>" 
                                             alt="<?php echo htmlspecialchars(isset($item['photo_alt']) ? $item['photo_alt'] : 'Security Photo'); ?>" 
                                             class="w-16 h-16 object-cover rounded">
                                    <?php else: ?>
                                        <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex space-x-2">
                                        <a href="edit_security_personnel.php?id=<?php echo $item['id']; ?>" 
                                           class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </a>
                                        <a href="?delete_personnel=<?php echo $item['id']; ?>" 
                                           class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600"
                                           onclick="return confirm('Yakin ingin menghapus data ini?')">
                                            <i class="fas fa-trash mr-1"></i>Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Security Gallery Section -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Galeri Foto Security</h2>
                    <a href="add_security_gallery.php" class="bg-primary-blue text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-plus mr-2"></i>Tambah Foto
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($gallery as $item): ?>
                    <div class="bg-gray-50 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition">
                        <div class="relative">
                            <img src="../<?php echo htmlspecialchars($item['photo_path']); ?>" 
                                 alt="<?php echo htmlspecialchars(isset($item['photo_alt']) ? $item['photo_alt'] : $item['title']); ?>" 
                                 class="w-full h-48 object-cover">
                            <div class="absolute top-2 right-2">
                                <span class="bg-primary-blue text-white px-2 py-1 rounded text-xs">
                                    <?php echo ucfirst($item['category']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($item['title']); ?></h3>
                            <p class="text-gray-600 text-sm mb-3"><?php echo htmlspecialchars($item['description']); ?></p>
                            <div class="flex space-x-2">
                                <a href="edit_security_gallery.php?id=<?php echo $item['id']; ?>" 
                                   class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                <a href="?delete_gallery=<?php echo $item['id']; ?>" 
                                   class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600"
                                   onclick="return confirm('Yakin ingin menghapus foto ini?')">
                                    <i class="fas fa-trash mr-1"></i>Hapus
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>