
<?php
require_once '../auth.php';
require_once '../../config/database.php';
requireAdminLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Management - OHSS</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        blue: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen">

<?php
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        $title = isset($_POST['title']) ? sanitize($_POST['title']) : '';
        $content = isset($_POST['content']) ? sanitize($_POST['content']) : '';
        $publish_date = isset($_POST['publish_date']) ? $_POST['publish_date'] : '';
        $status = isset($_POST['status']) ? $_POST['status'] : 'draft';
        if ($title !== '' && $content !== '' && $publish_date !== '') {
            $stmt = $pdo->prepare("INSERT INTO news (title, content, publish_date, status, created_by) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $content, $publish_date, $status, $_SESSION['admin_id']]);
            $message = 'News berhasil ditambahkan!';
        }
    } elseif ($_POST['action'] == 'delete') {
        $id = intval($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'News berhasil dihapus!';
        }
    } elseif ($_POST['action'] == 'update') {
        $id = intval($_POST['id'] ?? 0);
        $title = isset($_POST['title']) ? sanitize($_POST['title']) : '';
        $content = isset($_POST['content']) ? sanitize($_POST['content']) : '';
        $publish_date = isset($_POST['publish_date']) ? $_POST['publish_date'] : '';
        $status = isset($_POST['status']) ? $_POST['status'] : 'draft';
        if ($id > 0 && $title !== '' && $content !== '' && $publish_date !== '') {
            $stmt = $pdo->prepare("UPDATE news SET title=?, content=?, publish_date=?, status=? WHERE id=?");
            $stmt->execute([$title, $content, $publish_date, $status, $id]);
            $message = 'News berhasil diupdate!';
        }
    }
}

try {
    $news = $pdo->query("SELECT * FROM news ORDER BY publish_date DESC")->fetchAll();
    if (!$news) $news = [];
} catch (Exception $e) {
    $news = [];
}

?>


<!-- HTML HEAD & CSS -->
<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>News Management</title>
        <!-- Tailwind CSS CDN -->
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <!-- FontAwesome CDN -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

<!-- Header and Navigation -->
<header class="bg-gradient-to-r from-red-600 to-red-800 text-white py-4 shadow-lg mb-6">
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
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <?php echo $message; ?>
</div>
<?php endif; ?>

<!-- Add New News -->
<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">
        <i class="fas fa-plus text-yellow-600 mr-2"></i>Add New News
    </h2>
    <form method="POST" class="space-y-4">
        <input type="hidden" name="action" value="add">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                <input type="text" name="title" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Publish Date</label>
                <input type="date" name="publish_date" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Content</label>
            <textarea name="content" rows="4" required
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500"></textarea>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="archived">Archived</option>
            </select>
        </div>
        
        <div>
            <button type="submit" class="bg-yellow-600 text-white px-6 py-2 rounded-md hover:bg-yellow-700">
                <i class="fas fa-plus mr-2"></i>Add News
            </button>
        </div>
    </form>
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

// Toggle edit form for news item
function toggleEditForm(id) {
    var form = document.getElementById('edit-form-' + id);
    if (form) {
        form.classList.toggle('hidden');
    }
}
</script>
</div>

<!-- News List -->
<div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-lg p-8">
    <h2 class="text-3xl font-bold text-gray-800 mb-8 flex items-center">
        <i class="fas fa-newspaper text-blue-600 mr-3"></i>
        <span>News Management</span>
    </h2>
    <div class="grid gap-8 items-start">
        <?php foreach ($news as $item): ?>
        <div class="bg-white border-0 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col w-full max-w-2xl mx-auto overflow-hidden">
            <div class="p-6 flex flex-col flex-grow relative">
                <div class="absolute top-0 right-0 mt-4 mr-4">
                    <span class="bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs px-4 py-1.5 rounded-full shadow-md font-medium flex items-center">
                        <i class="far fa-calendar-alt mr-2"></i><?php echo date('d M Y', strtotime($item['publish_date'])); ?>
                    </span>
                </div>
                <div class="pr-32 mb-4">
                    <h3 class="font-bold text-xl text-gray-900 hover:text-blue-600 transition-colors duration-200 truncate" title="<?php echo htmlspecialchars($item['title']); ?>"><?php echo htmlspecialchars($item['title']); ?></h3>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed mb-4 flex-grow line-clamp-3"><?php echo nl2br(htmlspecialchars($item['content'])); ?></p>
                <div class="flex items-center gap-3 mt-4 border-t pt-4">
                    <span class="px-3 py-1.5 rounded-full text-xs font-medium inline-flex items-center gap-1.5
                        <?php echo $item['status'] == 'published' ? 'bg-green-100 text-green-800' : 
                               ($item['status'] == 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'); ?>">
                        <i class="fas <?php echo $item['status'] == 'published' ? 'fa-check-circle' : 
                                          ($item['status'] == 'draft' ? 'fa-clock' : 'fa-archive'); ?>"></i>
                        <?php echo ucfirst($item['status']); ?>
                    </span>
                    <button type="button" class="flex items-center gap-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-2 rounded-lg shadow-md text-sm font-medium transition-all duration-300 transform hover:scale-105" onclick="toggleEditForm(<?php echo $item['id']; ?>)">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <form method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this news item?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                        <button type="submit" class="flex items-center gap-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-4 py-2 rounded-lg shadow-md text-sm font-medium transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-trash-alt"></i> Delete
                        </button>
                    </form>
                </div>
                <form method="POST" id="edit-form-<?php echo $item['id']; ?>" class="hidden mt-6 space-y-4 bg-gray-50 p-6 rounded-xl border shadow-sm">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                    <div class="relative">
                        <label class="text-sm font-medium text-gray-700 block mb-2">Title</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($item['title']); ?>" required 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all duration-200">
                    </div>
                    <div class="relative">
                        <label class="text-sm font-medium text-gray-700 block mb-2">Content</label>
                        <textarea name="content" rows="3" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all duration-200"><?php echo htmlspecialchars($item['content']); ?></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="text-sm font-medium text-gray-700 block mb-2">Publish Date</label>
                            <input type="date" name="publish_date" value="<?php echo $item['publish_date']; ?>" required 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all duration-200">
                        </div>
                        <div class="relative">
                            <label class="text-sm font-medium text-gray-700 block mb-2">Status</label>
                            <select name="status" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all duration-200">
                                <option value="draft" <?php if($item['status']==='draft') echo 'selected'; ?>>Draft</option>
                                <option value="published" <?php if($item['status']==='published') echo 'selected'; ?>>Published</option>
                                <option value="archived" <?php if($item['status']==='archived') echo 'selected'; ?>>Archived</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end mt-2">
                        <button type="submit" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-2.5 rounded-lg shadow-md text-sm font-medium transition-all duration-300 transform hover:scale-105 flex items-center gap-2">
                            <i class="fas fa-check"></i> Update News
                        </button>
                    </div>
                </form>
                </div>
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

// Toggle edit form for news items
function toggleEditForm(id) {
    var form = document.getElementById('edit-form-' + id);
    if (form) {
        form.classList.toggle('hidden');
    }
}
</script>
</body>
</html>
