<?php
// Perbaikan: Mengganti session_status() dengan cara lama
if (!isset($_SESSION)) {
    session_start();
}
require_once '../../config/database.php';
// Pastikan fungsi checkAdminLogin() dan sanitize() ada di database.php
// Jika tidak, tambahkan di sini

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $category = sanitize($_POST['category']);
    $display_order = (int)$_POST['display_order'];
    
    // Handle file upload
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
                $photo_path = 'uploads/security/gallery/' . $file_name;
                $photo_alt = sanitize($_POST['photo_alt']);
                
                try {
                    $stmt = $pdo->prepare("INSERT INTO security_gallery (title, description, photo_path, photo_alt, category, display_order, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute(array($title, $description, $photo_path, $photo_alt, $category, $display_order, $_SESSION['admin_id']));
                    
                    header("Location: security_management.php?success=added");
                    exit();
                } catch (PDOException $e) {
                    $error = 'Gagal menyimpan data: ' . $e->getMessage();
                    // Delete uploaded file if database insert fails
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
            } else {
                $error = 'Gagal mengupload foto.';
            }
        } else {
            $error = 'Format file tidak didukung atau ukuran file terlalu besar (maksimal 5MB).';
        }
    } else {
        $error = 'Pilih foto untuk diupload.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Foto Galeri - OHSS Dashboard</title>
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
                <h1 class="text-2xl font-bold">Tambah Foto Galeri</h1>
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
                            <!-- Title -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Judul Foto <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="title" required 
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
                                    <option value="patrol">Patroli</option>
                                    <option value="inspection">Inspeksi</option>
                                    <option value="monitoring">Monitoring</option>
                                    <option value="coordination">Koordinasi</option>
                                    <option value="training">Pelatihan</option>
                                    <option value="emergery">Darurat</option>
                                    <option value="other">Lainnya</option>
                                </select>
                            </div>

                            <!-- Display Order -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Urutan Tampilan
                                </label>
                                <input type="number" name="display_order" min="0"
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
                                          placeholder="Deskripsi singkat tentang foto ini"></textarea>
                            </div>

                            <!-- Photo Upload -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Foto <span class="text-red-500">*</span>
                                </label>
                                <input type="file" name="photo" accept="image/*" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-blue">
                                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Maksimal 5MB.</p>
                            </div>

                            <!-- Photo Alt Text -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Teks Alternatif Foto
                                </label>
                                <input type="text" name="photo_alt"
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
                                <i class="fas fa-save mr-2"></i>Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>