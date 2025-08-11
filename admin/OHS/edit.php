<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../../config/database.php';
requireAdminLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: index.php'); exit; }

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

    $stmt = $pdo->prepare('UPDATE ohs_incidents SET title=?, incident_date=?, incident_time=?, who_name=?, who_npk=?, summary=?, result=?, root_causes=?, key_takeaways=?, corrective_actions=?, status=? WHERE id=?');
    $stmt->execute([$title, $incident_date, $incident_time, $who_name, $who_npk, $summary, $result, $root_causes, $key_takeaways, $corrective_actions, $status, $id]);

    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM ohs_incidents WHERE id = ?');
$stmt->execute([$id]);
$row = $stmt->fetch();
if (!$row) { header('Location: index.php'); exit; }
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
    <form method="post" class="bg-white rounded-lg shadow p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="md:col-span-2">
        <label class="block text-sm">Judul</label>
        <input name="title" required class="border rounded w-full px-3 py-2" value="<?php echo htmlspecialchars($row['title']); ?>" />
      </div>
      <div>
        <label class="block text-sm">Tanggal</label>
        <input type="date" name="incident_date" value="<?php echo htmlspecialchars($row['incident_date']); ?>" class="border rounded w-full px-3 py-2" />
      </div>
      <div>
        <label class="block text-sm">Waktu</label>
        <input type="time" name="incident_time" value="<?php echo htmlspecialchars($row['incident_time']); ?>" class="border rounded w-full px-3 py-2" />
      </div>
      <div>
        <label class="block text-sm">Nama (Who)</label>
        <input name="who_name" class="border rounded w-full px-3 py-2" value="<?php echo htmlspecialchars($row['who_name']); ?>" />
      </div>
      <div>
        <label class="block text-sm">NPK</label>
        <input name="who_npk" class="border rounded w-full px-3 py-2" value="<?php echo htmlspecialchars($row['who_npk']); ?>" />
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm">Ringkasan</label>
        <textarea name="summary" rows="3" class="border rounded w-full px-3 py-2"><?php echo htmlspecialchars($row['summary']); ?></textarea>
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm">Hasil</label>
        <textarea name="result" rows="2" class="border rounded w-full px-3 py-2"><?php echo htmlspecialchars($row['result']); ?></textarea>
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm">Root Causes</label>
        <textarea name="root_causes" rows="4" class="border rounded w-full px-3 py-2"><?php echo htmlspecialchars($row['root_causes']); ?></textarea>
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm">Key Takeaways</label>
        <textarea name="key_takeaways" rows="3" class="border rounded w-full px-3 py-2"><?php echo htmlspecialchars($row['key_takeaways']); ?></textarea>
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm">Corrective Actions</label>
        <textarea name="corrective_actions" rows="3" class="border rounded w-full px-3 py-2"><?php echo htmlspecialchars($row['corrective_actions']); ?></textarea>
      </div>
      <div>
        <label class="block text-sm">Status</label>
        <select name="status" class="border rounded w-full px-3 py-2">
          <?php foreach (["draft","published","archived"] as $st): ?>
            <option value="<?php echo $st; ?>" <?php echo ($row['status']===$st)?'selected':''; ?>><?php echo ucfirst($st); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="md:col-span-2">
        <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Simpan</button>
      </div>
    </form>
  </main>
</body>
</html>


