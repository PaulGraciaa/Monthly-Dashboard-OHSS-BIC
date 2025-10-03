<?php
require_once '../auth.php';
require_once '../../config/database.php';
requireAdminLogin();

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES);
}

// CRUD logic for gallery
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT photo_path FROM security_gallery WHERE id = ?");
    $stmt->execute(array($id));
    $photo = $stmt->fetch();
    if ($photo && file_exists($photo['photo_path'])) {
        unlink($photo['photo_path']);
    }
    $stmt = $pdo->prepare("DELETE FROM security_gallery WHERE id = ?");
    $stmt->execute(array($id));
    header("Location: gallery.php?success=deleted");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title = sanitize($_POST['title']);
    $photo_path = '';
    if (isset($_FILES['photo_file']) && $_FILES['photo_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/security/';
        if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
        $filename = uniqid('gallery_', true) . '_' . basename($_FILES['photo_file']['name']);
        $targetPath = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['photo_file']['tmp_name'], $targetPath)) {
            $photo_path = 'uploads/security/' . $filename;
        }
    } else if (!empty($_POST['photo_path'])) {
        $photo_path = sanitize($_POST['photo_path']);
    }
    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE security_gallery SET title=?, photo_path=? WHERE id=?");
        $stmt->execute(array($title, $photo_path, $id));
    } else {
        $stmt = $pdo->prepare("INSERT INTO security_gallery (title, photo_path) VALUES (?, ?)");
        $stmt->execute(array($title, $photo_path));
    }
    header("Location: gallery.php?success=saved");
    exit();
}

$gallery = $pdo->query("SELECT * FROM security_gallery ORDER BY id DESC")->fetchAll();
$page_title = 'Security Gallery';
require_once 'template_header.php';

// page content wrapper begins after header's container
?>
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Galeri Foto Security</h2>
                    <p class="text-gray-600 mt-1">Manage and organize security-related photos</p>
                </div>
                <button type="button" onclick="openModal('create', null)" 
                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-all duration-200 
                    shadow-lg hover:shadow-blue-500/20 flex items-center gap-2 transform hover:-translate-y-0.5">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Foto</span>
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($gallery as $item): ?>
                <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 group relative hover-zoom">
                    <div class="relative h-72 flex items-center justify-center bg-gray-100 overflow-hidden">
                    <img src="<?php echo '../' . htmlspecialchars($item['photo_path'], ENT_QUOTES); ?>" 
                        alt="<?php echo htmlspecialchars($item['title'], ENT_QUOTES); ?>" 
                        class="w-full h-full object-cover transition-all duration-500">
                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <div class="flex gap-3 scale-in">
                                <button onclick="openModal('edit', <?php echo htmlspecialchars(json_encode($item), ENT_QUOTES); ?>)" 
                                    class="bg-blue-500 text-white p-3 rounded-lg hover:bg-blue-600 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="openModal('delete', <?php echo $item['id']; ?>)" 
                                    class="bg-red-500 text-white p-3 rounded-lg hover:bg-red-600 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-semibold text-gray-800 text-lg mb-1 truncate">
                            <?php echo htmlspecialchars($item['title'], ENT_QUOTES); ?>
                        </h3>
                        <p class="text-gray-500 text-sm">Click to manage this photo</p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <!-- Modal -->
            <div id="galleryModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 relative scale-95 transition-transform duration-300">
                    <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                    <form id="galleryForm" method="post" enctype="multipart/form-data" class="space-y-6">
                        <div class="text-center mb-6">
                            <h3 class="text-2xl font-bold text-gray-800" id="modalTitle">Add New Photo</h3>
                            <p class="text-gray-600 mt-1" id="modalSubtitle">Fill in the details below</p>
                        </div>
                        <input type="hidden" name="id" id="modalId">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Judul</label>
                                <input type="text" name="title" id="modalTitle" 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" 
                                    required placeholder="Enter photo title">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Foto</label>
                                <div id="dropArea" class="border-2 border-dashed border-blue-400 rounded-lg p-4 text-center cursor-pointer bg-blue-50 hover:bg-blue-100 transition">
                                    <input type="file" name="photo_file" id="photoFileInput" accept="image/*" class="hidden">
                                    <span id="dropText">Drag & drop image here or click to select</span>
                                    <img id="previewImg" src="" alt="Preview" class="mx-auto mt-4 rounded-lg shadow max-h-40 hidden" />
                                </div>
                                <input type="hidden" name="photo_path" id="modalPhotoPath">
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 pt-6" id="modalActions">
                            <button type="button" onclick="closeModal()" 
                                class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200">
                                Cancel
                            </button>
                            <button type="submit" id="modalSaveBtn" 
                                class="bg-blue-600 text-white px-6 py-2.5 rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center gap-2">
                                <i class="fas fa-save"></i>
                                <span>Save Changes</span>
                            </button>
                            <button type="button" id="modalDeleteBtn" 
                                class="hidden flex items-center gap-2">
                                <i class="fas fa-trash"></i>
                                <span>Delete</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <script>
            function openModal(type, data) {
                var modal = document.getElementById('galleryModal');
                var form = document.getElementById('galleryForm');
                var saveBtn = document.getElementById('modalSaveBtn');
                var deleteBtn = document.getElementById('modalDeleteBtn');
                var modalTitle = document.querySelector('#modalTitle');
                var modalSubtitle = document.querySelector('#modalSubtitle');

                // Show modal with animation
                modal.classList.remove('pointer-events-none');
                modal.classList.add('opacity-100');
                modal.querySelector('.bg-white').classList.remove('scale-95');
                modal.querySelector('.bg-white').classList.add('scale-100');

                if (type === 'create') {
                    form.reset();
                    document.getElementById('modalId').value = '';
                    modalTitle.textContent = 'Add New Photo';
                    modalSubtitle.textContent = 'Fill in the details below';
                    saveBtn.innerHTML = '<i class="fas fa-save"></i><span>Save</span>';
                    deleteBtn.classList.add('hidden');
                } else if (type === 'edit') {
                    document.getElementById('modalId').value = data.id;
                    document.getElementById('modalTitle').value = data.title;
                    document.getElementById('modalPhotoPath').value = data.photo_path;
                    modalTitle.textContent = 'Edit Photo';
                    modalSubtitle.textContent = 'Update photo details';
                    saveBtn.innerHTML = '<i class="fas fa-save"></i><span>Update</span>';
                    deleteBtn.classList.add('hidden');
                } else if (type === 'delete') {
                    form.reset();
                    document.getElementById('modalId').value = data;
                    modalTitle.textContent = 'Confirm Delete';
                    modalSubtitle.textContent = 'This action cannot be undone';
                    
                    // Hide form fields for delete confirmation
                    form.querySelector('.space-y-4').style.display = 'none';
                    
                    // Show warning icon and message
                    const warningHtml = `
                        <div class="text-center py-6">
                            <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-full bg-red-100 mb-4">
                                <i class="fas fa-exclamation-triangle text-2xl text-red-500"></i>
                            </div>
                            <p class="text-gray-600 mb-6">Are you sure you want to delete this photo? This action is permanent and cannot be reversed.</p>
                        </div>
                    `;
                    form.querySelector('.space-y-4').insertAdjacentHTML('afterend', warningHtml);
                    
                    saveBtn.classList.add('hidden');
                    deleteBtn.classList.remove('hidden');
                    deleteBtn.innerHTML = '<i class="fas fa-trash"></i><span>Yes, Delete Photo</span>';
                    deleteBtn.className = 'bg-red-500 text-white px-6 py-2.5 rounded-lg hover:bg-red-600 transition-all duration-200 flex items-center gap-2';
                    
                    deleteBtn.onclick = function() {
                        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Deleting...</span>';
                        this.disabled = true;
                        this.classList.add('opacity-75', 'cursor-not-allowed');
                        setTimeout(() => {
                            window.location.href = 'gallery.php?delete=' + data;
                        }, 500); // Small delay for better UX feedback
                    };
                }
            }
            function closeModal() {
                var modal = document.getElementById('galleryModal');
                var saveBtn = document.getElementById('modalSaveBtn');
                var form = document.getElementById('galleryForm');
                
                // Hide modal with animation
                modal.classList.add('pointer-events-none');
                modal.classList.remove('opacity-100');
                modal.querySelector('.bg-white').classList.add('scale-95');
                modal.querySelector('.bg-white').classList.remove('scale-100');
                
                // Reset form visibility
                form.querySelector('.space-y-4').style.display = '';
                const warningElement = form.querySelector('.text-center.py-6');
                if (warningElement) {
                    warningElement.remove();
                }
                
                saveBtn.classList.remove('hidden');
                
                // Clear any temporary styles or classes
                setTimeout(() => {
                    form.reset();
                    const modalActions = document.getElementById('modalActions');
                    modalActions.querySelector('#modalDeleteBtn').className = 'hidden flex items-center gap-2';
                }, 300);
            }
            // Drag & Drop JS
            const dropArea = document.getElementById('dropArea');
            const photoInput = document.getElementById('photoFileInput');
            const previewImg = document.getElementById('previewImg');
            const dropText = document.getElementById('dropText');

            dropArea.addEventListener('click', () => photoInput.click());
            dropArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropArea.classList.add('bg-blue-100');
            });
            dropArea.addEventListener('dragleave', () => {
                dropArea.classList.remove('bg-blue-100');
            });
            dropArea.addEventListener('drop', (e) => {
                e.preventDefault();
                dropArea.classList.remove('bg-blue-100');
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
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewImg.classList.remove('hidden');
                    dropText.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
            </script>
        </div>
    </div>
</body>
</html>
