<?php
require_once 'template_header.php';

$error = '';
$success = '';
$editData = null;

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $delete_id = (int)$_GET['id'];
    $query = "UPDATE fire_safety_repair_impairment SET is_active = 0 WHERE id = ".$delete_id;
    if (mysqli_query($conn, $query)) {
        $success = 'Data berhasil dihapus';
    } else {
        $error = 'Gagal menghapus data: '.mysqli_error($conn);
    }
}

// Handle tambah data
if (isset($_POST['add_submit'])) {
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $year = (int)$_POST['year'];
    $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $months = array('jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec');
    $counts = array();
    foreach ($months as $m) {
        $counts[$m] = isset($_POST[$m.'_count']) ? (int)$_POST[$m.'_count'] : 0;
    }
    if (empty($category)) {
        $error = 'Category tidak boleh kosong';
    } else {
        $query = "INSERT INTO fire_safety_repair_impairment (category, jan_count, feb_count, mar_count, apr_count, may_count, jun_count, jul_count, aug_count, sep_count, oct_count, nov_count, dec_count, year, display_order, is_active) VALUES ('".$category."',".$counts['jan'].",".$counts['feb'].",".$counts['mar'].",".$counts['apr'].",".$counts['may'].",".$counts['jun'].",".$counts['jul'].",".$counts['aug'].",".$counts['sep'].",".$counts['oct'].",".$counts['nov'].",".$counts['dec'].",$year,$display_order,$is_active)";
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
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $year = (int)$_POST['year'];
    $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $months = array('jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec');
    $counts = array();
    foreach ($months as $m) {
        $counts[$m] = isset($_POST[$m.'_count']) ? (int)$_POST[$m.'_count'] : 0;
    }
    if (empty($category)) {
        $error = 'Category tidak boleh kosong';
    } else {
        $query = "UPDATE fire_safety_repair_impairment SET category='".$category."', jan_count=".$counts['jan'].", feb_count=".$counts['feb'].", mar_count=".$counts['mar'].", apr_count=".$counts['apr'].", may_count=".$counts['may'].", jun_count=".$counts['jun'].", jul_count=".$counts['jul'].", aug_count=".$counts['aug'].", sep_count=".$counts['sep'].", oct_count=".$counts['oct'].", nov_count=".$counts['nov'].", dec_count=".$counts['dec'].", year=$year, display_order=$display_order, is_active=$is_active WHERE id=$edit_id";
        if (mysqli_query($conn, $query)) {
            $success = 'Data berhasil diperbarui';
        } else {
            $error = 'Gagal memperbarui data: '.mysqli_error($conn);
        }
    }
}

// Ambil data untuk list
$list = array();
$result = mysqli_query($conn, "SELECT * FROM fire_safety_repair_impairment WHERE is_active = 1 ORDER BY display_order ASC, id DESC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
}

// Ambil data untuk modal edit
if (isset($_GET['edit']) && (int)$_GET['edit'] > 0) {
    $edit_id = (int)$_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM fire_safety_repair_impairment WHERE id = $edit_id LIMIT 1");
    if ($result) {
        $editData = mysqli_fetch_assoc($result);
    }
}

$months = array('jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec');
$page_title = 'Fire Safety Repair Impairment';
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
                    <i class="fas fa-wrench text-white text-xl"></i>
                </div>
                <h1 class="text-xl font-bold text-gray-800">Fire Safety Repair Impairment</h1>
            </div>
            <button onclick="document.getElementById('modalAdd').classList.remove('hidden')" 
                class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded transition duration-200 flex items-center gap-2 shadow-sm">
                <i class="fas fa-plus"></i>
                <span>Tambah Data</span>
            </button>
        </div>
        
        <?php if ($error) { ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></div>
        <?php } ?>
        <?php if ($success) { ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES); ?></div>
        <?php } ?>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="overflow-hidden">
                <table class="w-full table-fixed">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="w-12 py-3 px-2 text-left text-[11px] font-semibold text-gray-600">No</th>
                            <th class="w-32 py-3 px-2 text-left text-[11px] font-semibold text-gray-600">Category</th>
                            <?php foreach ($months as $m) { ?>
                            <th class="w-12 py-3 px-2 text-center text-[11px] font-semibold text-gray-600"><?php echo ucfirst($m); ?></th>
                            <?php } ?>
                            <th class="w-16 py-3 px-2 text-center text-[11px] font-semibold text-gray-600">Total</th>
                            <th class="w-16 py-3 px-2 text-center text-[11px] font-semibold text-gray-600">Year</th>
                            <th class="w-16 py-3 px-2 text-center text-[11px] font-semibold text-gray-600">Order</th>
                            <th class="w-28 py-3 px-2 text-center text-[11px] font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach ($list as $row) { 
                            $total = 0;
                            foreach ($months as $m) {
                                $total += $row[$m.'_count'];
                            }
                        ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-2 px-2 text-[11px] text-gray-700"><?php echo $no++; ?></td>
                            <td class="py-2 px-2 text-[11px] text-gray-700 font-medium truncate" title="<?php echo htmlspecialchars($row['category'], ENT_QUOTES); ?>"><?php echo htmlspecialchars($row['category'], ENT_QUOTES); ?></td>
                            <?php foreach ($months as $m) { ?>
                            <td class="py-2 px-2 text-[11px] text-gray-600 text-center"><?php echo $row[$m.'_count']; ?></td>
                            <?php } ?>
                            <td class="py-2 px-2 text-[11px] font-semibold text-red-500 text-center"><?php echo $total; ?></td>
                            <td class="py-2 px-2 text-[11px] text-gray-600 text-center"><?php echo $row['year']; ?></td>
                            <td class="py-2 px-2 text-[11px] text-gray-600 text-center"><?php echo isset($row['display_order']) ? $row['display_order'] : ''; ?></td>
                            <td class="py-2 px-2 text-center flex justify-center space-x-1">
                                <button onclick="document.getElementById('modalEdit<?php echo $row['id']; ?>').classList.remove('hidden')" 
                                    class="p-1 text-gray-500 hover:text-yellow-500 transition-colors">
                                    <i class="fas fa-edit text-[11px]"></i>
                                </button>
                                <a href="?action=delete&id=<?php echo $row['id']; ?>" 
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"
                                    class="p-1 text-gray-500 hover:text-red-500 transition-colors">
                                    <i class="fas fa-trash text-[11px]"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div id="modalEdit<?php echo $row['id']; ?>" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-hidden h-full w-full z-50">
                            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[800px] shadow-xl rounded-lg bg-white">
                                <form method="POST">
                                    <div class="flex justify-between items-center border-b border-gray-200 p-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="bg-red-500 p-2 rounded">
                                                <i class="fas fa-edit text-white text-sm"></i>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-800">
                                                Edit Data
                                            </h3>
                                        </div>
                                        <button type="button" onclick="this.closest('.fixed').classList.add('hidden')" 
                                            class="text-gray-400 hover:text-gray-500 transition-colors">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="p-6">
                                        <input type="hidden" name="edit_id" value="<?php echo $row['id']; ?>">
                                        <div class="grid grid-cols-12 gap-6">
                                            <div class="col-span-8">
                                                <label class="block text-gray-600 text-sm mb-2">Category</label>
                                                <input type="text" name="category" 
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" 
                                                    value="<?php echo htmlspecialchars($row['category'], ENT_QUOTES); ?>" required>
                                            </div>
                                            <div class="col-span-4">
                                                <label class="block text-gray-600 text-sm mb-2">Year</label>
                                                <input type="number" name="year" 
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" 
                                                    value="<?php echo $row['year']; ?>">
                                            </div>
                                        </div>

                                        <div class="mt-6">
                                            <label class="block text-gray-600 text-sm mb-3">Monthly Count Data</label>
                                            <div class="grid grid-cols-6 gap-4">
                                                <?php foreach ($months as $m) { ?>
                                                <div>
                                                    <label class="block text-gray-500 text-xs mb-1 uppercase"><?php echo ucfirst($m); ?></label>
                                                    <input type="number" name="<?php echo $m.'_count'; ?>" 
                                                        class="w-full px-2 py-1.5 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" 
                                                        value="<?php echo $row[$m.'_count']; ?>">
                                                </div>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <div class="mt-6 flex items-center">
                                            <input type="checkbox" name="is_active" 
                                                class="w-4 h-4 rounded border-gray-300 text-red-500 focus:ring-red-500" 
                                                <?php echo ($row['is_active'] ? 'checked' : ''); ?>>
                                            <label class="ml-2 block text-sm text-gray-600">Set sebagai data aktif</label>
                                        </div>
                                    </div>

                                    <div class="bg-gray-50 px-6 py-4 rounded-b-lg flex justify-end space-x-3">
                                        <button type="button" 
                                            onclick="this.closest('.fixed').classList.add('hidden')"
                                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                            Batal
                                        </button>
                                        <button type="submit" name="edit_submit" 
                                            class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                            <i class="fas fa-save mr-2"></i>Simpan Perubahan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php } ?>
                </tbody>
        </table>

        <!-- Modal Add -->
        <div id="modalAdd" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-hidden h-full w-full z-50">
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[800px] shadow-xl rounded-lg bg-white">
                <form method="POST">
                    <div class="flex justify-between items-center border-b border-gray-200 p-4">
                        <div class="flex items-center space-x-3">
                            <div class="bg-red-500 p-2 rounded">
                                <i class="fas fa-plus text-white text-sm"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                Tambah Data
                            </h3>
                        </div>
                        <button type="button" onclick="this.closest('.fixed').classList.add('hidden')" 
                            class="text-gray-400 hover:text-gray-500 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-12 gap-6">
                            <div class="col-span-8">
                                <label class="block text-gray-600 text-sm mb-2">Category</label>
                                <input type="text" name="category" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" 
                                    required>
                            </div>
                            <div class="col-span-4">
                                <label class="block text-gray-600 text-sm mb-2">Year</label>
                                <input type="number" name="year" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" 
                                    value="2025">
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-gray-600 text-sm mb-3">Monthly Count Data</label>
                            <div class="grid grid-cols-6 gap-4">
                                <?php foreach ($months as $m) { ?>
                                <div>
                                    <label class="block text-gray-500 text-xs mb-1 uppercase"><?php echo ucfirst($m); ?></label>
                                    <input type="number" name="<?php echo $m.'_count'; ?>" 
                                        class="w-full px-2 py-1.5 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" 
                                        value="0">
                                </div>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center">
                            <input type="checkbox" name="is_active" 
                                class="w-4 h-4 rounded border-gray-300 text-red-500 focus:ring-red-500" 
                                checked>
                            <label class="ml-2 block text-sm text-gray-600">Set sebagai data aktif</label>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 rounded-b-lg flex justify-end space-x-3">
                        <button type="button" 
                            onclick="this.closest('.fixed').classList.add('hidden')"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            Batal
                        </button>
                        <button type="submit" name="add_submit"
                            class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            <i class="fas fa-save mr-2"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

