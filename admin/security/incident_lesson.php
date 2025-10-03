<?php
require_once '../auth.php';
require_once '../../config/database.php';
requireAdminLogin();

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES);
}

// CRUD logic for incident & lesson
// Incident CRUD
if (isset($_GET['delete_incident']) && is_numeric($_GET['delete_incident'])) {
    $id = (int)$_GET['delete_incident'];
    $stmt = $pdo->prepare("DELETE FROM security_incidents WHERE id = ?");
    $stmt->execute(array($id));
    header("Location: incident_lesson.php?success=incident_deleted");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['incident_form'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title = sanitize($_POST['title']);
    $incident_date = $_POST['incident_date'];
    $incident_time = $_POST['incident_time'];
    $who_name = sanitize($_POST['who_name']);
    $who_npk = sanitize($_POST['who_npk']);
    $summary = sanitize($_POST['summary']);
    $result = sanitize($_POST['result']);
    $root_causes = sanitize($_POST['root_causes']);
    $key_takeaways = sanitize($_POST['key_takeaways']);
    $corrective_actions = sanitize($_POST['corrective_actions']);
    $map_image_path = sanitize($_POST['map_image_path']);
    $status = $_POST['status'];
    $created_by = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;
    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE security_incidents SET title=?, incident_date=?, incident_time=?, who_name=?, who_npk=?, summary=?, result=?, root_causes=?, key_takeaways=?, corrective_actions=?, map_image_path=?, status=?, updated_at=NOW() WHERE id=?");
        $stmt->execute(array($title, $incident_date, $incident_time, $who_name, $who_npk, $summary, $result, $root_causes, $key_takeaways, $corrective_actions, $map_image_path, $status, $id));
    } else {
        $stmt = $pdo->prepare("INSERT INTO security_incidents (title, incident_date, incident_time, who_name, who_npk, summary, result, root_causes, key_takeaways, corrective_actions, map_image_path, status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(array($title, $incident_date, $incident_time, $who_name, $who_npk, $summary, $result, $root_causes, $key_takeaways, $corrective_actions, $map_image_path, $status, $created_by));
    }
    header("Location: incident_lesson.php?success=incident_saved");
    exit();
}
if (isset($_GET['id_incident']) && is_numeric($_GET['id_incident'])) {
    $edit_incident = $pdo->prepare("SELECT * FROM security_incidents WHERE id = ?");
    $edit_incident->execute(array((int)$_GET['id_incident']));
    $edit_incident = $edit_incident->fetch();
}
// Lesson CRUD (unchanged)
// Incident list
$incidents = $pdo->query("SELECT * FROM security_incidents ORDER BY incident_date DESC, id DESC")->fetchAll();
$page_title = 'Incident & Lessons';
require_once 'template_header.php';
?>
    <div class="container mx-auto px-6 py-8">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-2">Incident & Accident Report</h2>
                    <button onclick="document.getElementById('incidentModal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition mb-4 inline-block">
                        <i class="fas fa-plus mr-2"></i>Tambah Incident
                    </button>
                    <!-- Incident Modal Form -->
                    <div id="incidentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
                        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl p-6 relative">
                            <button onclick="document.getElementById('incidentModal').classList.add('hidden')" class="absolute top-2 right-2 text-gray-400 hover:text-red-600"><i class="fas fa-times text-lg"></i></button>
                            <form method="post" class="grid grid-cols-2 gap-4" autocomplete="off">
                                <input type="hidden" name="incident_form" value="1">
                                <input type="hidden" name="id" value="<?php echo isset($edit_incident) ? $edit_incident['id'] : ''; ?>">
                                <div>
                                    <label class="block text-sm font-semibold mb-1">Judul</label>
                                    <input type="text" name="title" value="<?php echo isset($edit_incident) ? htmlspecialchars($edit_incident['title'], ENT_QUOTES) : ''; ?>" class="w-full border rounded px-3 py-2" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1">Tanggal</label>
                                    <input type="date" name="incident_date" value="<?php echo isset($edit_incident) ? htmlspecialchars($edit_incident['incident_date'], ENT_QUOTES) : ''; ?>" class="w-full border rounded px-3 py-2" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1">Waktu</label>
                                    <input type="time" name="incident_time" value="<?php echo isset($edit_incident) ? htmlspecialchars($edit_incident['incident_time'], ENT_QUOTES) : ''; ?>" class="w-full border rounded px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1">Nama Pelapor</label>
                                    <input type="text" name="who_name" value="<?php echo isset($edit_incident) ? htmlspecialchars($edit_incident['who_name'], ENT_QUOTES) : ''; ?>" class="w-full border rounded px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1">NPK Pelapor</label>
                                    <input type="text" name="who_npk" value="<?php echo isset($edit_incident) ? htmlspecialchars($edit_incident['who_npk'], ENT_QUOTES) : ''; ?>" class="w-full border rounded px-3 py-2">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-semibold mb-1">Ringkasan</label>
                                    <textarea name="summary" class="w-full border rounded px-3 py-2" rows="2"><?php echo isset($edit_incident) ? htmlspecialchars($edit_incident['summary'], ENT_QUOTES) : ''; ?></textarea>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-semibold mb-1">Hasil</label>
                                    <textarea name="result" class="w-full border rounded px-3 py-2" rows="2"><?php echo isset($edit_incident) ? htmlspecialchars($edit_incident['result'], ENT_QUOTES) : ''; ?></textarea>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-semibold mb-1">Root Causes</label>
                                    <textarea name="root_causes" class="w-full border rounded px-3 py-2" rows="2"><?php echo isset($edit_incident) ? htmlspecialchars($edit_incident['root_causes'], ENT_QUOTES) : ''; ?></textarea>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-semibold mb-1">Key Takeaways</label>
                                    <textarea name="key_takeaways" class="w-full border rounded px-3 py-2" rows="2"><?php echo isset($edit_incident) ? htmlspecialchars($edit_incident['key_takeaways'], ENT_QUOTES) : ''; ?></textarea>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-semibold mb-1">Corrective Actions</label>
                                    <textarea name="corrective_actions" class="w-full border rounded px-3 py-2" rows="2"><?php echo isset($edit_incident) ? htmlspecialchars($edit_incident['corrective_actions'], ENT_QUOTES) : ''; ?></textarea>
                                </div>
                                <div>
                                        <label class="block text-sm font-semibold mb-1">Map Image Path</label>
                                        <input type="text" name="map_image_path" value="<?php echo isset($edit_incident) ? htmlspecialchars($edit_incident['map_image_path'], ENT_QUOTES) : ''; ?>" class="w-full border rounded px-3 py-2">
                                    </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1">Status</label>
                                    <select name="status" class="w-full border rounded px-3 py-2">
                                        <option value="draft" <?php echo (isset($edit_incident) && $edit_incident['status']=='draft') ? 'selected' : ''; ?>>Draft</option>
                                        <option value="published" <?php echo (!isset($edit_incident) || $edit_incident['status']=='published') ? 'selected' : ''; ?>>Published</option>
                                        <option value="archived" <?php echo (isset($edit_incident) && $edit_incident['status']=='archived') ? 'selected' : ''; ?>>Archived</option>
                                    </select>
                                </div>
                                <div class="col-span-2 text-right">
                                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Simpan Incident</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <table class="table-auto w-full text-sm mb-8">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-2 py-2">Tanggal</th>
                                <th class="px-2 py-2">Judul</th>
                                <th class="px-2 py-2">Ringkasan</th>
                                <th class="px-2 py-2">Pelapor</th>
                                <th class="px-2 py-2">Status</th>
                                <th class="px-2 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($incidents as $row): ?>
                            <tr class="border-b">
                                <td class="px-2 py-2"><?php echo htmlspecialchars($row['incident_date'], ENT_QUOTES); ?></td>
                                <td class="px-2 py-2 font-semibold"><?php echo htmlspecialchars($row['title'], ENT_QUOTES); ?></td>
                                <td class="px-2 py-2"><?php echo htmlspecialchars($row['summary'], ENT_QUOTES); ?></td>
                                <td class="px-2 py-2"><?php echo htmlspecialchars($row['who_name'], ENT_QUOTES); ?></td>
                                <td class="px-2 py-2"><?php echo htmlspecialchars($row['status'], ENT_QUOTES); ?></td>
                                <td class="px-2 py-2">
                                    <a href="incident_lesson.php?id_incident=<?php echo $row['id']; ?>" class="text-blue-600 mr-2" onclick="document.getElementById('incidentModal').classList.remove('hidden');return false;">Edit</a>
                                    <a href="incident_lesson.php?delete_incident=<?php echo $row['id']; ?>" class="text-red-600" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <!-- Lesson section removed: only security_incidents is used -->
            </div>
        </div>
    </div>
</body>
</html>
