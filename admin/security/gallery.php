<?php
session_start();
require_once '../auth.php';
require_once '../../config/database.php';
requireAdminLogin();

// Fungsi sanitasi
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// CRUD untuk gallery
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($action === 'save') {
        $title = sanitize($_POST['title'] ?? '');
        $photo_path = sanitize($_POST['photo_path'] ?? '');

        // Upload foto baru jika ada
        if (isset($_FILES['photo_file']) && $_FILES['photo_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../../uploads/security/';
            if (!is_dir($uploadDir)) { 
                mkdir($uploadDir, 0777, true); 
            }
            
            // Validasi tipe file
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            $fileType = mime_content_type($_FILES['photo_file']['tmp_name']);
            
            if (!in_array($fileType, $allowedTypes)) {
                $_SESSION['error'] = "Invalid file type. Only JPG, PNG and GIF are allowed.";
                header("Location: gallery.php");
                exit();
            }

            $filename = uniqid('gallery_', true) . '_' . basename($_FILES['photo_file']['name']);
            $targetPath = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['photo_file']['tmp_name'], $targetPath)) {
                $photo_path = 'uploads/security/' . $filename;
            } else {
                $_SESSION['error'] = "Failed to upload file.";
                header("Location: gallery.php");
                exit();
            }
        }

        if ($id > 0) { // Update
            $stmt = $pdo->prepare("UPDATE security_gallery SET title=?, photo_path=? WHERE id=?");
            $stmt->execute([$title, $photo_path, $id]);
            $_SESSION['notif'] = "Photo has been updated successfully.";
        } else { // Add
            $stmt = $pdo->prepare("INSERT INTO security_gallery (title, photo_path) VALUES (?, ?)");
            $stmt->execute([$title, $photo_path]);
            $_SESSION['notif'] = "New photo has been added successfully.";
        }
    } elseif ($action === 'delete' && $id > 0) {
        // Hapus file fisik
        $stmt = $pdo->prepare("SELECT photo_path FROM security_gallery WHERE id = ?");
        $stmt->execute([$id]);
        $photo = $stmt->fetch();
        if ($photo && !empty($photo['photo_path']) && file_exists('../../' . $photo['photo_path'])) {
            unlink('../../' . $photo['photo_path']);
        }
        // Hapus record dari database
        $stmt = $pdo->prepare("DELETE FROM security_gallery WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['notif'] = "Photo has been deleted successfully.";
    }
    
    header("Location: gallery.php");
    exit();
}

$gallery = $pdo->query("SELECT * FROM security_gallery ORDER BY id DESC")->fetchAll();
$page_title = 'Security Gallery';
require_once 'template_header.php';
?>
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Security Photo Gallery</h2>
                    <p class="text-gray-600 mt-1">Manage and organize security-related photos</p>
                </div>
                <button type="button" onclick="openModal('create', null)" 
                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-all duration-200 
                    shadow-lg hover:shadow-blue-500/20 flex items-center gap-2 transform hover:-translate-y-0.5">
                    <i class="fas fa-plus"></i>
                    <span>Add Photo</span>
                </button>
            </div>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 flex justify-between items-center">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span><?php echo $_SESSION['error']; ?></span>
                    </div>
                    <button type="button" onclick="this.parentElement.style.display='none'">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($gallery as $item): ?>
                <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 group relative">
                    <div class="relative h-72 flex items-center justify-center bg-gray-100 overflow-hidden">
                        <img src="<?php echo '../../' . sanitize($item['photo_path']); ?>"
                             alt="<?php echo sanitize($item['title']); ?>"
                             class="w-full h-full object-cover transition-all duration-500 group-hover:scale-105"
                             onerror="this.onerror=null;this.src='../../img/no-image.png';">
                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <div class="flex gap-3">
                                <button onclick="openModal('edit', <?php echo htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8'); ?>)" 
                                    class="bg-blue-500 text-white w-12 h-12 rounded-lg hover:bg-blue-600 transition-colors flex items-center justify-center">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="openModal('delete', <?php echo $item['id']; ?>)" 
                                    class="bg-red-500 text-white w-12 h-12 rounded-lg hover:bg-red-600 transition-colors flex items-center justify-center">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-semibold text-gray-800 text-lg mb-1 truncate">
                            <?php echo sanitize($item['title']); ?>
                        </h3>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (empty($gallery)): ?>
                <div class="text-center py-12">
                    <i class="fas fa-images text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-500">No photos yet</h3>
                    <p class="text-gray-400 mt-2">Add your first photo to get started</p>
                </div>
            <?php endif; ?>
            
            <div id="galleryModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 relative scale-95 transition-transform duration-300 transform-gpu">
                    <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                    <form id="galleryForm" method="post" enctype="multipart/form-data" class="space-y-6">
                        <input type="hidden" name="id" id="modalId">
                        <input type="hidden" name="action" id="modalAction">
                        <input type="hidden" name="photo_path" id="modalPhotoPath">

                        <div id="formContent">
                             <div class="text-center mb-6">
                                <h3 class="text-2xl font-bold text-gray-800" id="modalTitleHeader">Add New Photo</h3>
                                <p class="text-gray-600 mt-1" id="modalSubtitle">Fill in the details below</p>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label for="modalTitleInput" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                                    <input type="text" name="title" id="modalTitleInput" 
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" 
                                        required placeholder="Enter photo title">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Photo</label>
                                    <div id="dropArea" class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer bg-gray-50 hover:bg-gray-100 transition hover:border-blue-500">
                                        <input type="file" name="photo_file" id="photoFileInput" accept="image/*" class="hidden">
                                        <div id="dropText">
                                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400"></i>
                                            <p class="mt-2 text-sm text-gray-600">Drag & drop image here or click to select</p>
                                        </div>
                                        <img id="previewImg" src="" alt="Preview" class="mx-auto mt-2 rounded-lg shadow-sm max-h-40 hidden" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="deleteConfirmation" class="hidden text-center py-6">
                            <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-full bg-red-100 mb-4">
                                <i class="fas fa-exclamation-triangle text-2xl text-red-500"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Photo</h3>
                            <p class="text-gray-500 mb-6">Are you sure you want to delete this photo? This action cannot be undone.</p>
                        </div>

                        <div class="flex justify-end gap-3 pt-6">
                            <button type="button" onclick="closeModal()" 
                                class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200">
                                Cancel
                            </button>
                            <button type="submit" id="modalSaveBtn" 
                                class="bg-blue-600 text-white px-6 py-2.5 rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center gap-2">
                                <i class="fas fa-save"></i>
                                <span>Save Changes</span>
                            </button>
                            <button type="submit" id="modalDeleteBtn" class="hidden bg-red-600 text-white px-6 py-2.5 rounded-lg hover:bg-red-700 transition-all duration-200 flex items-center gap-2">
                                <i class="fas fa-trash"></i>
                                <span>Yes, Delete</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <script>
            const modal = document.getElementById('galleryModal');
            const form = document.getElementById('galleryForm');
            const formContent = document.getElementById('formContent');
            const deleteConfirmation = document.getElementById('deleteConfirmation');
            const modalTitleHeader = document.getElementById('modalTitleHeader');
            const modalSubtitle = document.getElementById('modalSubtitle');
            const modalId = document.getElementById('modalId');
            const modalAction = document.getElementById('modalAction');
            const modalSaveBtn = document.getElementById('modalSaveBtn');
            const modalDeleteBtn = document.getElementById('modalDeleteBtn');
            const modalTitleInput = document.getElementById('modalTitleInput');
            const modalPhotoPath = document.getElementById('modalPhotoPath');
            const previewImg = document.getElementById('previewImg');
            const dropText = document.getElementById('dropText');
            const photoInput = document.getElementById('photoFileInput');
            
            function openModal(type, data) {
                // Helper: enable/disable all form fields
                function setFormFieldsDisabled(disabled) {
                    const fields = form.querySelectorAll('input, textarea, select');
                    fields.forEach(f => {
                        if (f.type !== 'hidden') f.disabled = !!disabled;
                    });
                }

                form.reset();
                formContent.classList.remove('hidden');
                deleteConfirmation.classList.add('hidden');
                modalSaveBtn.classList.remove('hidden');
                modalDeleteBtn.classList.add('hidden');
                previewImg.src = '';
                previewImg.classList.add('hidden');
                dropText.classList.remove('hidden');

                if (type === 'create') {
                    setFormFieldsDisabled(false);
                    modalAction.value = 'save';
                    modalId.value = '';
                    modalPhotoPath.value = '';
                    modalTitleHeader.textContent = 'Add New Photo';
                    modalSubtitle.textContent = 'Fill in the details below';
                    modalSaveBtn.innerHTML = '<i class="fas fa-save"></i><span>Save</span>';
                } else if (type === 'edit') {
                    setFormFieldsDisabled(false);
                    modalAction.value = 'save';
                    modalId.value = data.id;
                    modalTitleInput.value = data.title;
                    modalPhotoPath.value = data.photo_path;
                    modalTitleHeader.textContent = 'Edit Photo';
                    modalSubtitle.textContent = 'Update photo details';
                    modalSaveBtn.innerHTML = '<i class="fas fa-save"></i><span>Update</span>';
                    
                    if (data.photo_path) {
                        previewImg.src = '../../' + data.photo_path;
                        previewImg.classList.remove('hidden');
                        dropText.classList.add('hidden');
                    }
                } else if (type === 'delete') {
                    setFormFieldsDisabled(true);
                    modalAction.value = 'delete';
                    modalId.disabled = false; // id harus tetap enabled agar terkirim
                    modalId.value = data;
                    formContent.classList.add('hidden');
                    deleteConfirmation.classList.remove('hidden');
                    modalSaveBtn.classList.add('hidden');
                    modalDeleteBtn.classList.remove('hidden');
                }
                
                modal.classList.remove('pointer-events-none', 'opacity-0');
                modal.querySelector('.bg-white').classList.remove('scale-95');
            }

            function closeModal() {
                modal.classList.add('pointer-events-none', 'opacity-0');
                modal.querySelector('.bg-white').classList.add('scale-95');
                // Enable all fields again
                const fields = form.querySelectorAll('input, textarea, select');
                fields.forEach(f => f.disabled = false);
            }
            
            form.addEventListener('submit', function() {
                const activeBtn = document.querySelector('button[type="submit"]:not(.hidden)');
                activeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Processing...</span>';
                activeBtn.disabled = true;
            });

            // Drag & Drop JS
            const dropArea = document.getElementById('dropArea');
            dropArea.addEventListener('click', () => photoInput.click());
            
            ['dragover', 'dragenter'].forEach(event => {
                dropArea.addEventListener(event, (e) => {
                    e.preventDefault();
                    dropArea.classList.add('border-blue-500');
                });
            });
            
            ['dragleave', 'dragend', 'drop'].forEach(event => {
                dropArea.addEventListener(event, (e) => {
                    e.preventDefault();
                    dropArea.classList.remove('border-blue-500');
                });
            });
            
            dropArea.addEventListener('drop', (e) => {
                if (e.dataTransfer.files.length) {
                    photoInput.files = e.dataTransfer.files;
                    showPreview(photoInput.files[0]);
                }
            });
            
            photoInput.addEventListener('change', () => {
                if (photoInput.files.length) {
                    showPreview(photoInput.files[0]);
                }
            });
            
            function showPreview(file) {
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        previewImg.classList.remove('hidden');
                        dropText.classList.add('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            }
            </script>
        </div>
    </div>
</body>
</html>