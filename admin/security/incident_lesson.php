<?php
session_start(); // Mulai sesi di baris pertama
require_once '../auth.php';
require_once '../../config/database.php';
requireAdminLogin();

// Logika CRUD untuk Insiden
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $title = sanitize($_POST['title']);
        $incident_date = sanitize($_POST['incident_date']);
        $incident_time = sanitize($_POST['incident_time']);
        $who_name = sanitize($_POST['who_name']);
        $who_npk = sanitize($_POST['who_npk']);
        $summary = sanitize($_POST['summary']);
        $result = sanitize($_POST['result']);
        $root_causes = sanitize($_POST['root_causes']);
        $key_takeaways = sanitize($_POST['key_takeaways']);
        $corrective_actions = sanitize($_POST['corrective_actions']);
        $map_image_path = sanitize($_POST['map_image_path']);
        $status = sanitize($_POST['status']);
        $created_by = $_SESSION['admin_id'] ?? null;

        $params = [$title, $incident_date, $incident_time, $who_name, $who_npk, $summary, $result, $root_causes, $key_takeaways, $corrective_actions, $map_image_path, $status];

        if ($id > 0) { // Update
            $sql = "UPDATE security_incidents SET title=?, incident_date=?, incident_time=?, who_name=?, who_npk=?, summary=?, result=?, root_causes=?, key_takeaways=?, corrective_actions=?, map_image_path=?, status=?, updated_at=NOW() WHERE id=?";
            $params[] = $id;
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $_SESSION['notif'] = "Incident report has been updated successfully.";
        } else { // Insert
            $sql = "INSERT INTO security_incidents (title, incident_date, incident_time, who_name, who_npk, summary, result, root_causes, key_takeaways, corrective_actions, map_image_path, status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params[] = $created_by;
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $_SESSION['notif'] = "New incident report has been added successfully.";
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM security_incidents WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['notif'] = "Incident report has been deleted successfully.";
        }
    }

    header("Location: incident_lesson.php");
    exit();
}

$incidents = $pdo->query("SELECT * FROM security_incidents ORDER BY incident_date DESC, id DESC")->fetchAll();
$page_title = 'Incident & Lessons';
require_once 'template_header.php';
?>
    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600">Incident & Accident Report</h2>
                    <p class="text-gray-600 mt-2">Manage and track all security incident reports in one place</p>
                </div>
            <button onclick="openModal('create')" 
                class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-xl 
                hover:from-blue-700 hover:to-blue-800 transition-all duration-300 
                shadow-lg hover:shadow-blue-500/30 flex items-center gap-2 transform hover:-translate-y-0.5 
                focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 active:scale-95">
                <i class="fas fa-plus-circle text-lg"></i>
                <span class="font-medium">Add Incident</span>
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-lg border border-gray-200/50 overflow-hidden">
            <div class="p-4">
                <table class="w-full divide-y divide-gray-200 border-collapse">
                    <thead>
                        <tr class="bg-gradient-to-b from-gray-50 to-gray-100">
                            <th class="px-4 py-3 text-left w-[120px] text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">Date</th>
                            <th class="px-4 py-3 text-left w-[200px] text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">Title</th>
                            <th class="px-4 py-3 text-left w-[350px] text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">Summary</th>
                            <th class="px-4 py-3 text-left w-[150px] text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">Reporter</th>
                            <th class="px-4 py-3 text-left w-[100px] text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">Status</th>
                            <th class="px-4 py-3 text-left w-[100px] text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($incidents as $row): ?>
                        <tr class="hover:bg-blue-50/30 transition-all duration-200 border-b border-gray-100 last:border-0">
                            <td class="px-4 py-4 text-sm text-gray-500">
                                <div class="font-medium text-gray-900"><?php echo date("d M Y", strtotime($row['incident_date'])); ?></div>
                                <div class="text-xs text-gray-400 mt-0.5"><?php echo date("H:i", strtotime($row['incident_time'])); ?></div>
                            </td>
                            <td class="px-4 py-4 text-sm">
                                <div class="font-medium text-gray-900 leading-relaxed"><?php echo sanitize($row['title']); ?></div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500">
                                <div class="leading-relaxed whitespace-normal"><?php echo sanitize($row['summary']); ?></div>
                            </td>
                            <td class="px-4 py-4 text-sm">
                                <div class="font-medium text-gray-900"><?php echo sanitize($row['who_name']); ?></div>
                                <?php if($row['who_npk']): ?>
                                <div class="text-xs text-gray-500 mt-0.5">NPK: <?php echo sanitize($row['who_npk']); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-flex items-center justify-center px-3 py-1 text-xs font-medium rounded-full ring-1 ring-inset min-w-[90px] <?php 
                                    echo $row['status'] == 'published' ? 'bg-green-50 text-green-700 ring-green-600/20' : ($row['status'] == 'draft' ? 'bg-yellow-50 text-yellow-700 ring-yellow-600/20' : 'bg-gray-50 text-gray-700 ring-gray-500/20');
                                ?>">
                                    <?php echo ucfirst(sanitize($row['status'])); ?>
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <button onclick='openModal("edit", <?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, "UTF-8"); ?>)' 
                                        class="p-1.5 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick='openModal("delete", <?php echo $row["id"]; ?>)' 
                                        class="p-1.5 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                        <i class="fas fa-trash"></i>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="incidentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl relative scale-95 transition-transform duration-300 transform-gpu">
                <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors z-10">
                    <i class="fas fa-times text-xl"></i>
                </button>
                <form id="incidentForm" method="post" class="max-h-[90vh] overflow-y-auto">
                    <div class="p-8">
                        <input type="hidden" name="id" id="modalId">
                        <input type="hidden" name="action" id="modalAction">

                        <div id="formContent">
                            <div class="text-center mb-6">
                                <h3 class="text-2xl font-bold text-gray-800" id="modalTitle">Add New Incident</h3>
                                <p class="text-gray-600 mt-1" id="modalSubtitle">Fill in the incident details below</p>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                                    <input type="text" name="title" id="title" class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out px-3 py-2" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                                    <input type="date" name="incident_date" id="incident_date" class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out px-3 py-2" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Time</label>
                                    <input type="time" name="incident_time" id="incident_time" class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Reporter Name</label>
                                    <input type="text" name="who_name" id="who_name" class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Reporter NPK</label>
                                    <input type="text" name="who_npk" id="who_npk" class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out px-3 py-2">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Summary</label>
                                    <textarea name="summary" id="summary" rows="2" class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out p-3 resize-y"></textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Result</label>
                                    <textarea name="result" id="result" rows="2" class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out p-3 resize-y"></textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Root Causes</label>
                                    <textarea name="root_causes" id="root_causes" rows="2" class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out p-3 resize-y"></textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Key Takeaways</label>
                                    <textarea name="key_takeaways" id="key_takeaways" rows="2" class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out p-3 resize-y"></textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Corrective Actions</label>
                                    <textarea name="corrective_actions" id="corrective_actions" rows="2" class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out p-3 resize-y"></textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Map Image</label>
                                    <div id="dropzone" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-all duration-300 ease-in-out">
                                        <div class="space-y-1 text-center">
                                            <div id="preview-container" class="hidden mb-3">
                                                <img id="image-preview" src="" alt="Preview" class="mx-auto h-32 object-cover rounded-lg shadow-sm">
                                            </div>
                                            <div id="upload-icon" class="mx-auto h-12 w-12 text-gray-400">
                                                <i class="fas fa-cloud-upload-alt text-3xl"></i>
                                            </div>
                                            <div class="flex flex-col text-sm text-gray-600">
                                                <label for="file-upload" class="relative cursor-pointer rounded-md font-medium text-blue-600 hover:text-blue-500">
                                                    <span>Upload a file</span>
                                                    <input id="file-upload" name="map_image" type="file" class="sr-only" accept="image/*">
                                                </label>
                                                <p class="text-xs text-gray-500">PNG, JPG up to 5MB</p>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="map_image_path" id="map_image_path">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select name="status" id="status" class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out px-3 py-2">
                                        <option value="draft">Draft</option>
                                        <option value="published">Published</option>
                                        <option value="archived">Archived</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="deleteConfirmation" class="hidden text-center py-6">
                             <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-full bg-red-100 mb-4">
                                <i class="fas fa-exclamation-triangle text-2xl text-red-500"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Incident Report</h3>
                            <p class="text-gray-500 mb-6">Are you sure you want to delete this report? This action cannot be undone.</p>
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-3 p-6 bg-gray-50 border-t border-gray-200 rounded-b-2xl">
                        <button type="button" onclick="closeModal()" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 focus:ring-2 focus:ring-gray-200 active:bg-gray-200 transition duration-150 ease-in-out">Cancel</button>
                        <button type="submit" id="modalSaveBtn" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 active:bg-blue-800 transition duration-150 ease-in-out flex items-center gap-2 font-medium">
                            <i class="fas fa-save"></i><span>Save Changes</span>
                        </button>
                        <button type="submit" id="modalDeleteBtn" class="hidden bg-red-600 text-white px-6 py-2.5 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 active:bg-red-800 transition duration-150 ease-in-out flex items-center gap-2 font-medium">
                            <i class="fas fa-trash"></i><span>Yes, Delete</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            const modal = document.getElementById('incidentModal');
            const form = document.getElementById('incidentForm');
            const formContent = document.getElementById('formContent');
            const deleteConfirmation = document.getElementById('deleteConfirmation');
            const modalTitle = document.getElementById('modalTitle');
            const modalSubtitle = document.getElementById('modalSubtitle');
            const modalAction = document.getElementById('modalAction');
            const modalId = document.getElementById('modalId');
            const modalSaveBtn = document.getElementById('modalSaveBtn');
            const modalDeleteBtn = document.getElementById('modalDeleteBtn');

            function openModal(type, data) {
                form.reset();
                formContent.classList.remove('hidden');
                deleteConfirmation.classList.add('hidden');
                modalSaveBtn.classList.remove('hidden');
                modalDeleteBtn.classList.add('hidden');
                
                if (type === 'create') {
                    modalAction.value = 'save';
                    modalId.value = '';
                    modalTitle.textContent = 'Add New Incident';
                    modalSubtitle.textContent = 'Fill in the incident details below';
                    modalSaveBtn.innerHTML = '<i class="fas fa-save"></i><span>Save</span>';
                } else if (type === 'edit') {
                    modalAction.value = 'save';
                    modalTitle.textContent = 'Edit Incident Report';
                    modalSubtitle.textContent = 'Update incident information';
                    modalSaveBtn.innerHTML = '<i class="fas fa-save"></i><span>Update</span>';

                    // Populate form fields
                    modalId.value = data.id;
                    Object.keys(data).forEach(key => {
                        const field = form.querySelector(`[name="${key}"]`);
                        if (field) {
                            field.value = data[key];
                        }
                    });
                } else if (type === 'delete') {
                    modalAction.value = 'delete';
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
            }

            // Handle drag and drop file upload
            const dropzone = document.getElementById('dropzone');
            const fileInput = document.getElementById('file-upload');
            const imagePreview = document.getElementById('image-preview');
            const previewContainer = document.getElementById('preview-container');
            const uploadIcon = document.getElementById('upload-icon');
            const mapImagePath = document.getElementById('map_image_path');

            // Prevent default drag behaviors
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, preventDefaults, false);
                document.body.addEventListener(eventName, preventDefaults, false);
            });

            // Highlight dropzone when dragging over it
            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, highlight, false);
            });
            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, unhighlight, false);
            });

            // Handle dropped files
            dropzone.addEventListener('drop', handleDrop, false);
            fileInput.addEventListener('change', handleFiles, false);

            function preventDefaults (e) {
                e.preventDefault();
                e.stopPropagation();
            }

            function highlight(e) {
                dropzone.classList.add('border-blue-500', 'bg-blue-50/50');
            }

            function unhighlight(e) {
                dropzone.classList.remove('border-blue-500', 'bg-blue-50/50');
            }

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                handleFiles({target: {files: files}});
            }

            function handleFiles(e) {
                const files = e.target.files;
                if (files.length > 0) {
                    const file = files[0];
                    if (file.size > 5 * 1024 * 1024) { // 5MB limit
                        alert('File is too large. Maximum size is 5MB.');
                        return;
                    }
                    if (!file.type.startsWith('image/')) {
                        alert('Please upload an image file.');
                        return;
                    }

                    // Preview image
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        previewContainer.classList.remove('hidden');
                        uploadIcon.classList.add('hidden');
                    }
                    reader.readAsDataURL(file);

                    // Upload file
                    uploadFile(file);
                }
            }

            function uploadFile(file) {
                const formData = new FormData();
                formData.append('file', file);

                fetch('upload_map.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mapImagePath.value = data.path;
                    } else {
                        alert('Upload failed: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Upload failed. Please try again.');
                });
            }

            // Form submit handler
            form.addEventListener('submit', function() {
                const activeBtn = this.querySelector('button[type="submit"]:not(.hidden)');
                activeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Processing...</span>';
                activeBtn.disabled = true;
            });
        </script>
    </div>
</body>
</html>