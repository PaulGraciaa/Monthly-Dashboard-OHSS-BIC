<?php
require_once '../auth.php';
require_once '../../config/database.php';
requireAdminLogin();

$message = '';

// CRUD logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $stat_name = trim($_POST['stat_name'] ?? '');
    $stat_value = trim($_POST['stat_value'] ?? '');
    $stat_description = trim($_POST['stat_description'] ?? '');
    $stat_icon = trim($_POST['stat_icon'] ?? '');
    $display_order = (int)($_POST['display_order'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $stmt = $pdo->prepare("INSERT INTO dashboard_stats (stat_name, stat_value, stat_description, stat_icon, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$stat_name, $stat_value, $stat_description, $stat_icon, $display_order, $is_active])) {
                $message = 'Data berhasil ditambahkan!';
            }
        } elseif ($_POST['action'] === 'update' && $id) {
            $stmt = $pdo->prepare("UPDATE dashboard_stats SET stat_name=?, stat_value=?, stat_description=?, stat_icon=?, display_order=?, is_active=? WHERE id=?");
            if ($stmt->execute([$stat_name, $stat_value, $stat_description, $stat_icon, $display_order, $is_active, $id])) {
                $message = 'Data berhasil diupdate!';
            }
        } elseif ($_POST['action'] === 'delete' && $id) {
            $stmt = $pdo->prepare("DELETE FROM dashboard_stats WHERE id=?");
            if ($stmt->execute([$id])) {
                $message = 'Data berhasil dihapus!';
            }
        }
    }
}

$stats = $pdo->query("SELECT * FROM dashboard_stats ORDER BY display_order, id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Stats Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans">
    <div class="container mx-auto px-6 py-8">
        <h1 class="text-3xl font-extrabold mb-6 text-primary-blue flex items-center gap-2"><i class="fas fa-database"></i> Dashboard Stats Management</h1>
        <?php if ($message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 shadow">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>
    <!-- Form tambah dashboard stats dihapus sesuai permintaan user -->
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto bg-white rounded-xl shadow-lg">
                <thead class="bg-gradient-to-r from-blue-50 to-blue-100">
                    <tr>
                        <th class="px-6 py-3 text-xs font-bold text-gray-700 w-1/2">Name</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-700 w-1/3">Value</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-700 w-20">Order</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-700 w-20">Active</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($stats as $stat): ?>
                    <tr class="border-b hover:bg-blue-50 transition">
                        <form method="POST" class="contents">
                        <input type="hidden" name="id" value="<?php echo $stat['id']; ?>">
                        <td class="px-6 py-2 text-sm text-gray-900 font-semibold whitespace-normal break-words"><input type="text" name="stat_name" value="<?php echo htmlspecialchars($stat['stat_name']); ?>" class="border px-2 py-1 rounded w-full"></td>
                        <td class="px-6 py-2 text-sm text-gray-900 whitespace-normal break-words"><input type="text" name="stat_value" value="<?php echo htmlspecialchars($stat['stat_value']); ?>" class="border px-2 py-1 rounded w-full"></td>
                        <td class="px-4 py-2 text-center"><input type="number" name="display_order" value="<?php echo $stat['display_order']; ?>" class="border px-2 py-1 rounded w-16"></td>
                        <td class="px-4 py-2 text-center"><input type="checkbox" name="is_active" value="1" <?php if($stat['is_active']) echo 'checked'; ?>></td>
                        <td class="px-4 py-2 flex gap-2 justify-center">
                            <button type="submit" name="action" value="update" class="bg-gradient-to-r from-blue-500 to-blue-400 text-white px-3 py-1 rounded-lg font-semibold shadow hover:from-blue-600 hover:to-blue-500 transition">Update</button>
                            <button type="submit" name="action" value="delete" class="bg-gradient-to-r from-red-500 to-red-400 text-white px-3 py-1 rounded-lg font-semibold shadow hover:from-red-600 hover:to-red-500 transition" onclick="return confirm('Yakin hapus?')">Delete</button>
                        </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
