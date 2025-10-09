<?php
session_start(); // Mulai sesi di baris pertama
require_once '../auth.php';
require_once '../../config/database.php';
requireAdminLogin();

// Fungsi sanitize tidak perlu didefinisikan ulang, karena sudah ada di template_header.php

// Logika CRUD untuk personel
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'save') {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $position = sanitize($_POST['position']);
        $personnel_count = (int)$_POST['personnel_count'];
        $personnel_names = sanitize($_POST['personnel_names']);
        
        if ($id > 0) { // Update
            $stmt = $pdo->prepare("UPDATE security_personnel SET position=?, personnel_count=?, personnel_names=? WHERE id=?");
            $stmt->execute([$position, $personnel_count, $personnel_names, $id]);
            $_SESSION['notif'] = "Personnel data has been updated successfully.";
        } else { // Insert
            $stmt = $pdo->prepare("INSERT INTO security_personnel (position, personnel_count, personnel_names) VALUES (?, ?, ?)");
            $stmt->execute([$position, $personnel_count, $personnel_names]);
            $_SESSION['notif'] = "New personnel has been added successfully.";
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM security_personnel WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['notif'] = "Personnel has been deleted successfully.";
        }
    }
    
    header("Location: personnel.php");
    exit();
}

$personnel = $pdo->query("SELECT * FROM security_personnel ORDER BY display_order, id")->fetchAll();
$page_title = 'Security Personnel';
require_once 'template_header.php';
?>
    <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Security Personnel Data</h2>
                    <p class="text-gray-600 mt-1">Manage and organize security staff information</p>
                </div>
                <button onclick="openModal('create')" 
                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-all duration-200 
                    shadow-lg hover:shadow-blue-500/20 flex items-center gap-2 transform hover:-translate-y-0.5">
                    <i class="fas fa-plus"></i>
                    <span>Add Personnel</span>
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 text-sm font-medium">
                                <th class="px-6 py-4 text-left">No</th>
                                <th class="px-6 py-4 text-left">Position</th>
                                <th class="px-6 py-4 text-left">Count</th>
                                <th class="px-6 py-4 text-left">Details</th>
                                <th class="px-6 py-4 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($personnel as $index => $item): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-500"><?php echo $index + 1; ?></td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900"><?php echo sanitize($item['position']); ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?php echo $item['personnel_count']; ?> personnel
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500 max-w-xs">
                                        <?php echo nl2br(sanitize($item['personnel_names'])); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <button onclick="openModal('edit', <?php echo htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8'); ?>)" 
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <i class="fas fa-edit mr-1.5"></i>Edit
                                        </button>
                                        <button onclick="openModal('delete', <?php echo $item['id']; ?>)" 
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            <i class="fas fa-trash mr-1.5"></i>Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="personnelModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 relative scale-95 transition-transform duration-300 transform-gpu">
                    <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                    
                    <form id="personnelForm" method="post" class="space-y-6">
                        <input type="hidden" name="id" id="modalId">
                        <input type="hidden" name="action" id="modalAction">

                        <div id="formContent">
                            <div class="text-center mb-6">
                                <h3 class="text-2xl font-bold text-gray-800" id="modalTitle">Add New Personnel</h3>
                                <p class="text-gray-600 mt-1" id="modalSubtitle">Fill in the personnel details below</p>
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="modalPosition" class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                                    <input type="text" name="position" id="modalPosition" 
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" 
                                        required placeholder="Enter position title">
                                </div>
                                
                                <div>
                                    <label for="modalCount" class="block text-sm font-medium text-gray-700 mb-2">Personnel Count</label>
                                    <input type="number" name="personnel_count" id="modalCount" 
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" 
                                        required min="1" placeholder="Enter number of personnel">
                                </div>
                                
                                <div>
                                    <label for="modalNames" class="block text-sm font-medium text-gray-700 mb-2">Personnel Details</label>
                                    <textarea name="personnel_names" id="modalNames" rows="3" 
                                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" 
                                            required placeholder="Enter personnel names or details"></textarea>
                                </div>
                            </div>
                        </div>

                        <div id="deleteConfirmation" class="hidden text-center py-6">
                            <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-full bg-red-100 mb-4">
                                <i class="fas fa-exclamation-triangle text-2xl text-red-500"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Personnel</h3>
                            <p class="text-gray-500 mb-6">Are you sure you want to delete this personnel? This action cannot be undone.</p>
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
            const modal = document.getElementById('personnelModal');
            const form = document.getElementById('personnelForm');
            const formContent = document.getElementById('formContent');
            const deleteConfirmation = document.getElementById('deleteConfirmation');
            const modalTitle = document.getElementById('modalTitle');
            const modalSubtitle = document.getElementById('modalSubtitle');
            const modalId = document.getElementById('modalId');
            const modalAction = document.getElementById('modalAction');
            const modalSaveBtn = document.getElementById('modalSaveBtn');
            const modalDeleteBtn = document.getElementById('modalDeleteBtn');
            const modalPosition = document.getElementById('modalPosition');
            const modalCount = document.getElementById('modalCount');
            const modalNames = document.getElementById('modalNames');

            function openModal(type, data) {
                // Reset to default state
                form.reset();
                formContent.classList.remove('hidden');
                deleteConfirmation.classList.add('hidden');
                modalSaveBtn.classList.remove('hidden');
                modalDeleteBtn.classList.add('hidden');

                if (type === 'create') {
                    modalAction.value = 'save';
                    modalId.value = '';
                    modalTitle.textContent = 'Add New Personnel';
                    modalSubtitle.textContent = 'Fill in the personnel details below';
                    modalSaveBtn.innerHTML = '<i class="fas fa-save"></i><span>Save</span>';
                } else if (type === 'edit') {
                    modalAction.value = 'save';
                    modalId.value = data.id;
                    modalPosition.value = data.position;
                    modalCount.value = data.personnel_count;
                    modalNames.value = data.personnel_names;
                    modalTitle.textContent = 'Edit Personnel';
                    modalSubtitle.textContent = 'Update personnel information';
                    modalSaveBtn.innerHTML = '<i class="fas fa-save"></i><span>Update</span>';
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
            
            form.addEventListener('submit', function() {
                const activeBtn = document.querySelector('button[type="submit"]:not(.hidden)');
                activeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Processing...</span>';
                activeBtn.disabled = true;
            });
            </script>
        </div>
    </div>
</body>
</html>