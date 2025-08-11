<?php
require_once '../auth.php';
require_once '../../config/database.php';
requireAdminLogin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $title = sanitize($_POST['title']);
        $content = sanitize($_POST['content']);
        $publish_date = $_POST['publish_date'];
        $status = $_POST['status'];
        
        $stmt = $pdo->prepare("INSERT INTO news (title, content, publish_date, status, created_by) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$title, $content, $publish_date, $status, $_SESSION['admin_id']])) {
            $message = 'News berhasil ditambahkan!';
        }
    }
}

$news = $pdo->query("SELECT * FROM news ORDER BY publish_date DESC")->fetchAll();
?>

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
