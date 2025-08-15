<?php
require_once '../auth.php';
require_once '../../config/database.php';
requireAdminLogin();

// Fungsi sanitasi sederhana
if (!function_exists('sanitize')) {
    function sanitize($data) {
        return htmlspecialchars(strip_tags(trim($data)));
    }
}

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
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
    }
}

try {
    $stmt = $pdo->query("SELECT * FROM activities ORDER BY activity_date DESC");
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $message = 'Error loading activities: ' . $e->getMessage();
    $activities = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activities Management - OHSS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FF0000',
                        secondary: '#1a1a1a',
                    }
                }
            }
        }
    </script>
</head>
<body>
    <!-- Red Header Section -->
    <div class="bg-gradient-to-r from-red-600 to-red-800">
        <header class="text-white py-4">
            <div class="max-w-7xl mx-auto px-4">
                <!-- Company Header -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-4">
                        <img src="../../img/batamindo.png" alt="Batamindo" class="h-12 w-auto bg-white p-1 rounded">
                        <div>
                            <h1 class="text-2xl font-bold text-white">Batamindo Industrial Park</h1>
                            <p class="text-red-200">OHS Security System Management</p>
                        </div>
                    </div>
                    <div class="hidden md:flex items-center space-x-3">
                        <div class="text-right">
                            <p class="text-sm text-white">Welcome, Admin</p>
                            <p class="text-xs text-red-200"><?php echo date('l, d F Y'); ?></p>
                        </div>
                        <a href="../logout.php" class="bg-white hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-150">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Navigation -->
        <div class="border-t border-red-500/30">
            <div class="max-w-7xl mx-auto px-4 py-2">
                <nav class="flex space-x-4">
                    <a href="index.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-chart-line mr-1"></i> Dashboard
                    </a>
                    <a href="activities_tab.php" class="bg-red-700 text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-tasks mr-1"></i> Activities
                    </a>
                    <a href="kpi_tab.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-chart-bar mr-1"></i> KPI
                    </a>
                    <a href="news_tab.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-newspaper mr-1"></i> News
                    </a>
                    <a href="config_tab.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-cog mr-1"></i> Config
                    </a>
                </nav>
            </div>
        </div>
    </div>

    <!-- Gray Background Content Area -->
    <div class="bg-gray-200 min-h-screen">
        <main class="max-w-7xl mx-auto px-4 py-6">
            <?php if ($message): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-sm">
                <?php echo $message; ?>
            </div>
            <?php endif; ?>

            <!-- Add Activity Form -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-3xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-plus text-red-600 mr-3"></i>
                        Add New Activity
                    </h2>
                </div>

                <form method="POST" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="action" value="add">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                                <input type="text" name="title" required 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Activity Date</label>
                                <input type="date" name="activity_date" required 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" rows="3" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500"></textarea>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                            <input type="file" name="image" accept="image/*" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" 
                                class="flex items-center gap-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-8 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5">
                            <i class="fas fa-plus"></i>
                            <span class="font-medium">Add Activity</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Activities List -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-3xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-list text-red-600 mr-3"></i>
                        Activities List
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php if (empty($activities)): ?>
                    <div class="col-span-full text-center py-12">
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-clipboard-list text-6xl"></i>
                        </div>
                        <p class="text-gray-500 text-lg">No activities have been added yet.</p>
                    </div>
                    <?php else: ?>
                        <?php foreach ($activities as $activity): ?>
                        <div class="bg-gray-50 rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300">
                            <div class="relative">
                                <img src="../../<?php echo htmlspecialchars($activity['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($activity['title']); ?>" 
                                     class="w-full h-48 object-cover transform hover:scale-105 transition-transform duration-500">
                                <div class="absolute top-4 right-4 flex space-x-2">
                                    <button type="button" onclick="toggleEditForm(<?php echo $activity['id']; ?>)"
                                            class="bg-white text-red-600 p-2 rounded-lg shadow-md hover:shadow-lg hover:bg-red-50 transition-all duration-300">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this activity?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $activity['id']; ?>">
                                        <button type="submit" 
                                                class="bg-white text-red-600 p-2 rounded-lg shadow-md hover:shadow-lg hover:bg-red-50 transition-all duration-300">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-xl font-bold text-gray-800 truncate" 
                                        title="<?php echo htmlspecialchars($activity['title']); ?>">
                                        <?php echo htmlspecialchars($activity['title']); ?>
                                    </h3>
                                    <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-sm font-medium">
                                        <?php echo date('d M Y', strtotime($activity['activity_date'])); ?>
                                    </span>
                                </div>
                                <p class="text-gray-600 mb-4 line-clamp-3">
                                    <?php echo nl2br(htmlspecialchars($activity['description'])); ?>
                                </p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide success messages
        const messageBox = document.querySelector('.bg-green-100');
        if (messageBox) {
            setTimeout(() => {
                messageBox.style.opacity = '0';
                messageBox.style.transition = 'opacity 0.5s ease-out';
                setTimeout(() => messageBox.remove(), 500);
            }, 5000);
        }
    });
    </script>
</body>
</html>
