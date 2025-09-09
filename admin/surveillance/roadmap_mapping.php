<?php
require_once '../../config/database.php';
$page_title = 'Roadmap Mapping';
$message = '';
$edit_data = null;
// CRUD logic moved before any output
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $title = sanitize($_POST['title']);
    $image = isset($_FILES['image']) ? $_FILES['image'] : null;
    $imagePath = '';
    $uploadError = '';
    if ($image && $image['error'] == UPLOAD_ERR_OK) {
        $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
        $filename = 'roadmap_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        $target = '../../uploads/roadmap_mapping/' . $filename;
        if (move_uploaded_file($image['tmp_name'], $target)) {
            $imagePath = 'uploads/roadmap_mapping/' . $filename;
        } else {
            $uploadError = 'Gagal upload gambar. Pastikan folder uploads/roadmap_mapping sudah ada dan permission benar.';
        }
    } elseif ($image && $image['error'] != UPLOAD_ERR_NO_FILE) {
        $uploadError = 'Gagal upload gambar. Error code: ' . $image['error'];
    }
    if ($_POST['action'] == 'add') {
        if ($uploadError) {
            $message = $uploadError;
        } else {
            $stmt = $mysqli->prepare("INSERT INTO surveillance_roadmap_mapping (title, image) VALUES (?, ?)");
            if ($stmt && $stmt->bind_param('ss', $title, $imagePath) && $stmt->execute()) {
                $_SESSION['notif'] = "Data berhasil ditambahkan!";
                header('Location: roadmap_mapping.php');
                exit();
            } else {
                $message = "Gagal menambahkan data!";
            }
        }
    } elseif ($_POST['action'] == 'edit') {
        $id = sanitize($_POST['id']);
        if ($uploadError) {
            $message = $uploadError;
        } else {
            if ($imagePath) {
                $stmt = $mysqli->prepare("UPDATE surveillance_roadmap_mapping SET title = ?, image = ? WHERE id = ?");
                $success = $stmt && $stmt->bind_param('ssi', $title, $imagePath, $id) && $stmt->execute();
            } else {
                $stmt = $mysqli->prepare("UPDATE surveillance_roadmap_mapping SET title = ? WHERE id = ?");
                $success = $stmt && $stmt->bind_param('si', $title, $id) && $stmt->execute();
            }
            if ($success) {
                $_SESSION['notif'] = "Data berhasil diperbarui!";
                header('Location: roadmap_mapping.php');
                exit();
            } else {
                $message = "Gagal memperbarui data!";
            }
        }
    }
}
if (isset($_GET['delete'])) {
    $id = sanitize($_GET['delete']);
    $stmt = $mysqli->prepare("DELETE FROM surveillance_roadmap_mapping WHERE id = ?");
    if ($stmt && $stmt->bind_param('i', $id) && $stmt->execute()) {
        $_SESSION['notif'] = "Data berhasil dihapus!";
        header('Location: roadmap_mapping.php');
        exit();
    } else {
        $message = "Gagal menghapus data!";
    }
}
if (isset($_GET['edit'])) {
    $id = sanitize($_GET['edit']);
    $stmt = $mysqli->prepare("SELECT * FROM surveillance_roadmap_mapping WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_data = $result->fetch_assoc();
}
$data = array();
$result = $mysqli->query("SELECT * FROM surveillance_roadmap_mapping ORDER BY id ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}
require_once 'template_header.php';
?>
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Roadmap Mapping Management</h2>
            <?php if ($message): ?>
                <div class="rounded-lg bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800"><?php echo $message; ?></h3>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8 hover:shadow-lg transition-shadow duration-300">
        <div class="p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-6">
                <?php echo $edit_data ? 'Edit Roadmap' : 'Tambah Roadmap Baru'; ?>
            </h3>
            <form method="post" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="action" value="<?php echo $edit_data ? 'edit' : 'add'; ?>">
                <?php if ($edit_data): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                <?php endif; ?>
                
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Judul Roadmap</label>
                    <input type="text" name="title" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors duration-200"
                           value="<?php echo $edit_data ? htmlspecialchars($edit_data['title']) : ''; ?>" 
                           placeholder="Masukkan judul roadmap"
                           required>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Gambar Roadmap</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-indigo-500 transition-colors duration-200">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label class="relative cursor-pointer rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    <span>Upload file</span>
                                    <input type="file" name="image" class="sr-only" <?php echo $edit_data ? '' : 'required'; ?>>
                                </label>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                        </div>
                    </div>
                    <?php if ($edit_data && $edit_data['image']): ?>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 mb-2">Gambar Saat Ini:</p>
                            <img src="../../<?php echo $edit_data['image']; ?>" alt="Gambar" class="h-32 w-auto object-cover rounded-lg">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4">
                    <?php if ($edit_data): ?>
                        <a href="roadmap_mapping.php" 
                           class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                            Batal
                        </a>
                    <?php endif; ?>
                    <button type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <?php echo $edit_data ? 'Update Roadmap' : 'Simpan Roadmap'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900">Daftar Roadmap</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                                Belum ada data roadmap
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data as $row): ?>
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($row['title']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($row['image']): ?>
                                        <img src="../../<?php echo $row['image']; ?>" alt="Gambar" class="h-20 w-auto object-cover rounded-lg">
                                    <?php else: ?>
                                        <span class="text-sm text-gray-500">Tidak ada gambar</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <a href="roadmap_mapping.php?edit=<?php echo $row['id']; ?>" 
                                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200 mr-2">
                                        <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>
                                    <a href="roadmap_mapping.php?delete=<?php echo $row['id']; ?>" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus roadmap ini?')"
                                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                                        <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus
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
<?php require_once 'template_footer.php';
