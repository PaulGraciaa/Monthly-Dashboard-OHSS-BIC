<?php
session_start();
require_once '../../config/database.php';
checkAdminLogin();

$error = '';
$success = '';

// Get personnel data
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: security_management.php");
    exit();
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM security_personnel WHERE id = ?");
$stmt->execute(array($id));
$personnel = $stmt->fetch();

if (!$personnel) {
    header("Location: security_management.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $position = sanitize($_POST['position']);
    $personnel_count = (int)$_POST['personnel_count'];
    $personnel_names = sanitize($_POST['personnel_names']);
    $description = sanitize($_POST['description']);
    $display_order = (int)$_POST['display_order'];
    
    // Handle file upload
    $photo_path = $personnel['photo_path'];
    $photo_alt = sanitize($_POST['photo_alt']);
    
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (in_array($_FILES['photo']['type'], $allowed_types) && $_FILES['photo']['size'] <= $max_size) {
            $upload_dir = '../uploads/security/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $file_name = 'security_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $file_path)) {
                // Delete old photo if exists
                if ($personnel['photo_path'] && file_exists('../' . $personnel['photo_path'])) {
                    unlink('../' . $personnel['photo_path']);
                }
                $photo_path = 'uploads/security/' . $file_name;
            } else {
                $error = 'Gagal mengupload foto.';
            }
        } else {
            $error = 'Format file tidak didukung atau ukuran file terlalu besar (maksimal 5MB).';
        }
    }
    
    if (empty($error)) {
        try {
            $stmt = $pdo->prepare("UPDATE security_personnel SET position = ?, personnel_count = ?, personnel_names = ?, photo_path = ?, photo_alt = ?, description = ?, display_order = ? WHERE id = ?");
            $stmt->execute(array($position, $personnel_count, $personnel_names, $photo_path, $photo_alt, $description, $display_order, $id));
            
            header("Location: security_management.php?success=updated");
            exit();
        } catch (PDOException $e) {
            $error = 'Gagal memperbarui data: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Security Personnel - OHSS Dashboard</title>
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
                <h1 class="text-2xl font-bold">Edit Security Personnel</h1>
                <a href="security_management.php" class="bg-white text-header-footer-bg px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </header>

        <div class="container mx-auto px-6 py-8">
            <div class="max-w-2xl mx-auto">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <?php if ($error): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Position -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jabatan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="position" required 
                                       value="<?php echo htmlspecialchars($personnel['position']); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-blue"
                                       placeholder="Contoh: Executive, Inspector, dll">
                            </div>

                            <!-- Personnel Count -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jumlah Personel <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="personnel_count" required min="0"
                                       value="<?php echo $personnel['personnel_count']; ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-blue"
                                       placeholder="Contoh: 5">
                            </div>

                            <!-- Display Order -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Urutan Tampilan
                                </label>
                                <input type="number" name="display_order" min="0"
                                       value="<?php echo $personnel['display_order']; ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-blue"
                                       placeholder="Contoh: 1">
                            </div>

                            <!-- Personnel Names -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama/Keterangan Personel
                                </label>
                                <textarea name="personnel_names" rows="4"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-blue"
                                          placeholder="Masukkan nama-nama personel (satu nama per baris)"><?php echo htmlspecialchars($personnel['personnel_names']); ?></textarea>
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Deskripsi
                                </label>
                                <textarea name="description" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-blue"
                                          placeholder="Deskripsi singkat tentang jabatan ini"><?php echo htmlspecialchars($personnel['description']); ?></textarea>
                            </div>

                            <!-- Current Photo -->
                            <?php if ($personnel['photo_path']): ?>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Foto Saat Ini
                                </label>
                                <div class="flex items-center space-x-4">
                                    <img src="../<?php echo htmlspecialchars($personnel['photo_path']); ?>" 
                                         alt="<?php echo htmlspecialchars(isset($personnel['photo_alt']) ? $personnel['photo_alt'] : 'Security Photo'); ?>" 
                                         class="w-24 h-24 object-cover rounded border">
                                    <div>
                                        <p class="text-sm text-gray-600"><?php echo htmlspecialchars(isset($personnel['photo_alt']) ? $personnel['photo_alt'] : 'Tidak ada deskripsi'); ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Photo Upload -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Ganti Foto (Opsional)
                                </label>
                                <input type="file" name="photo" accept="image/*"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-blue">
                                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Maksimal 5MB. Biarkan kosong jika tidak ingin mengubah foto.</p>
                            </div>

                            <!-- Photo Alt Text -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Teks Alternatif Foto
                                </label>
                                <input type="text" name="photo_alt"
                                       value="<?php echo htmlspecialchars($personnel['photo_alt']); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-blue"
                                       placeholder="Deskripsi foto untuk aksesibilitas">
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end space-x-4 mt-6">
                            <a href="security_management.php" 
                               class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="bg-primary-blue text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                                <i class="fas fa-save mr-2"></i>Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
[file content end]

[file name]: index.php
[file content begin]
<?php
require_once '../auth.php';
require_once '../../config/database.php';
requireAdminLogin();
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
    <style>
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .tab-btn.active { background-color: #1f2937; }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <div class="min-h-screen">
        <header class="bg-red-600 text-white px-6 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">Security Management</h1>
                <a href="../dashboard.php" class="bg-white text-red-600 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Dashboard
                </a>
            </div>
        </header>
        <div class="container mx-auto px-6 py-8">
            <div class="mb-6 flex gap-2">
                <button class="tab-btn bg-gray-800 text-white px-4 py-2 rounded" onclick="showTab('personnel')">
                    <i class="fas fa-users-cog mr-2"></i>Personnel
                </button>
                <button class="tab-btn bg-gray-800 text-white px-4 py-2 rounded" onclick="showTab('gallery')">
                    <i class="fas fa-images mr-2"></i>Gallery
                </button>
            </div>
            <div id="personnel" class="tab-content active">
                <?php include 'security_management.php'; ?>
            </div>
            <div id="gallery" class="tab-content">
                <?php include 'add_security_gallery.php'; ?>
            </div>
        </div>
    </div>
    <script>
        function showTab(tab) {
            var tabs = document.querySelectorAll('.tab-content');
            for (var i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove('active');
            }
            
            var buttons = document.querySelectorAll('.tab-btn');
            for (var i = 0; i < buttons.length; i++) {
                buttons[i].classList.remove('active');
            }
            
            document.getElementById(tab).classList.add('active');
            var indexMap = { personnel: 0, gallery: 1 };
            var buttons = document.querySelectorAll('.tab-btn');
            var idx = indexMap[tab];
            if (buttons[idx]) buttons[idx].classList.add('active');
        }
        // default
        showTab('personnel');
    </script>
</body>
</html>