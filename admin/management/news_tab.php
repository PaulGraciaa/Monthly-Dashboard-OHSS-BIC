<?php
require_once '../auth.php';
require_once '../../config/database.php';
requireAdminLogin();

// Initialize variables
$message = '';

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    // Process form data...
    if ($_POST['action'] === 'add') {
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $content = isset($_POST['content']) ? trim($_POST['content']) : '';
        $publish_date = isset($_POST['publish_date']) ? $_POST['publish_date'] : date('Y-m-d');
        $status = isset($_POST['status']) ? $_POST['status'] : 'draft';
        if ($title && $content) {
            $stmt = $pdo->prepare("INSERT INTO news (title, content, publish_date, status) VALUES (?, ?, ?, ?)");
            $stmt->execute(array($title, $content, $publish_date, $status));
            $_SESSION['notif'] = 'Berita berhasil ditambahkan!';
            header('Location: news_tab.php');
            exit;
        }
    } else if ($_POST['action'] === 'update') {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $content = isset($_POST['content']) ? trim($_POST['content']) : '';
        $publish_date = isset($_POST['publish_date']) ? $_POST['publish_date'] : date('Y-m-d');
        $status = isset($_POST['status']) ? $_POST['status'] : 'draft';
        if ($id && $title && $content) {
            $stmt = $pdo->prepare("UPDATE news SET title=?, content=?, publish_date=?, status=? WHERE id=?");
            $stmt->execute(array($title, $content, $publish_date, $status, $id));
            $_SESSION['notif'] = 'Berita berhasil diupdate!';
            header('Location: news_tab.php');
            exit;
        }
    } else if ($_POST['action'] === 'delete') {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
            $stmt->execute(array($id));
            $_SESSION['notif'] = 'Berita berhasil dihapus!';
            header('Location: news_tab.php');
            exit;
        }
    }
}

// Fetch news
try {
    $news = $pdo->query("SELECT * FROM news ORDER BY publish_date DESC")->fetchAll();
    if (!$news) $news = array();
} catch (Exception $e) {
    $news = array();
    $message = 'Error loading news: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Management - OHSS</title>
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
    <style>
        /* Memastikan header selalu di atas notifikasi */
        header {
            z-index: 60;
            position: relative;
        }
        
        /* Notifikasi di bawah header */
        @keyframes notifSlideIn {
            0% { opacity: 0; transform: translateY(-30px) scale(0.95); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes notifFadeOut {
            to { opacity: 0; transform: translateY(-10px) scale(0.98); }
        }
        .notif-animate-in { animation: notifSlideIn 0.5s cubic-bezier(.4,0,.2,1); }
        .notif-animate-out { animation: notifFadeOut 0.5s cubic-bezier(.4,0,.2,1) forwards; }
    </style>
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
                    <div class="flex items-center space-x-3">
                        <div class="text-right">
                            <p class="text-sm text-white">Welcome, Admin</p>
                            <p class="text-xs text-red-200"><?php echo date('l, d F Y'); ?></p>
                        </div>
                        <a href="../logout.php" class="bg-white hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-150 logout-btn">
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
                    <a href="../dashboard.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-chart-line mr-1"></i> Dashboard
                    </a>
                    <a href="activities_tab.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-tasks mr-1"></i> Activities
                    </a>
                    <a href="life_saving_rules_tab.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-shield-alt mr-1"></i> Life Saving Rules & BASCOM
                    </a>
                    <a href="kpi_tab.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-chart-bar mr-1"></i> KPI
                    </a>
                    <a href="news_tab.php" class="bg-red-700 text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-newspaper mr-1"></i> News
                    </a>
                    <a href="config_tab.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-cog mr-1"></i> Config
                    </a>
                    <a href="dashboard_stats_tab.php" class="text-red-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-chart-pie mr-1"></i> Stats
                    </a>
                </nav>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-8">
        <?php if (!isset($_SESSION)) { session_start(); } ?>
        <?php if (!empty($_SESSION['notif'])): ?>
        <div id="notifBox" class="fixed top-24 right-8 z-50 min-w-[260px] max-w-xs bg-white border border-green-400 shadow-2xl rounded-xl flex items-center px-5 py-4 gap-3 notif-animate-in" style="box-shadow:0 8px 32px 0 rgba(34,197,94,0.15);">
            <div class="flex-shrink-0">
                <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-green-100">
                    <i class="fas fa-check text-green-600 text-xl"></i>
                </span>
            </div>
            <div class="flex-1 text-green-800 font-semibold text-sm">
                <?php echo $_SESSION['notif']; unset($_SESSION['notif']); ?>
            </div>
            <button onclick="closeNotif()" class="ml-2 text-green-400 hover:text-green-700 focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <script>
        function closeNotif() {
            var notif = document.getElementById('notifBox');
            if (notif) {
                notif.classList.remove('notif-animate-in');
                notif.classList.add('notif-animate-out');
                setTimeout(function(){ notif.remove(); }, 500);
            }
        }
        setTimeout(closeNotif, 3000);
        </script>
        <?php endif; ?>

        <!-- Quick Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <?php 
            $total_news = count($news);
            $published_news = count(array_filter($news, function($item) { return $item['status'] == 'published'; }));
            $draft_news = count(array_filter($news, function($item) { return $item['status'] == 'draft'; }));
            $archived_news = count(array_filter($news, function($item) { return $item['status'] == 'archived'; }));
            ?>
            <!-- Total News -->
            <div class="bg-blue-500 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Total News</h3>
                    <i class="fas fa-newspaper text-2xl opacity-75"></i>
                </div>
                <div class="text-3xl font-bold"><?php echo $total_news; ?></div>
                <div class="text-blue-100 text-sm mt-2">Total news articles</div>
            </div>

            <!-- Published News -->
            <div class="bg-green-500 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Published</h3>
                    <i class="fas fa-check-circle text-2xl opacity-75"></i>
                </div>
                <div class="text-3xl font-bold"><?php echo $published_news; ?></div>
                <div class="text-green-100 text-sm mt-2">Published articles</div>
            </div>

            <!-- Draft News -->
            <div class="bg-yellow-500 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Drafts</h3>
                    <i class="fas fa-pencil-alt text-2xl opacity-75"></i>
                </div>
                <div class="text-3xl font-bold"><?php echo $draft_news; ?></div>
                <div class="text-yellow-100 text-sm mt-2">Draft articles</div>
            </div>

            <!-- Add New -->
            <div class="bg-primary hover:bg-primary/90 rounded-lg shadow-lg p-6 text-white cursor-pointer transition-all duration-300" onclick="document.getElementById('addNewModal').classList.remove('hidden')">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Add New</h3>
                    <i class="fas fa-plus-circle text-2xl opacity-75"></i>
                </div>
                <div class="text-xl font-bold">Create News</div>
                <div class="text-white/90 text-sm mt-2">Click to add article</div>
            </div>
        </div>

        <!-- News Table -->
        <div class="bg-white rounded-lg shadow-lg">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">News Management</h2>
                <p class="text-sm text-gray-600 mt-1">View and manage all news articles</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Title</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Content</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Date</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($news as $item): ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900"><?php echo htmlspecialchars($item['title']); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-600 truncate max-w-md"><?php echo htmlspecialchars($item['content']); ?></div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-sm text-gray-600"><?php echo date('d M Y', strtotime($item['publish_date'])); ?></div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo $item['status'] == 'published' ? 'bg-green-100 text-green-800' : 
                                           ($item['status'] == 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'); ?>">
                                    <?php echo ucfirst($item['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button onclick="editNews(<?php echo htmlspecialchars(json_encode($item)); ?>)" 
                                        class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this article?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Add/Edit Modal -->
    <div id="addNewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-2xl mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900">Add New Article</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="id" value="">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                    <textarea name="content" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Publish Date</label>
                        <input type="date" name="publish_date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" id="submitNewsBtn" class="px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg">
                        Add Article
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Modal handling
    function closeModal() {
        document.getElementById('addNewModal').classList.add('hidden');
        // Reset form to add mode
        const form = document.querySelector('#addNewModal form');
        if (form) {
            form.reset();
            form.querySelector('[name="action"]').value = 'add';
            form.querySelector('[name="id"]').value = '';
            document.getElementById('submitNewsBtn').textContent = 'Add Article';
        }
    }

    function editNews(news) {
    const form = document.querySelector('#addNewModal form');
    form.querySelector('[name="action"]').value = 'update';
    form.querySelector('[name="id"]').value = news.id;
    form.querySelector('[name="title"]').value = news.title;
    form.querySelector('[name="content"]').value = news.content;
    form.querySelector('[name="publish_date"]').value = news.publish_date;
    form.querySelector('[name="status"]').value = news.status;
    document.getElementById('submitNewsBtn').textContent = 'Update Article';
    document.getElementById('addNewModal').classList.remove('hidden');
    }
    </script>
</body>
</html>