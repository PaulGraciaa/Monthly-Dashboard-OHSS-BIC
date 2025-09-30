<?php
ob_start(); // Start output buffering
session_start();
require_once '../../config/database.php';
require_once 'template_header.php';

// Create upload directory if it doesn't exist
$uploadDir = __DIR__ . '/../../uploads/distribution';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
    error_log("Created directory: " . $uploadDir);
}

$page_title = 'Distribution Mapping';
$message = '';
$edit_data = null;

// Process all form submissions and redirects
if (isset($_SESSION['notif'])) {
    $message = $_SESSION['notif'];
    unset($_SESSION['notif']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    // Debug information
    error_log("POST request received with action: " . $_POST['action']);
    error_log("POST data: " . print_r($_POST, true));
    error_log("FILES data: " . print_r($_FILES, true));
    
    $title = $_POST['title'] ?? '';
    $image = isset($_FILES['image']) ? $_FILES['image'] : null;
    $imagePath = '';
    $uploadError = '';
    
    if ($image && $image['error'] == UPLOAD_ERR_OK) {
        $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
        $filename = 'distribution_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        $target = $uploadDir . '/' . $filename;
        
        error_log("Upload directory: " . $uploadDir);
        error_log("Target path: " . $target);
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            error_log("Creating directory: " . $uploadDir);
            mkdir($uploadDir, 0777, true);
        }
        
        if (move_uploaded_file($image['tmp_name'], $target)) {
            $imagePath = 'uploads/distribution/' . $filename;
            error_log("File uploaded successfully.");
            error_log("DB Image path: " . $imagePath);
            error_log("Full file path: " . $target);
            error_log("File exists check: " . (file_exists($target) ? "Yes" : "No"));
        } else {
            $uploadError = 'Failed to upload image. Error: ' . error_get_last()['message'];
            error_log("Upload failed: " . $uploadError);
            error_log("Attempted to upload to: " . $target);
        }
    } elseif ($image && $image['error'] != UPLOAD_ERR_NO_FILE) {
        $uploadError = 'Failed to upload image. Error code: ' . $image['error'];
        error_log("Upload error: " . $uploadError);
    }

    if ($_POST['action'] == 'create') {
        if ($uploadError) {
            $message = $uploadError;
        } else {
            error_log("About to insert - Title: " . $title . ", Image Path: " . $imagePath);
            $stmt = $pdo->prepare("INSERT INTO surveillance_distribution_map (title, image) VALUES (?, ?)");
            if ($stmt && $stmt->execute([$title, $imagePath])) {
                error_log("Insert successful - Image path saved: " . $imagePath);
                $_SESSION['notif'] = "Data added successfully!";
                ob_end_clean(); // Clear the output buffer
                header('Location: distribution.php');
                exit();
            } else {
                $message = "Failed to add data!";
            }
        }
    } elseif ($_POST['action'] == 'update') {
        $id = $_POST['id'] ?? '';
        if ($uploadError) {
            $message = $uploadError;
        } else {
            if ($imagePath) {
                $stmt = $pdo->prepare("UPDATE surveillance_distribution_map SET title = ?, image = ? WHERE id = ?");
                $success = $stmt && $stmt->execute([$title, $imagePath, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE surveillance_distribution_map SET title = ? WHERE id = ?");
                $success = $stmt && $stmt->execute([$title, $id]);
            }
            if ($success) {
                $_SESSION['notif'] = "Data updated successfully!";
                ob_end_clean(); // Clear the output buffer
                header('Location: distribution.php');
                exit();
            } else {
                $message = "Failed to update data!";
            }
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Get image path before deleting
    $stmt = $pdo->prepare("SELECT image FROM surveillance_distribution_map WHERE id = ?");
    $stmt->execute([$id]);
    $oldImage = $stmt->fetchColumn();
    
    // Delete the record
    $stmt = $pdo->prepare("DELETE FROM surveillance_distribution_map WHERE id = ?");
    if ($stmt->execute([$id])) {
        // Delete the image file if exists
        if ($oldImage && file_exists('../../' . $oldImage)) {
            unlink('../../' . $oldImage);
        }
        $_SESSION['notif'] = "Data deleted successfully!";
        ob_end_clean(); // Clear the output buffer
        header('Location: distribution.php');
        exit();
    }
}

// Get data
$stmt = $pdo->query("SELECT * FROM surveillance_distribution_map ORDER BY id DESC");
$data = $stmt->fetchAll();

// Get edit data
$editRow = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM surveillance_distribution_map WHERE id = ?");
    $stmt->execute([$id]);
    $editRow = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Distribution Surveillance Mapping</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css">
    <style>
        .dropzone {
            border: 2px dashed #ccc;
            border-radius: 4px;
            padding: 20px;
            text-align: center;
            background: #f8fafc;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .dropzone.dragover {
            background: #e2e8f0;
            border-color: #4a90e2;
        }
        .aspect-w-16 {
            position: relative;
        }
        .aspect-w-16::before {
            content: '';
            display: block;
            padding-top: 56.25%; /* 16:9 aspect ratio */
        }
        .aspect-w-16 > * {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen py-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Distribution Surveillance Mapping</h1>
        </div>

        <?php if ($message): ?>
            <div class="mb-4 rounded-lg <?= strpos($message, 'Failed') !== false ? 'bg-red-50' : 'bg-green-50' ?> p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 <?= strpos($message, 'Failed') !== false ? 'text-red-400' : 'text-green-400' ?>" viewBox="0 0 20 20" fill="currentColor">
                            <?php if (strpos($message, 'Failed') !== false): ?>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            <?php else: ?>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            <?php endif; ?>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium <?= strpos($message, 'Failed') !== false ? 'text-red-800' : 'text-green-800' ?>"><?= htmlspecialchars($message) ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <?php if ($editRow): ?>
                <h2 class="text-xl font-semibold mb-4">Edit Data</h2>
                <form method="post" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($editRow['id']) ?>">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" value="<?= htmlspecialchars($editRow['title']) ?>" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                        <div class="dropzone" id="dropzone">
                            <input type="file" name="image" id="fileInput" class="hidden" accept="image/*">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <label for="fileInput" class="relative cursor-pointer rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        Upload a file
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500" id="fileInfo">PNG, JPG up to 10MB</p>
                            </div>
                        </div>
                        <?php if (!empty($editRow['image'])): 
                            $currentImagePath = '../../' . $editRow['image'];
                            $fullImagePath = __DIR__ . '/../../' . $editRow['image'];
                        ?>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Current image:</p>
                                <?php if (file_exists($fullImagePath)): ?>
                                    <img src="<?= htmlspecialchars($currentImagePath) ?>" alt="Current image" class="mt-2 h-32 object-contain">
                                <?php else: ?>
                                    <p class="text-sm text-red-500 mt-2">Image file not found: <?= htmlspecialchars($editRow['image']) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        <a href="distribution.php" class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">Cancel</a>
                        <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">Update</button>
                    </div>
                </form>
            <?php else: ?>
                <h2 class="text-xl font-semibold mb-4">Add New Data</h2>
                <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" enctype="multipart/form-data" class="space-y-4" id="createForm">
                    <input type="hidden" name="action" value="create">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                        <div class="dropzone" id="dropzone">
                            <input type="file" name="image" id="fileInput" class="hidden" accept="image/*" required>
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <label for="fileInput" class="relative cursor-pointer rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        Upload a file
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500" id="fileInfo">PNG, JPG up to 10MB</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">Save</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>

        <div class="mt-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php 
                if (!empty($data)) {
                    foreach ($data as $row) { 
                        $imagePath = !empty($row['image']) ? __DIR__ . '/../../' . $row['image'] : '';
                        $displayPath = !empty($row['image']) ? '../../' . $row['image'] : '';
                        ?>
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                            <div class="aspect-w-16">
                                <?php if (!empty($row['image']) && file_exists($imagePath)): ?>
                                    <img src="<?= htmlspecialchars($displayPath) ?>" 
                                         alt="<?= htmlspecialchars($row['title']) ?>" 
                                         class="w-full h-48 object-contain">
                                <?php else: ?>
                                    <div class="flex items-center justify-center h-48 bg-gray-100">
                                        <div class="text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <p class="mt-2 text-xs text-gray-500">No image available</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="p-4">
                                <h3 class="text-lg font-medium text-gray-900 mb-2"><?= htmlspecialchars($row['title']) ?></h3>
                                <div class="flex justify-between items-center mt-4">
                                    <a href="distribution.php?edit=<?= $row['id'] ?>" 
                                       class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue active:bg-blue-700 transition ease-in-out duration-150">
                                        Edit
                                    </a>
                                    <a href="distribution.php?delete=<?= $row['id'] ?>" 
                                       onclick="return confirm('Are you sure you want to delete this item?')"
                                       class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-red-600 hover:bg-red-500 focus:outline-none focus:border-red-700 focus:shadow-outline-red active:bg-red-700 transition ease-in-out duration-150">
                                        Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php }
                } else { ?>
                    <div class="col-span-full">
                        <div class="text-center py-12 bg-white rounded-lg shadow">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No images available</p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <script>
        // Form submission handler
        document.getElementById('createForm')?.addEventListener('submit', function(e) {
            const fileInput = this.querySelector('input[type="file"]');
            const titleInput = this.querySelector('input[name="title"]');
            
            if (!titleInput.value.trim()) {
                e.preventDefault();
                alert('Please enter a title');
                return false;
            }
            
            if (!fileInput.files || fileInput.files.length === 0) {
                e.preventDefault();
                alert('Please select an image');
                return false;
            }
        });

        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('fileInput');
        const fileInfo = document.getElementById('fileInfo');

        if (dropzone && fileInput) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, unhighlight, false);
            });

            function highlight(e) {
                dropzone.classList.add('dragover');
            }

            function unhighlight(e) {
                dropzone.classList.remove('dragover');
            }

            dropzone.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                fileInput.files = files;
                
                if (files.length > 0) {
                    updateFileInfo(files[0]);
                }
            }

            dropzone.addEventListener('click', () => {
                fileInput.click();
            });

            fileInput.addEventListener('change', () => {
                if (fileInput.files.length > 0) {
                    updateFileInfo(fileInput.files[0]);
                }
            });

            function updateFileInfo(file) {
                const fileName = file.name;
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                if (fileInfo) {
                    fileInfo.textContent = `Selected: ${fileName} (${fileSize}MB)`;
                    fileInfo.className = 'text-xs text-green-500';
                }
            }
        }
    </script>
</body>
</html>
<?php ob_end_flush(); // Flush the output buffer and turn off output buffering ?>