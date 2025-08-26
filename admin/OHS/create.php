<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../../config/database.php';
requireAdminLogin();

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

  // Handle file upload
  $photo_image_path = '';
  $map_image_path = '';
  $upload_dir = __DIR__ . '/../../uploads/ohs/';
  if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }
  if (isset($_FILES['photo_image']) && $_FILES['photo_image']['error'] == 0) {
    $ext = pathinfo($_FILES['photo_image']['name'], PATHINFO_EXTENSION);
    $filename = 'evidence_' . time() . '_' . rand(1000,9999) . '.' . $ext;
    $path = $upload_dir . $filename;
    if (move_uploaded_file($_FILES['photo_image']['tmp_name'], $path)) {
      $photo_image_path = 'uploads/ohs/' . $filename;
    }
  }
  if (isset($_FILES['map_image']) && $_FILES['map_image']['error'] == 0) {
    $ext = pathinfo($_FILES['map_image']['name'], PATHINFO_EXTENSION);
    $filename = 'map_' . time() . '_' . rand(1000,9999) . '.' . $ext;
    $path = $upload_dir . $filename;
    if (move_uploaded_file($_FILES['map_image']['tmp_name'], $path)) {
      $map_image_path = 'uploads/ohs/' . $filename;
    }
  }

  $stmt = $pdo->prepare('INSERT INTO ohs_incidents (title, incident_date, incident_time, who_name, who_npk, summary, result, root_causes, key_takeaways, corrective_actions, map_image_path, photo_image_path, status, created_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
  $stmt->execute([$title, $incident_date, $incident_time, $who_name, $who_npk, $summary, $result, $root_causes, $key_takeaways, $corrective_actions, $map_image_path, $photo_image_path, $status, $_SESSION['admin_id']]);

  header('Location: index.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tambah Insiden - OHS Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans">
  <header class="bg-red-600 text-white px-6 py-4">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold">Tambah Insiden</h1>
      <a href="index.php" class="bg-white text-red-600 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
        <i class="fas fa-arrow-left mr-2"></i>Kembali
      </a>
    </div>
  </header>

  <main class="max-w-4xl mx-auto px-6 py-6">
  <form method="post" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="md:col-span-2">
        <label class="block text-sm">Judul</label>
        <input name="title" required class="border rounded w-full px-3 py-2" />
      </div>
      <div>
        <label class="block text-sm">Tanggal</label>
        <input type="date" name="incident_date" value="<?php echo date('Y-m-d'); ?>" class="border rounded w-full px-3 py-2" />
      </div>
      <div>
        <label class="block text-sm">Waktu</label>
        <input type="time" name="incident_time" class="border rounded w-full px-3 py-2" />
      </div>
      <div>
        <label class="block text-sm">Nama (Who)</label>
        <input name="who_name" class="border rounded w-full px-3 py-2" />
      </div>
      <div>
        <label class="block text-sm">NPK</label>
        <input name="who_npk" class="border rounded w-full px-3 py-2" />
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm">Ringkasan</label>
        <textarea name="summary" rows="3" class="border rounded w-full px-3 py-2"></textarea>
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm">Hasil</label>
        <textarea name="result" rows="2" class="border rounded w-full px-3 py-2"></textarea>
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm">Root Causes</label>
        <textarea name="root_causes" rows="4" class="border rounded w-full px-3 py-2"></textarea>
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm">Key Takeaways</label>
        <textarea name="key_takeaways" rows="3" class="border rounded w-full px-3 py-2"></textarea>
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm">Corrective Actions</label>
        <textarea name="corrective_actions" rows="3" class="border rounded w-full px-3 py-2"></textarea>
      </div>
      <div>
        <label class="block text-sm">Status</label>
        <select name="status" class="border rounded w-full px-3 py-2">
          <option value="draft">Draft</option>
          <option value="published" selected>Published</option>
          <option value="archived">Archived</option>
        </select>
      </div>
      <div>
        <label class="block text-sm">Evidence (Foto)</label>
        <input type="file" name="photo_image" accept="image/*" class="border rounded w-full px-3 py-2" />
      </div>
      <div>
        <label class="block text-sm">Incident Map</label>
        <input type="file" name="map_image" accept="image/*" class="border rounded w-full px-3 py-2" />
      </div>
      <div class="md:col-span-2">
        <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Simpan</button>
      </div>
    </form>
  </main>
</body>
</html>


