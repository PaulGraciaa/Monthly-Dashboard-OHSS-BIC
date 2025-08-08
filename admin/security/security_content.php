<?php
session_start();
require_once '../../config/database.php';
checkAdminLogin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $section_name = sanitize($_POST['section_name']);
            $title = sanitize($_POST['title']);
            $content = sanitize($_POST['content']);
            $display_order = sanitize($_POST['display_order']);
            $image_path = '';
            
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $upload_dir = '../img/';
                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $file_name = 'security_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image_path = 'img/' . $file_name;
                }
            }
            
            $stmt = $pdo->prepare("INSERT INTO security_content (section_name, title, content, image_path, display_order, created_by) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$section_name, $title, $content, $image_path, $display_order, $_SESSION['admin_id']])) {
                $message = 'Security content berhasil ditambahkan!';
            }
        } elseif ($_POST['action'] == 'update') {
            $id = $_POST['id'];
            $section_name = sanitize($_POST['section_name']);
            $title = sanitize($_POST['title']);
            $content = sanitize($_POST['content']);
            $display_order = sanitize($_POST['display_order']);
            
            $stmt = $pdo->prepare("UPDATE security_content SET section_name = ?, title = ?, content = ?, display_order = ? WHERE id = ?");
            if ($stmt->execute([$section_name, $title, $content, $display_order, $id])) {
                $message = 'Security content berhasil diperbarui!';
            }
        } elseif ($_POST['action'] == 'delete') {
            $id = $_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM security_content WHERE id = ?");
            if ($stmt->execute([$id])) {
                $message = 'Security content berhasil dihapus!';
            }
        }
    }
}

$securityContent = $pdo->query("SELECT * FROM security_content ORDER BY display_order")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Content Management - OHSS Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <header class="bg-red-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <img src="../img/batamindo.png" alt="Batamindo Logo" class="h-8 mr-4">
                    <h1 class="text-xl font-bold">Security Content Management</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="dashboard.php" class="text-white hover:text-gray-200">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                    </a>
                    <a href="logout.php" class="bg-red-700 hover:bg-red-800 px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Add New Security Content -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">
                <i class="fas fa-plus text-blue-600 mr-2"></i>Add New Security Content
            </h2>
            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="action" value="add">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Section Name</label>
                        <select name="section_name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="patrol">Security Patrol</option>
                            <option value="access_control">Access Control</option>
                            <option value="incident_report">Security Incident Report</option>
                            <option value="training">Security Training</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Display Order</label>
                        <input type="number" name="display_order" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <input type="text" name="title" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                    <textarea name="content" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Image (Optional)</label>
                    <input type="file" name="image" accept="image/*" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>Add Security Content
                    </button>
                </div>
            </form>
        </div>

        <!-- Security Content List -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">
                <i class="fas fa-list text-blue-600 mr-2"></i>Security Content List
            </h2>
            <div class="space-y-4">
                <?php foreach ($securityContent as $content): ?>
                <div class="border rounded-lg p-4">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <h3 class="font-semibold text-gray-900 mr-4"><?php echo $content['title']; ?></h3>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                    <?php echo ucfirst(str_replace('_', ' ', $content['section_name'])); ?>
                                </span>
                            </div>
                            <p class="text-gray-600 text-sm mb-2"><?php echo $content['content']; ?></p>
                            <div class="flex items-center space-x-4 text-xs text-gray-500">
                                <span>Order: <?php echo $content['display_order']; ?></span>
                                <span>Status: <?php echo $content['is_active'] ? 'Active' : 'Inactive'; ?></span>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="editContent(<?php echo $content['id']; ?>)" 
                                    class="bg-yellow-600 text-white px-3 py-1 rounded text-sm hover:bg-yellow-700">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>
                            <form method="POST" class="inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $content['id']; ?>">
                                <button type="submit" onclick="return confirm('Yakin ingin menghapus content ini?')" 
                                        class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        function editContent(id) {
            // Implement edit functionality
            alert('Edit functionality for content ID: ' + id);
        }
    </script>
</body>
</html> 