<?php
require_once '../auth.php';
require_once '../../config/database.php';
requireAdminLogin();

// Helper: sanitize input

$message = '';
if (isset($_GET['success'])) $message = 'Activity berhasil ditambahkan!';
if (isset($_GET['updated'])) $message = 'Activity berhasil diupdate!';
if (isset($_GET['deleted'])) $message = 'Activity berhasil dihapus!';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if (isset($_POST['action']) && $_POST['action'] == 'add') {
            $title = sanitize($_POST['title'] ?? '');
            $description = sanitize($_POST['description'] ?? '');
            $activity_date = $_POST['activity_date'] ?? '';
            $image_path = 'uploads/activity/default.jpg';
            $upload_dir = '../../uploads/activity/';
            if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $file_name = 'activity_' . time() . '_' . rand(1000,9999) . '.' . $file_extension;
                $upload_path = $upload_dir . $file_name;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image_path = 'uploads/activity/' . $file_name;
                }
            }
            $stmt = $pdo->prepare("INSERT INTO activities (title, description, activity_date, image_path, created_by) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $activity_date, $image_path, $_SESSION['admin_id']]);
            header('Location: activities_tab.php?success=1');
            exit();
        } elseif (isset($_POST['action']) && $_POST['action'] == 'delete') {
            $id = intval($_POST['id'] ?? 0);
            $stmt = $pdo->prepare("DELETE FROM activities WHERE id = ?");
            $stmt->execute([$id]);
            header('Location: activities_tab.php?deleted=1');
            exit();
        } elseif (isset($_POST['action']) && $_POST['action'] == 'update') {
            $id = intval($_POST['id'] ?? 0);
            $title = sanitize($_POST['title'] ?? '');
            $description = sanitize($_POST['description'] ?? '');
            $activity_date = $_POST['activity_date'] ?? '';
            $image_path = $_POST['old_image_path'] ?? 'uploads/activity/default.jpg';
            $upload_dir = '../../uploads/activity/';
            if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $file_name = 'activity_' . time() . '_' . rand(1000,9999) . '.' . $file_extension;
                $upload_path = $upload_dir . $file_name;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image_path = 'uploads/activity/' . $file_name;
                }
            }
            $stmt = $pdo->prepare("UPDATE activities SET title=?, description=?, activity_date=?, image_path=? WHERE id=?");
            $stmt->execute([$title, $description, $activity_date, $image_path, $id]);
            header('Location: activities_tab.php?updated=1');
            exit();
        }
    } catch (Exception $e) {
        $message = 'Terjadi kesalahan: ' . $e->getMessage();
    }
}

?>


<!-- HTML HEAD & CSS -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activities Management</title>
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

<!-- Hamburger Menu Navigation -->
<nav class="bg-white shadow mb-6">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16 items-center">
            <div class="flex-shrink-0 flex items-center">
                <span class="font-bold text-lg text-green-700">OHSS Management</span>
            </div>
            <div class="hidden md:flex space-x-4">
                <a href="activities_tab.php" class="text-gray-700 hover:text-green-700 font-semibold">Activities</a>
                <a href="kpi_tab.php" class="text-gray-700 hover:text-green-700 font-semibold">KPI</a>
                <a href="dashboard_stats_tab.php" class="text-gray-700 hover:text-green-700 font-semibold">Stats</a>
                <a href="config_tab.php" class="text-gray-700 hover:text-green-700 font-semibold">Config</a>
                <a href="news_tab.php" class="text-gray-700 hover:text-green-700 font-semibold">News</a>
            </div>
            <div class="md:hidden flex items-center">
                <button id="hamburgerBtn" class="text-gray-700 focus:outline-none">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
        <div id="mobileMenu" class="md:hidden hidden flex-col space-y-2 pb-4">
            <a href="activities_tab.php" class="block text-gray-700 hover:text-green-700 font-semibold">Activities</a>
            <a href="kpi_tab.php" class="block text-gray-700 hover:text-green-700 font-semibold">KPI</a>
            <a href="dashboard_stats_tab.php" class="block text-gray-700 hover:text-green-700 font-semibold">Stats</a>
            <a href="config_tab.php" class="block text-gray-700 hover:text-green-700 font-semibold">Config</a>
            <a href="news_tab.php" class="block text-gray-700 hover:text-green-700 font-semibold">News</a>
        </div>
    </div>
</nav>

<?php if ($message): ?>
<div id="message-box" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <?php echo $message; ?>
</div>
<?php endif; ?>

<!-- Add New Activity -->
<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">
        <i class="fas fa-plus text-green-600 mr-2"></i>Add New Activity
    </h2>
    <form method="POST" enctype="multipart/form-data" class="space-y-4">
        <input type="hidden" name="action" value="add">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                <input type="text" name="title" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Activity Date</label>
                <input type="date" name="activity_date" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
            <input type="file" name="image" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>
        <div>
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700">
                <i class="fas fa-plus mr-2"></i>Add Activity
            </button>
        </div>
    </form>
</div>

<!-- Activities List -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">
        <i class="fas fa-list text-blue-600 mr-2"></i>Activities List
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($activities as $activity): ?>
        <div class="border rounded-lg overflow-hidden shadow-sm">
            <img src="../../<?php echo $activity['image_path']; ?>" alt="<?php echo $activity['title']; ?>" class="w-full h-48 object-cover">
            <div class="p-4">
                <h3 class="font-semibold text-gray-900 mb-2"><?php echo $activity['title']; ?></h3>
                <p class="text-gray-600 text-sm mb-2"><?php echo $activity['description']; ?></p>
                <p class="text-gray-500 text-xs mb-3"><?php echo date('d M Y', strtotime($activity['activity_date'])); ?></p>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?php echo $activity['id']; ?>">
                    <button type="submit" class="text-red-600 hover:underline mr-2" onclick="return confirm('Yakin ingin menghapus activity ini?')">Delete</button>
                </form>
                <button type="button" class="text-blue-600 hover:underline" onclick="toggleEditForm(<?php echo $activity['id']; ?>)">Edit</button>
                <form method="POST" enctype="multipart/form-data" id="edit-form-<?php echo $activity['id']; ?>" style="display:none; margin-top:10px;">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo $activity['id']; ?>">
                    <input type="hidden" name="old_image_path" value="<?php echo $activity['image_path']; ?>">
                    <input type="text" name="title" value="<?php echo htmlspecialchars($activity['title']); ?>" required class="w-full mb-2 px-2 py-1 border rounded">
                    <textarea name="description" rows="2" class="w-full mb-2 px-2 py-1 border rounded"><?php echo htmlspecialchars($activity['description']); ?></textarea>
                    <input type="date" name="activity_date" value="<?php echo $activity['activity_date']; ?>" required class="w-full mb-2 px-2 py-1 border rounded">
                    <input type="file" name="image" class="w-full mb-2 px-2 py-1 border rounded">
                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">Update</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<script>
// Hamburger menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('hamburgerBtn');
    const menu = document.getElementById('mobileMenu');
    if (btn && menu) {
        btn.addEventListener('click', function() {
            menu.classList.toggle('hidden');
        });
    }
});
function toggleEditForm(id) {
        var form = document.getElementById('edit-form-' + id);
        if (form.style.display === 'none') {
                form.style.display = 'block';
        } else {
                form.style.display = 'none';
        }
}
</script>
