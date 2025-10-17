<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../../config/database.php';
requireAdminLogin();

if (!function_exists('sanitize')) {
  function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES);
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = sanitize(isset($_POST['title']) ? $_POST['title'] : '');
  $incident_date = isset($_POST['incident_date']) ? $_POST['incident_date'] : date('Y-m-d');
  $incident_time = isset($_POST['incident_time']) ? $_POST['incident_time'] : NULL;
  $who_name = sanitize(isset($_POST['who_name']) ? $_POST['who_name'] : '');
  $who_npk = sanitize(isset($_POST['who_npk']) ? $_POST['who_npk'] : '');
  $summary = isset($_POST['summary']) ? $_POST['summary'] : '';
  $result = isset($_POST['result']) ? $_POST['result'] : '';
  $root_causes = isset($_POST['root_causes']) ? $_POST['root_causes'] : '';
  $key_takeaways = isset($_POST['key_takeaways']) ? $_POST['key_takeaways'] : '';
  $corrective_actions = isset($_POST['corrective_actions']) ? $_POST['corrective_actions'] : '';
  $status = isset($_POST['status']) ? $_POST['status'] : 'published';

  // Handle file upload
  $photo_image_path = '';
  $map_image_path = '';
  $upload_dir = __DIR__ . '/../../uploads/ohs/';
  if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }
  // Validasi file photo
  if (isset($_FILES['photo_image']) && $_FILES['photo_image']['error'] == 0) {
    $allowedTypes = array('image/jpeg', 'image/png', 'image/gif');
    $fileType = function_exists('mime_content_type') ? mime_content_type($_FILES['photo_image']['tmp_name']) : $_FILES['photo_image']['type'];
    $validType = false;
    foreach ($allowedTypes as $type) {
      if ($fileType == $type) { $validType = true; break; }
    }
    if ($validType && $_FILES['photo_image']['size'] <= 5242880) {
      $ext = pathinfo($_FILES['photo_image']['name'], PATHINFO_EXTENSION);
      $filename = 'evidence_' . time() . '_' . rand(1000,9999) . '.' . $ext;
      $path = $upload_dir . $filename;
      if (move_uploaded_file($_FILES['photo_image']['tmp_name'], $path)) {
        $photo_image_path = 'uploads/ohs/' . $filename;
      }
    }
  }
  // Validasi file map
  if (isset($_FILES['map_image']) && $_FILES['map_image']['error'] == 0) {
    $allowedTypes = array('image/jpeg', 'image/png', 'image/gif');
    $fileType = function_exists('mime_content_type') ? mime_content_type($_FILES['map_image']['tmp_name']) : $_FILES['map_image']['type'];
    $validType = false;
    foreach ($allowedTypes as $type) {
      if ($fileType == $type) { $validType = true; break; }
    }
    if ($validType && $_FILES['map_image']['size'] <= 5242880) {
      $ext = pathinfo($_FILES['map_image']['name'], PATHINFO_EXTENSION);
      $filename = 'map_' . time() . '_' . rand(1000,9999) . '.' . $ext;
      $path = $upload_dir . $filename;
      if (move_uploaded_file($_FILES['map_image']['tmp_name'], $path)) {
        $map_image_path = 'uploads/ohs/' . $filename;
      }
    }
  }

  $stmt = $pdo->prepare('INSERT INTO ohs_incidents (title, incident_date, incident_time, who_name, who_npk, summary, result, root_causes, key_takeaways, corrective_actions, map_image_path, photo_image_path, status, created_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
  $stmt->execute(array($title, $incident_date, $incident_time, $who_name, $who_npk, $summary, $result, $root_causes, $key_takeaways, $corrective_actions, $map_image_path, $photo_image_path, $status, $_SESSION['admin_id']));

  header('Location: index.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="../../img/logo_safety.png" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Incident - OHSS Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Header and Navigation -->
    <header class="bg-gradient-to-r from-red-600 to-red-800 text-white py-4 shadow-lg mb-6">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Company Header -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-4">
                    <img src="../../img/batamindo.png" alt="Batamindo" class="h-12 w-auto bg-white p-1 rounded">
                    <div>
                        <h1 class="text-2xl font-bold text-white">Batamindo Industrial Park</h1>
                        <p class="text-red-200">OHS Security System</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="text-right">
                        <p class="text-sm text-white">Welcome, Admin</p>
                        <p class="text-xs text-red-200"><?php echo date('l, d F Y'); ?></p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="index.php" class="bg-white text-red-600 px-4 py-2 rounded-lg hover:bg-red-100 transition">
                            <i class="fas fa-arrow-left mr-1"></i> Back
                        </a>
                        <a href="../logout.php" class="bg-white hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-150">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 pb-12">
        <!-- Page Title -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Add New Incident</h2>
            <p class="mt-1 text-sm text-gray-600">Fill in the incident details below.</p>
        </div>

        <!-- Incident Form -->
        <form method="post" enctype="multipart/form-data" class="bg-white rounded-lg shadow-lg">
            <!-- Form Header -->
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-medium text-gray-900">Incident Information</h3>
            </div>

            <!-- Form Content -->
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input name="title" required 
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                       placeholder="Enter incident title" />
      </div>
      
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
        <input type="date" name="incident_date" value="<?php echo date('Y-m-d'); ?>" 
               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Time</label>
        <input type="time" name="incident_time" 
               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Reporter Name</label>
        <input name="who_name" 
               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
               placeholder="Enter reporter name" />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">NPK</label>
        <input name="who_npk" 
               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
               placeholder="Enter NPK number" />
      </div>

      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Summary</label>
        <textarea name="summary" rows="3" 
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                  placeholder="Describe what happened..."></textarea>
      </div>

      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Result</label>
        <textarea name="result" rows="2" 
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                  placeholder="What was the outcome..."></textarea>
      </div>

      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Root Causes</label>
        <textarea name="root_causes" rows="4" 
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                  placeholder="List the root causes..."></textarea>
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Key Takeaways</label>
        <textarea name="key_takeaways" rows="3" 
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                  placeholder="List the key lessons learned..."></textarea>
      </div>

      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Corrective Actions</label>
        <textarea name="corrective_actions" rows="3" 
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                  placeholder="List the actions taken to prevent recurrence..."></textarea>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
        <select name="status" 
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
          <option value="draft">Draft</option>
          <option value="published" selected>Published</option>
          <option value="archived">Archived</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Evidence Photo</label>
        <div id="photo-drop-area" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md cursor-pointer relative">
          <input id="photo-image" name="photo_image" type="file" accept="image/*" class="sr-only">
          <div class="space-y-1 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
              <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <div class="flex text-sm text-gray-600 justify-center">
              <span id="photo-drop-label" class="relative cursor-pointer rounded-md font-medium text-blue-600 hover:text-blue-500">Upload or drag a photo</span>
            </div>
            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
            <img id="photo-preview" src="#" alt="Preview" class="mx-auto mt-2 rounded shadow hidden max-h-40" />
          </div>
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Incident Map</label>
        <div id="map-drop-area" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md cursor-pointer relative">
          <input id="map-image" name="map_image" type="file" accept="image/*" class="sr-only">
          <div class="space-y-1 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
              <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <div class="flex text-sm text-gray-600 justify-center">
              <span id="map-drop-label" class="relative cursor-pointer rounded-md font-medium text-blue-600 hover:text-blue-500">Upload or drag a map</span>
            </div>
            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
            <img id="map-preview" src="#" alt="Preview" class="mx-auto mt-2 rounded shadow hidden max-h-40" />
          </div>
        </div>
      </div>

      <!-- Form Actions -->
      <div class="md:col-span-2 border-t border-gray-200 pt-5">
        <div class="flex justify-end space-x-3">
          <button type="button" onclick="window.location.href='index.php'" 
                  class="rounded-lg border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Cancel
          </button>
          <button type="submit" 
                  class="rounded-lg border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Save Incident
          </button>
        </div>
      </div>
    </form>
  </main>
</body>
</html>
<script>
// Photo drag and drop
const photoDropArea = document.getElementById('photo-drop-area');
const photoInput = document.getElementById('photo-image');
const photoPreview = document.getElementById('photo-preview');
const photoLabel = document.getElementById('photo-drop-label');
photoDropArea.addEventListener('click', () => photoInput.click());
photoDropArea.addEventListener('dragover', e => {
  e.preventDefault();
  photoDropArea.classList.add('bg-blue-50');
});
photoDropArea.addEventListener('dragleave', e => {
  photoDropArea.classList.remove('bg-blue-50');
});
photoDropArea.addEventListener('drop', e => {
  e.preventDefault();
  photoDropArea.classList.remove('bg-blue-50');
  if (e.dataTransfer.files.length) {
    photoInput.files = e.dataTransfer.files;
    showPhotoPreview(photoInput.files[0]);
  }
});
photoInput.addEventListener('change', e => {
  if (photoInput.files.length) {
    showPhotoPreview(photoInput.files[0]);
  }
});
function showPhotoPreview(file) {
  if (!file) return photoPreview.classList.add('hidden');
  const reader = new FileReader();
  reader.onload = e => {
    photoPreview.src = e.target.result;
    photoPreview.classList.remove('hidden');
  };
  reader.readAsDataURL(file);
}
// Map drag and drop
const mapDropArea = document.getElementById('map-drop-area');
const mapInput = document.getElementById('map-image');
const mapPreview = document.getElementById('map-preview');
const mapLabel = document.getElementById('map-drop-label');
mapDropArea.addEventListener('click', () => mapInput.click());
mapDropArea.addEventListener('dragover', e => {
  e.preventDefault();
  mapDropArea.classList.add('bg-blue-50');
});
mapDropArea.addEventListener('dragleave', e => {
  mapDropArea.classList.remove('bg-blue-50');
});
mapDropArea.addEventListener('drop', e => {
  e.preventDefault();
  mapDropArea.classList.remove('bg-blue-50');
  if (e.dataTransfer.files.length) {
    mapInput.files = e.dataTransfer.files;
    showMapPreview(mapInput.files[0]);
  }
});
mapInput.addEventListener('change', e => {
  if (mapInput.files.length) {
    showMapPreview(mapInput.files[0]);
  }
});
function showMapPreview(file) {
  if (!file) return mapPreview.classList.add('hidden');
  const reader = new FileReader();
  reader.onload = e => {
    mapPreview.src = e.target.result;
    mapPreview.classList.remove('hidden');
  };
  reader.readAsDataURL(file);
}
</script>


