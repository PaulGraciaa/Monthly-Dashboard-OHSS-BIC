<?php
// Perbaikan: Mengganti session_status() dengan cara lama
if (!isset($_SESSION)) {
    session_start();
}
require_once '../config/database.php';

// Definisikan fungsi sanitize jika belum ada
if (!function_exists('sanitize')) {
    function sanitize($data) {
        return htmlspecialchars(strip_tags(trim($data)));
    }
}

// Definisikan fungsi checkAdminLogin jika belum ada
if (!function_exists('checkAdminLogin')) {
    function checkAdminLogin() {
        if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
            header("Location: ../login.php");
            exit();
        }
    }
}

checkAdminLogin();

$error = '';
$success = '';

// Get gallery data
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: security_management.php");
    exit();
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM security_gallery WHERE id = ?");
$stmt->execute(array($id));
$gallery = $stmt->fetch();

if (!$gallery) {
    header("Location: security_management.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $category = sanitize($_POST['category']);
    $display_order = (int)$_POST['display_order'];
    
    // Handle file upload
    $photo_path = $gallery['photo_path'];
    $photo_alt = sanitize($_POST['photo_alt']);
    
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (in_array($_FILES['photo']['type'], $allowed_types) && $_FILES['photo']['size'] <= $max_size) {
            $upload_dir = '../uploads/security/gallery/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $file_name = 'gallery_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $file_path)) {
                // Delete old photo if exists
                if ($gallery['photo_path'] && file_exists('../' . $gallery['photo_path'])) {
                    unlink('../' . $gallery['photo_path']);
                }
                $photo_path = 'uploads/security/gallery/' . $file_name;
            } else {
                $error = 'Gagal mengupload foto.';
            }
        } else {
            $error = 'Format file tidak didukung atau ukuran file terlalu besar (maksimal 5MB).';
        }
    }
    
    if (empty($error)) {
        try {
            $stmt = $pdo->prepare("UPDATE security_gallery SET title = ?, description = ?, photo_path = ?, photo_alt = ?, category = ?, display_order = ? WHERE id = ?");
            $stmt->execute(array($title, $description, $photo_path, $photo_alt, $category, $display_order, $id));
            
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
    <title>Edit Foto Galeri - OHSS Dashboard</title>
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
                <h1 class="text-2xl font-bold">Edit Foto Galeri</h1>
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
                            <?php echo htmlspecialchars($error, ENT_QUOTES); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Title -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Judul Foto <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="title" required 
                                       value="<?php echo htmlspecialchars($gallery['title'], ENT_QUOTES); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-blue"
                                       placeholder="Contoh: Patroli Siang Hari">
                            </div>

                            <!-- Category -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Kategori <span class="text-red-500">*</span>
                                </label>
                                <select name="category" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-blue">
                                    <option value="">Pilih Kategori</option>
                                    <option value="patrol" <?php echo $gallery['category'] == 'patrol' ? 'selected' : ''; ?>>Patroli</option>
                                    <option value="inspection" <?php echo $gallery['category'] == 'inspection' ? 'selected' : ''; ?>>Inspeksi</option>
                                    <option value="monitoring" <?php echo $gallery['category'] == 'monitoring' ? 'selected' : ''; ?>>Monitoring</option>
                                    <option value="coordination" <?php echo $gallery['category'] == 'coordination' ? 'selected' : ''; ?>>Koordinasi</option>
                                    <option value="training" <?php echo $gallery['category'] == 'training' ? 'selected' : ''; ?>>Pelatihan</option>
                                    <option value="emergency" <?php echo $gallery['category'] == 'emergency' ? 'selected' : ''; ?>>Darurat</option>
                                    <option value="other" <?php echo $gallery['category'] == 'other' ? 'selected' : ''; ?>>Lainnya</option>
                                </select>
                            </div>

                            <!-- Display Order -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Urutan Tampilan
                                </label>
                                <input type="number" name="display_order" min="0"
                                       value="<?php echo $gallery['display_order']; ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-blue"
                                       placeholder="Contoh: 1">
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Deskripsi
                                </label>
                                <textarea name="description" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-blue"
                                          placeholder="Deskripsi singkat tentang foto ini"><?php echo htmlspecialchars($gallery['description'], ENT_QUOTES); ?></textarea>
                            </div>

                            <!-- Current Photo -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Foto Saat Ini
                                </label>
                                <div class="flex items-center space-x-4">
                             <img src="../<?php echo htmlspecialchars($gallery['photo_path'], ENT_QUOTES); ?>" 
                                 alt="<?php echo htmlspecialchars(isset($gallery['photo_alt']) ? $gallery['photo_alt'] : $gallery['title'], ENT_QUOTES); ?>" 
                                         class="w-32 h-32 object-cover rounded border">
                                    <div>
                                        <p class="text-sm text-gray-600"><?php echo htmlspecialchars(isset($gallery['photo_alt']) ? $gallery['photo_alt'] : 'Tidak ada deskripsi', ENT_QUOTES); ?></p>
                                        <p class="text-xs text-gray-500 mt-1">Kategori: <?php echo ucfirst($gallery['category']); ?></p>
                                    </div>
                                </div>
                            </div>

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
                                       value="<?php echo htmlspecialchars($gallery['photo_alt'], ENT_QUOTES); ?>"
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