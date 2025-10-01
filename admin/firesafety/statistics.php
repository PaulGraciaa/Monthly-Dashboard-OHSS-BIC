
<?php
require_once 'template_header.php';

// Ambil data statistik
$query = "SELECT * FROM fire_equipment_statistics WHERE is_active = 1 ORDER BY display_order ASC, id DESC";
$result = mysqli_query($conn, $query);

$notif = isset($_SESSION['notif']) ? $_SESSION['notif'] : '';
unset($_SESSION['notif']);
$months = array('jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistik Fire Safety - Batamindo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
<div class="min-h-screen p-6">
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-3">
                <div class="bg-red-500 p-2 rounded-lg">
                    <i class="fas fa-fire-extinguisher text-white text-xl"></i>
                </div>
                <h1 class="text-xl font-bold text-gray-800">Statistik Fire Safety</h1>
            </div>
            <button onclick="document.getElementById('modalAdd').classList.remove('hidden')" 
                class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded transition duration-200 flex items-center gap-2 shadow-sm">
                <i class="fas fa-plus"></i>
                <span>Tambah Data</span>
            </button>
        </div>
    <?php if ($notif): ?>
        <div class="bg-green-100 border border-green-200 text-green-700 p-3 rounded-md mb-4 text-sm">
            <?php echo $notif; ?>
        </div>
    <?php endif; ?>
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="overflow-hidden">
            <table class="w-full table-fixed">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="w-12 py-3 px-2 text-left text-[11px] font-semibold text-gray-600">No</th>
                        <th class="w-32 py-3 px-2 text-left text-[11px] font-semibold text-gray-600">Equipment</th>
                            <?php foreach($months as $m): ?>
                            <th class="w-12 py-3 px-2 text-center text-[11px] font-semibold text-gray-600"><?php echo ucfirst($m); ?></th>
                            <?php endforeach; ?>
                            <th class="w-16 py-3 px-2 text-center text-[11px] font-semibold text-gray-600">Total</th>
                            <th class="w-16 py-3 px-2 text-center text-[11px] font-semibold text-gray-600">Order</th>
                            <th class="w-28 py-3 px-2 text-center text-[11px] font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $no=1; while($row = mysqli_fetch_assoc($result)): ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-2 px-2 text-[11px] text-gray-700"><?php echo $no++; ?></td>
                            <td class="py-2 px-2 text-[11px] text-gray-700 font-medium truncate" title="<?php echo htmlspecialchars($row['equipment_type'], ENT_QUOTES); ?>"><?php echo htmlspecialchars($row['equipment_type'], ENT_QUOTES); ?></td>
                            <?php $grand_total = 0; foreach($months as $m): $grand_total += $row[$m.'_count']; ?>
                            <td class="py-2 px-2 text-[11px] text-gray-600 text-center"><?php echo $row[$m.'_count']; ?></td>
                            <?php endforeach; ?>
                            <td class="py-2 px-2 text-[11px] font-semibold text-red-500 text-center"><?php echo $grand_total; ?></td>
                            <td class="py-2 px-2 text-[11px] text-gray-600 text-center"><?php echo $row['display_order']; ?></td>
                            <td class="py-2 px-2 text-center flex justify-center space-x-1">
                                <button onclick="document.getElementById('modalEdit<?php echo $row['id']; ?>').classList.remove('hidden')" 
                                    class="p-1 text-gray-500 hover:text-yellow-500 transition-colors">
                                    <i class="fas fa-edit text-[11px]"></i>
                                </button>
                                <a href="statistics_action.php?action=delete&id=<?php echo $row['id']; ?>" 
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"
                                    class="p-1 text-gray-500 hover:text-red-500 transition-colors">
                                    <i class="fas fa-trash text-[11px]"></i>
                                </a>
                            </td>
                        </tr>
                        <!-- Modal Edit -->
                        <div id="modalEdit<?php echo $row['id']; ?>" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-hidden h-full w-full z-50">
                            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[800px] shadow-xl rounded-lg bg-white">
                                <form method="POST" action="statistics_action.php?action=edit&id=<?php echo $row['id']; ?>">
                                    <div class="flex justify-between items-center border-b border-gray-200 p-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="bg-red-500 p-2 rounded">
                                                <i class="fas fa-edit text-white text-sm"></i>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-800">
                                                Edit Data Equipment
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
                                                <label class="block text-gray-600 text-sm mb-2">Equipment Type</label>
                                                <input type="text" name="equipment_type" 
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" 
                                                    value="<?php echo htmlspecialchars($row['equipment_type'], ENT_QUOTES); ?>" required>
                                            </div>
                                            <div class="col-span-4">
                                                <label class="block text-gray-600 text-sm mb-2">Display Order</label>
                                                <input type="number" name="display_order" 
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" 
                                                    value="<?php echo $row['display_order']; ?>">
                                            </div>
                                        </div>

                                        <div class="mt-6">
                                            <label class="block text-gray-600 text-sm mb-3">Monthly Count Data</label>
                                            <div class="grid grid-cols-6 gap-4">
                                                <?php foreach($months as $m): ?>
                                                <div>
                                                    <label class="block text-gray-500 text-xs mb-1 uppercase"><?php echo ucfirst($m); ?></label>
                                                    <input type="number" name="<?php echo $m.'_count'; ?>" 
                                                        class="w-full px-2 py-1.5 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" 
                                                        value="<?php echo $row[$m.'_count']; ?>">
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <div class="mt-6 flex items-center">
                                            <input type="checkbox" name="is_active" 
                                                class="w-4 h-4 rounded border-gray-300 text-red-500 focus:ring-red-500" 
                                                <?php echo ($row['is_active']?'checked':''); ?>>
                                            <label class="ml-2 block text-sm text-gray-600">Set sebagai data aktif</label>
                                        </div>
                                    </div>

                                    <div class="bg-gray-50 px-6 py-4 rounded-b-lg flex justify-end space-x-3">
                                        <button type="button" 
                                            onclick="this.closest('.fixed').classList.add('hidden')"
                                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                            Batal
                                        </button>
                                        <button type="submit" 
                                            class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                            <i class="fas fa-save mr-2"></i>Simpan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal Add -->
    <div id="modalAdd" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-hidden h-full w-full z-50">
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[800px] shadow-xl rounded-lg bg-white">
            <form method="POST" action="statistics_action.php?action=create">
                <div class="flex justify-between items-center border-b border-gray-200 p-4">
                    <div class="flex items-center space-x-3">
                        <div class="bg-red-500 p-2 rounded">
                            <i class="fas fa-plus text-white text-sm"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Tambah Data Equipment
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
                            <label class="block text-gray-600 text-sm mb-2">Equipment Type</label>
                            <input type="text" name="equipment_type" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" 
                                required>
                        </div>
                        <div class="col-span-4">
                            <label class="block text-gray-600 text-sm mb-2">Display Order</label>
                            <input type="number" name="display_order" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" 
                                value="0">
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="block text-gray-600 text-sm mb-3">Monthly Count Data</label>
                        <div class="grid grid-cols-6 gap-4">
                            <?php foreach($months as $m): ?>
                            <div>
                                <label class="block text-gray-500 text-xs mb-1 uppercase"><?php echo ucfirst($m); ?></label>
                                <input type="number" name="<?php echo $m.'_count'; ?>" 
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 text-sm" 
                                    value="0">
                            </div>
                            <?php endforeach; ?>
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
                    <button type="submit" 
                        class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Function untuk menampilkan notifikasi
function showNotification(message, type = 'success') {
    const alert = document.createElement('div');
    alert.className = `fixed top-4 right-4 p-4 rounded-lg ${type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'} max-w-md`;
    alert.innerHTML = `
        <div class="flex items-center justify-between">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    document.body.appendChild(alert);
    setTimeout(() => alert.remove(), 5000);
}

// Function untuk menghandle konfirmasi hapus
function confirmDelete(url) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        window.location.href = url;
    }
}
</script>

</body>
</html>
