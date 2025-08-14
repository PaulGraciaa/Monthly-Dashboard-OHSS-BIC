
<?php
require_once '../auth.php';
require_once '../../config/database.php';
requireAdminLogin();

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $title = isset($_POST['title']) ? sanitize($_POST['title']) : '';
    $content = isset($_POST['content']) ? sanitize($_POST['content']) : '';
    $publish_date = isset($_POST['publish_date']) ? $_POST['publish_date'] : '';
    $status = isset($_POST['status']) ? $_POST['status'] : 'draft';
    if ($title !== '' && $content !== '' && $publish_date !== '') {
        $stmt = $pdo->prepare("INSERT INTO news (title, content, publish_date, status, created_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $content, $publish_date, $status, $_SESSION['admin_id']]);
        $message = 'News berhasil ditambahkan!';
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
</script>
</div>

<!-- News List -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">
        <i class="fas fa-list text-blue-600 mr-2"></i>News List
    </h2>
    <div class="space-y-4">
        <?php foreach ($news as $item): ?>
        <div class="border rounded-lg p-4">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900 mb-2"><?php echo $item['title']; ?></h3>
                    <p class="text-gray-600 text-sm mb-2"><?php echo $item['content']; ?></p>
                    <div class="flex items-center space-x-4 text-xs text-gray-500">
                        <span><i class="fas fa-calendar mr-1"></i><?php echo date('d M Y', strtotime($item['publish_date'])); ?></span>
                        <span class="px-2 py-1 rounded-full text-xs 
                            <?php echo $item['status'] == 'published' ? 'bg-green-100 text-green-800' : 
                                   ($item['status'] == 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'); ?>">
                            <?php echo ucfirst($item['status']); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
