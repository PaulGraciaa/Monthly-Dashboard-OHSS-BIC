<?php
require_once '../auth.php';
require_once '../../config/database.php';
requireAdminLogin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $title = sanitize($_POST['title']);
        $description = sanitize($_POST['description']);
        $activity_date = $_POST['activity_date'];
        $image_path = 'img/default.jpg';
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = '../../img/';
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $file_name = 'activity_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_path = 'img/' . $file_name;
            }
        }
        
        $stmt = $pdo->prepare("INSERT INTO activities (title, description, activity_date, image_path, created_by) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$title, $description, $activity_date, $image_path, $_SESSION['admin_id']])) {
            $message = 'Activity berhasil ditambahkan!';
        }
    }
}

$activities = $pdo->query("SELECT * FROM activities ORDER BY activity_date DESC")->fetchAll();
?>

<?php if ($message): ?>
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
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
                <input type="text" name="title" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Activity Date</label>
                <input type="date" name="activity_date" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea name="description" rows="3" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
            <input type="file" name="image" accept="image/*" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
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
            <img src="../../<?php echo $activity['image_path']; ?>" alt="<?php echo $activity['title']; ?>" 
                 class="w-full h-48 object-cover">
            <div class="p-4">
                <h3 class="font-semibold text-gray-900 mb-2"><?php echo $activity['title']; ?></h3>
                <p class="text-gray-600 text-sm mb-2"><?php echo $activity['description']; ?></p>
                <p class="text-gray-500 text-xs mb-3"><?php echo date('d M Y', strtotime($activity['activity_date'])); ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
