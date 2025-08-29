<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../../config/database.php';
requireAdminLogin();

// Function sanitize jika belum ada
if (!function_exists('sanitize')) {
    function sanitize($data) {
        return htmlspecialchars(strip_tags(trim($data)));
    }
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: index.php'); exit; }

// Ambil data yang akan diedit sebelum memproses form
$stmt = $pdo->prepare('SELECT * FROM ohs_incidents WHERE id = ?');
$stmt->execute(array($id));
$row = $stmt->fetch();
if (!$row) { header('Location: index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = sanitize(isset($_POST['title']) ? $_POST['title'] : '');
  $incident_date = isset($_POST['incident_date']) ? $_POST['incident_date'] : date('Y-m-d');
  $incident_time = isset($_POST['incident_time']) ? $_POST['incident_time'] : null;
  $who_name = sanitize(isset($_POST['who_name']) ? $_POST['who_name'] : '');
  $who_npk = sanitize(isset($_POST['who_npk']) ? $_POST['who_npk'] : '');
  $summary = isset($_POST['summary']) ? $_POST['summary'] : '';
  $result = isset($_POST['result']) ? $_POST['result'] : '';
  $root_causes = isset($_POST['root_causes']) ? $_POST['root_causes'] : '';
  $key_takeaways = isset($_POST['key_takeaways']) ? $_POST['key_takeaways'] : '';
  $corrective_actions = isset($_POST['corrective_actions']) ? $_POST['corrective_actions'] : '';
  $status = isset($_POST['status']) ? $_POST['status'] : 'published';

  // Gunakan path gambar yang sudah ada sebagai default
  $photo_image_path = isset($row['photo_image_path']) ? $row['photo_image_path'] : '';
  $map_image_path = isset($row['map_image_path']) ? $row['map_image_path'] : '';
  
  $upload_dir = __DIR__ . '/../../uploads/ohs/';
  if (!is_dir($upload_dir)) { 
    mkdir($upload_dir, 0777, true); 
  }

  // Fungsi untuk memproses upload file dengan validasi
  function processFileUpload($fileKey, $uploadDir, $currentPath) {
    if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] == 0) {
      // Validasi tipe file
      $allowedTypes = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');
      $fileType = mime_content_type($_FILES[$fileKey]['tmp_name']);
      
      if (!in_array($fileType, $allowedTypes)) {
        return array('success' => false, 'path' => $currentPath, 'error' => 'Jenis file tidak diizinkan');
      }
      
      // Validasi ukuran file (maksimal 5MB)
      if ($_FILES[$fileKey]['size'] > 5 * 1024 * 1024) {
        return array('success' => false, 'path' => $currentPath, 'error' => 'Ukuran file terlalu besar (maksimal 5MB)');
      }
      
      $ext = pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION);
      $filename = $fileKey . '_' . time() . '_' . rand(1000,9999) . '.' . $ext;
      $path = $uploadDir . $filename;
      
      if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $path)) {
        // Hapus file lama jika ada dan bukan file default
        if (!empty($currentPath) && file_exists(__DIR__ . '/../../' . $currentPath)) {
          @unlink(__DIR__ . '/../../' . $currentPath);
        }
        return array('success' => true, 'path' => 'uploads/ohs/' . $filename);
      }
    }
    return array('success' => true, 'path' => $currentPath); // Tidak ada file yang diupload, gunakan yang lama
  }

  // Proses upload untuk photo_image
  $photoResult = processFileUpload('photo_image', $upload_dir, $photo_image_path);
  if (!$photoResult['success']) {
    // Handle error upload photo
    die($photoResult['error']);
  }
  $photo_image_path = $photoResult['path'];

  // Proses upload untuk map_image
  $mapResult = processFileUpload('map_image', $upload_dir, $map_image_path);
  if (!$mapResult['success']) {
    // Handle error upload map
    die($mapResult['error']);
  }
  $map_image_path = $mapResult['path'];

  $stmt = $pdo->prepare('UPDATE ohs_incidents SET title=?, incident_date=?, incident_time=?, who_name=?, who_npk=?, summary=?, result=?, root_causes=?, key_takeaways=?, corrective_actions=?, map_image_path=?, photo_image_path=?, status=?, updated_at=NOW() WHERE id=?');
  $stmt->execute(array($title, $incident_date, $incident_time, $who_name, $who_npk, $summary, $result, $root_causes, $key_takeaways, $corrective_actions, $map_image_path, $photo_image_path, $status, $id));

  header('Location: index.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Incident - OHSS Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Header and Navigation -->
    <header class="bg-gradient-to-r from-red-600 to-red-800 text-white py-4 shadow-lg mb-6">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Company Header -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-4">
                    <img src="../../img/batamindo.png" alt="Batamindo" class="h-12 w-auto bg-white p-1 rounded">
                    <div>
                        <h1 class="text-2xl font-bold text-white">Batamindo Industrial Park</h1>
                        <p class="text-red-200">OHS Security System</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="text-right">
                        <p class="text-sm text-white">Welcome, Admin</p>
                        <p class="text-xs text-red-200"><?php echo date('l, d F Y'); ?></p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="index.php" class="bg-white text-red-600 px-4 py-2 rounded-lg hover:bg-red-100 transition">
                            <i class="fas fa-arrow-left mr-1"></i> Back
                        </a>
                        <a href="../logout.php" class="bg-white hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-150">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 pb-12">
        <!-- Page Title -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Edit Incident</h2>
            <p class="mt-1 text-sm text-gray-600">Update the incident details below.</p>
        </div>

        <!-- Incident Form -->
        <form method="post" enctype="multipart/form-data" class="bg-white rounded-lg shadow-lg">
            <!-- Form Header -->
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-medium text-gray-900">Incident Information</h3>
            </div>

            <!-- Form Content -->
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input name="title" required 
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                           value="<?php echo htmlspecialchars($row['title']); ?>" 
                           placeholder="Enter incident title" />
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="incident_date" 
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                           value="<?php echo htmlspecialchars($row['incident_date']); ?>" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                    <input type="time" name="incident_time" 
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                           value="<?php echo htmlspecialchars($row['incident_time']); ?>" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reporter Name</label>
                    <input name="who_name" 
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                           value="<?php echo htmlspecialchars($row['who_name']); ?>" 
                           placeholder="Enter reporter name" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NPK</label>
                    <input name="who_npk" 
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                           value="<?php echo htmlspecialchars($row['who_npk']); ?>" 
                           placeholder="Enter NPK number" />
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Summary</label>
                    <textarea name="summary" rows="3" 
                              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                              placeholder="Describe what happened..."><?php echo htmlspecialchars($row['summary']); ?></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Result</label>
                    <textarea name="result" rows="2" 
                              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                              placeholder="What was the outcome..."><?php echo htmlspecialchars($row['result']); ?></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Root Causes</label>
                    <textarea name="root_causes" rows="4" 
                              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                              placeholder="List the root causes..."><?php echo htmlspecialchars($row['root_causes']); ?></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Key Takeaways</label>
                    <textarea name="key_takeaways" rows="3" 
                              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                              placeholder="List the key lessons learned..."><?php echo htmlspecialchars($row['key_takeaways']); ?></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Corrective Actions</label>
                    <textarea name="corrective_actions" rows="3" 
                              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                              placeholder="List the actions taken to prevent recurrence..."><?php echo htmlspecialchars($row['corrective_actions']); ?></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Evidence Photo</label>
                    <?php if (!empty($row['photo_image_path'])): ?>
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">Current Photo:</p>
                        <img src="../../<?php echo htmlspecialchars($row['photo_image_path']); ?>" 
                             alt="Evidence" 
                             class="w-full h-40 object-cover rounded-lg shadow-sm" />
                    </div>
                    <?php endif; ?>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" 
                                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="photo-image" class="relative cursor-pointer rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload new photo</span>
                                    <input id="photo-image" name="photo_image" type="file" accept="image/*" class="sr-only">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Incident Map</label>
                    <?php if (!empty($row['map_image_path'])): ?>
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">Current Map:</p>
                        <img src="../../<?php echo htmlspecialchars($row['map_image_path']); ?>" 
                             alt="Incident Map" 
                             class="w-full h-40 object-cover rounded-lg shadow-sm" />
                    </div>
                    <?php endif; ?>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" 
                                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="map-image" class="relative cursor-pointer rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload new map</span>
                                    <input id="map-image" name="map_image" type="file" accept="image/*" class="sr-only">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" 
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <?php foreach (array("draft","published","archived") as $st): ?>
                            <option value="<?php echo $st; ?>" <?php echo ($row['status']===$st)?'selected':''; ?>><?php echo ucfirst($st); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Form Actions -->
                <div class="md:col-span-2 border-t border-gray-200 pt-5">
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="window.location.href='index.php'" 
                                class="rounded-lg border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="rounded-lg border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </main>
</body>
</html>