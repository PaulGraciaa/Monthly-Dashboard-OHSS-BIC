<?php
require_once 'template_header.php';
$error = '';
$success = '';
$notif = isset($_SESSION['notif']) ? $_SESSION['notif'] : '';
unset($_SESSION['notif']);

// Handle tambah data
if (isset($_POST['add_submit'])) {
    $serial_number = isset($_POST['serial_number']) ? (int)$_POST['serial_number'] : 0;
    $training_date = isset($_POST['training_date']) ? $_POST['training_date'] : '';
    $location = isset($_POST['location']) ? mysqli_real_escape_string($conn, $_POST['location']) : '';
    $subject = isset($_POST['subject']) ? mysqli_real_escape_string($conn, $_POST['subject']) : '';
    $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    if (empty($location) || empty($subject)) {
        $error = 'Location dan Subject tidak boleh kosong';
    } else {
        $query = "INSERT INTO fire_safety_training (serial_number, training_date, location, subject, display_order, is_active) VALUES ($serial_number, '$training_date', '$location', '$subject', $display_order, $is_active)";
        if (mysqli_query($conn, $query)) {
            $success = 'Data berhasil ditambahkan';
        } else {
            $error = 'Gagal menambahkan data: '.mysqli_error($conn);
        }
    }
}

// Handle edit data
if (isset($_POST['edit_submit']) && isset($_POST['edit_id'])) {
    $edit_id = (int)$_POST['edit_id'];
    $serial_number = isset($_POST['serial_number']) ? (int)$_POST['serial_number'] : 0;
    $training_date = isset($_POST['training_date']) ? $_POST['training_date'] : '';
    $location = isset($_POST['location']) ? mysqli_real_escape_string($conn, $_POST['location']) : '';
    $subject = isset($_POST['subject']) ? mysqli_real_escape_string($conn, $_POST['subject']) : '';
    $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    if (empty($location) || empty($subject)) {
        $error = 'Location dan Subject tidak boleh kosong';
    } else {
        $query = "UPDATE fire_safety_training SET serial_number=$serial_number, training_date='$training_date', location='$location', subject='$subject', display_order=$display_order, is_active=$is_active WHERE id=$edit_id";
        if (mysqli_query($conn, $query)) {
            $success = 'Data berhasil diperbarui';
        } else {
            $error = 'Gagal memperbarui data: '.mysqli_error($conn);
        }
    }
}

// Handle delete data (soft delete)
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    if ($delete_id > 0) {
        $query = "UPDATE fire_safety_training SET is_active=0 WHERE id=$delete_id";
        if (mysqli_query($conn, $query)) {
            $success = 'Data berhasil dihapus';
        } else {
            $error = 'Gagal menghapus data: '.mysqli_error($conn);
        }
    }
}

// Ambil data untuk list
$list = array();
$result = mysqli_query($conn, "SELECT * FROM fire_safety_training WHERE is_active = 1 ORDER BY display_order ASC, training_date DESC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
}

$page_title = 'Fire Safety Training';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
<div class="min-h-screen p-6">
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-3">
                <div class="bg-red-500 p-2 rounded-lg">
                    <i class="fas fa-chalkboard-teacher text-white text-xl"></i>
                </div>
                <h1 class="text-xl font-bold text-gray-800">Fire Safety Training</h1>
            </div>
            <button onclick="document.getElementById('modalAdd').classList.remove('hidden')" 
                class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded transition duration-200 flex items-center gap-2 shadow-sm">
                <i class="fas fa-plus"></i>
                <span>Tambah Data</span>
            </button>
        </div>
        <?php if ($error) { ?>
            <div class="bg-red-100 border border-red-200 text-red-700 p-3 rounded-md mb-4 text-sm"> <?php echo $error; ?> </div>
        <?php } ?>
        <?php if ($success) { ?>
            <div class="bg-green-100 border border-green-200 text-green-700 p-3 rounded-md mb-4 text-sm"> <?php echo $success; ?> </div>
        <?php } ?>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full table-fixed">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="w-10 py-3 px-2 text-left text-[11px] font-semibold text-gray-600">No</th>
                            <th class="w-20 py-3 px-2 text-left text-[11px] font-semibold text-gray-600">Serial</th>
                            <th class="w-24 py-3 px-2 text-left text-[11px] font-semibold text-gray-600">Training Date</th>
                            <th class="w-32 py-3 px-2 text-left text-[11px] font-semibold text-gray-600">Location</th>
                            <th class="w-32 py-3 px-2 text-left text-[11px] font-semibold text-gray-600">Subject</th>
                            <th class="w-12 py-3 px-2 text-center text-[11px] font-semibold text-gray-600">Order</th>
                            <th class="w-24 py-3 px-2 text-center text-[11px] font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach ($list as $row) { ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-2 px-2 text-[11px] text-gray-700"><?php echo $no++; ?></td>
                            <td class="py-2 px-2 text-[11px] text-gray-700"><?php echo $row['serial_number']; ?></td>
                            <td class="py-2 px-2 text-[11px] text-gray-700"><?php echo $row['training_date']; ?></td>
                            <td class="py-2 px-2 text-[11px] text-gray-700 font-medium truncate" title="<?php echo htmlspecialchars($row['location'], ENT_QUOTES); ?>"><?php echo htmlspecialchars($row['location'], ENT_QUOTES); ?></td>
                            <td class="py-2 px-2 text-[11px] text-gray-700 truncate" title="<?php echo htmlspecialchars($row['subject'], ENT_QUOTES); ?>"><?php echo htmlspecialchars($row['subject'], ENT_QUOTES); ?></td>
                            <td class="py-2 px-2 text-[11px] text-gray-600 text-center"><?php echo $row['display_order']; ?></td>
                            <td class="py-2 px-2 text-center flex justify-center space-x-1">
                                <button onclick="document.getElementById('modalEdit<?php echo $row['id']; ?>').classList.remove('hidden')" 
                                    class="p-1 text-gray-500 hover:text-red-500 transition-colors">
                                    <i class="fas fa-edit text-[11px]"></i>
                                </button>
                                <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Yakin hapus data ini?')" class="p-1 text-gray-500 hover:text-red-500 transition-colors">
                                    <i class="fas fa-trash text-[11px]"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div id="modalEdit<?php echo $row['id']; ?>" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-hidden h-full w-full z-50">
                            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] shadow-xl rounded-lg bg-white">
                                <form method="POST">
                                    <input type="hidden" name="edit_id" value="<?php echo $row['id']; ?>">
                                    <div class="flex justify-between items-center border-b border-gray-200 p-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="bg-red-500 p-2 rounded">
                                                <i class="fas fa-edit text-white text-sm"></i>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-800">Edit Data</h3>
                                        </div>
                                        <button type="button" onclick="this.closest('.fixed').classList.add('hidden')" 
                                            class="text-gray-400 hover:text-gray-500 transition-colors">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="p-6">
                                        <label class="block text-gray-600 text-sm mb-2">Serial Number</label>
                                        <input type="number" name="serial_number" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" value="<?php echo $row['serial_number']; ?>" required>
                                        <label class="block text-gray-600 text-sm mb-2 mt-4">Training Date</label>
                                        <input type="date" name="training_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" value="<?php echo $row['training_date']; ?>">
                                        <label class="block text-gray-600 text-sm mb-2 mt-4">Location</label>
                                        <input type="text" name="location" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" value="<?php echo htmlspecialchars($row['location'], ENT_QUOTES); ?>" required>
                                        <label class="block text-gray-600 text-sm mb-2 mt-4">Subject</label>
                                        <input type="text" name="subject" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" value="<?php echo htmlspecialchars($row['subject'], ENT_QUOTES); ?>" required>
                                        <label class="block text-gray-600 text-sm mb-2 mt-4">Display Order</label>
                                        <input type="number" name="display_order" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" value="<?php echo $row['display_order']; ?>">
                                        <div class="mt-4 flex items-center">
                                            <input type="checkbox" name="is_active" class="w-4 h-4 rounded border-gray-300 text-red-500 focus:ring-red-500" <?php echo ($row['is_active'] ? 'checked' : ''); ?>>
                                            <label class="ml-2 block text-sm text-gray-600">Set sebagai data aktif</label>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 px-6 py-4 rounded-b-lg flex justify-end space-x-3">
                                        <button type="button" onclick="this.closest('.fixed').classList.add('hidden')" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">Batal</button>
                                        <button type="submit" name="edit_submit" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"><i class="fas fa-save mr-2"></i>Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
<?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Modal Add -->
        <div id="modalAdd" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-hidden h-full w-full z-50">
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] shadow-xl rounded-lg bg-white">
                <form method="POST">
                    <div class="flex justify-between items-center border-b border-gray-200 p-4">
                        <div class="flex items-center space-x-3">
                            <div class="bg-red-500 p-2 rounded">
                                <i class="fas fa-plus text-white text-sm"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800">Tambah Data</h3>
                        </div>
                        <button type="button" onclick="this.closest('.fixed').classList.add('hidden')" class="text-gray-400 hover:text-gray-500 transition-colors"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="p-6">
                        <label class="block text-gray-600 text-sm mb-2">Serial Number</label>
                        <input type="number" name="serial_number" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" required>
                        <label class="block text-gray-600 text-sm mb-2 mt-4">Training Date</label>
                        <input type="date" name="training_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm">
                        <label class="block text-gray-600 text-sm mb-2 mt-4">Location</label>
                        <input type="text" name="location" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" required>
                        <label class="block text-gray-600 text-sm mb-2 mt-4">Subject</label>
                        <input type="text" name="subject" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" required>
                        <label class="block text-gray-600 text-sm mb-2 mt-4">Display Order</label>
                        <input type="number" name="display_order" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" value="0">
                        <div class="mt-4 flex items-center">
                            <input type="checkbox" name="is_active" class="w-4 h-4 rounded border-gray-300 text-red-500 focus:ring-red-500" checked>
                            <label class="ml-2 block text-sm text-gray-600">Set sebagai data aktif</label>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 rounded-b-lg flex justify-end space-x-3">
                        <button type="button" onclick="this.closest('.fixed').classList.add('hidden')" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">Batal</button>
                        <button type="submit" name="add_submit" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"><i class="fas fa-save mr-2"></i>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
