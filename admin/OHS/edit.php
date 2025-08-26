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
$stmt->execute([$id]);
$row = $stmt->fetch();
if (!$row) { header('Location: index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = sanitize($_POST['title'] ?? '');
  $incident_date = $_POST['incident_date'] ?? date('Y-m-d');
  $incident_time = $_POST['incident_time'] ?? null;
  $who_name = sanitize($_POST['who_name'] ?? '');
  $who_npk = sanitize($_POST['who_npk'] ?? '');
  $summary = $_POST['summary'] ?? '';
  $result = $_POST['result'] ?? '';
  $root_causes = $_POST['root_causes'] ?? '';
  $key_takeaways = $_POST['key_takeaways'] ?? '';
  $corrective_actions = $_POST['corrective_actions'] ?? '';
  $status = $_POST['status'] ?? 'published';

  // Gunakan path gambar yang sudah ada sebagai default
  $photo_image_path = $row['photo_image_path'] ?? '';
  $map_image_path = $row['map_image_path'] ?? '';
  
  $upload_dir = __DIR__ . '/../../uploads/ohs/';
  if (!is_dir($upload_dir)) { 
    mkdir($upload_dir, 0777, true); 
  }

  // Fungsi untuk memproses upload file dengan validasi
  function processFileUpload($fileKey, $uploadDir, $currentPath) {
    if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] == 0) {
      // Validasi tipe file
      $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
      $fileType = mime_content_type($_FILES[$fileKey]['tmp_name']);
      
      if (!in_array($fileType, $allowedTypes)) {
        return ['success' => false, 'path' => $currentPath, 'error' => 'Jenis file tidak diizinkan'];
      }
      
      // Validasi ukuran file (maksimal 5MB)
      if ($_FILES[$fileKey]['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'path' => $currentPath, 'error' => 'Ukuran file terlalu besar (maksimal 5MB)'];
      }
      
      $ext = pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION);
      $filename = $fileKey . '_' . time() . '_' . rand(1000,9999) . '.' . $ext;
      $path = $uploadDir . $filename;
      
      if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $path)) {
        // Hapus file lama jika ada dan bukan file default
        if (!empty($currentPath) && file_exists(__DIR__ . '/../../' . $currentPath)) {
          @unlink(__DIR__ . '/../../' . $currentPath);
        }
        return ['success' => true, 'path' => 'uploads/ohs/' . $filename];
      }
    }
    return ['success' => true, 'path' => $currentPath]; // Tidak ada file yang diupload, gunakan yang lama
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
  $stmt->execute([$title, $incident_date, $incident_time, $who_name, $who_npk, $summary, $result, $root_causes, $key_takeaways, $corrective_actions, $map_image_path, $photo_image_path, $status, $id]);

  header('Location: index.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Insiden - OHS Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .form-label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: #374151;
    }
    .form-input {
      width: 100%;
      padding: 0.5rem 0.75rem;
      border: 1px solid #d1d5db;
      border-radius: 0.375rem;
      box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    .form-input:focus {
      outline: none;
      border-color: #3b82f6;
    }
    .btn-primary {
      background-color: #16a34a;
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 0.375rem;
      font-weight: 600;
    }
    .btn-primary:hover {
      background-color: #15803d;
    }
    .btn-secondary {
      background-color: #6b7280;
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 0.375rem;
      font-weight: 600;
    }
    .btn-secondary:hover {
      background-color: #4b5563;
    }
  </style>
</head>
<body class="bg-gray-50 font-sans">
  <header class="bg-red-600 text-white px-6 py-4">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold">Edit Insiden</h1>
      <a href="index.php" class="bg-white text-red-600 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
        <i class="fas fa-arrow-left mr-2"></i>Kembali
      </a>
    </div>
  </header>

  <main class="max-w-4xl mx-auto px-6 py-6">
    <form method="post" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="md:col-span-2">
        <label class="form-label">Judul</label>
        <input name="title" required class="form-input" value="<?php echo htmlspecialchars($row['title']); ?>" />
      </div>
      <div>
        <label class="form-label">Tanggal</label>
        <input type="date" name="incident_date" value="<?php echo htmlspecialchars($row['incident_date']); ?>" class="form-input" />
      </div>
      <div>
        <label class="form-label">Waktu</label>
        <input type="time" name="incident_time" value="<?php echo htmlspecialchars($row['incident_time']); ?>" class="form-input" />
      </div>
      <div>
        <label class="form-label">Nama (Who)</label>
        <input name="who_name" class="form-input" value="<?php echo htmlspecialchars($row['who_name']); ?>" />
      </div>
      <div>
        <label class="form-label">NPK</label>
        <input name="who_npk" class="form-input" value="<?php echo htmlspecialchars($row['who_npk']); ?>" />
      </div>
      <div class="md:col-span-2">
        <label class="form-label">Ringkasan</label>
        <textarea name="summary" rows="3" class="form-input"><?php echo htmlspecialchars($row['summary']); ?></textarea>
      </div>
      <div class="md:col-span-2">
        <label class="form-label">Hasil</label>
        <textarea name="result" rows="2" class="form-input"><?php echo htmlspecialchars($row['result']); ?></textarea>
      </div>
      <div class="md:col-span-2">
        <label class="form-label">Root Causes</label>
        <textarea name="root_causes" rows="4" class="form-input"><?php echo htmlspecialchars($row['root_causes']); ?></textarea>
      </div>
      <div class="md:col-span-2">
        <label class="form-label">Key Takeaways</label>
        <textarea name="key_takeaways" rows="3" class="form-input"><?php echo htmlspecialchars($row['key_takeaways']); ?></textarea>
      </div>
      <div class="md:col-span-2">
        <label class="form-label">Corrective Actions</label>
        <textarea name="corrective_actions" rows="3" class="form-input"><?php echo htmlspecialchars($row['corrective_actions']); ?></textarea>
      </div>
      <div>
        <label class="form-label">Evidence (Foto)</label>
        <input type="file" name="photo_image" accept="image/*" class="form-input" />
        <?php if (!empty($row['photo_image_path'])): ?>
          <div class="mt-2">
            <p class="text-sm text-gray-600">Foto saat ini:</p>
            <img src="<?php echo htmlspecialchars($row['photo_image_path']); ?>" alt="Evidence" class="w-full h-32 object-cover rounded shadow" />
          </div>
        <?php endif; ?>
      </div>
      <div>
        <label class="form-label">Incident Map</label>
        <input type="file" name="map_image" accept="image/*" class="form-input" />
        <?php if (!empty($row['map_image_path'])): ?>
          <div class="mt-2">
            <p class="text-sm text-gray-600">Map saat ini:</p>
            <img src="<?php echo htmlspecialchars($row['map_image_path']); ?>" alt="Incident Map" class="w-full h-32 object-cover rounded shadow" />
          </div>
        <?php endif; ?>
      </div>
      <div>
        <label class="form-label">Status</label>
        <select name="status" class="form-input">
          <?php foreach (["draft","published","archived"] as $st): ?>
            <option value="<?php echo $st; ?>" <?php echo ($row['status']===$st)?'selected':''; ?>><?php echo ucfirst($st); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="md:col-span-2 flex justify-between pt-4">
        <a href="index.php" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary">Simpan Perubahan</button>
      </div>
    </form>
  </main>
</body>
</html>