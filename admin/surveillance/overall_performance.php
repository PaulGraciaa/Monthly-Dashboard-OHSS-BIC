<?php
require_once 'template_header.php';
// Sanitize function for PHP 5.3.8
function sanitize($str) {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

$message = '';
$message_type = '';

// Create/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $indicator = sanitize($_POST['indicator']);
        $current_month = sanitize($_POST['current_month']);
        $cumulative = sanitize($_POST['cumulative']);

        if ($_POST['action'] == 'add') {
            $stmt = mysqli_prepare($conn, "INSERT INTO surveillance_overall_performance (indicator, current_month, cumulative) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $indicator, $current_month, $cumulative);
            if (mysqli_stmt_execute($stmt)) {
                $message = "Data berhasil ditambahkan!";
                $message_type = "success";
            } else {
                $message = "Gagal menambahkan data!";
                $message_type = "error";
            }
            mysqli_stmt_close($stmt);
        } elseif ($_POST['action'] == 'edit') {
            $id = (int)sanitize($_POST['id']);
            $stmt = mysqli_prepare($conn, "UPDATE surveillance_overall_performance SET indicator = ?, current_month = ?, cumulative = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "sssi", $indicator, $current_month, $cumulative, $id);
            if (mysqli_stmt_execute($stmt)) {
                $message = "Data berhasil diperbarui!";
                $message_type = "success";
            } else {
                $message = "Gagal memperbarui data!";
                $message_type = "error";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Delete
if (isset($_GET['delete'])) {
    $id = (int)sanitize($_GET['delete']);
    $stmt = mysqli_prepare($conn, "DELETE FROM surveillance_overall_performance WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        $message = "Data berhasil dihapus!";
        $message_type = "success";
    } else {
        $message = "Gagal menghapus data!";
        $message_type = "error";
    }
    mysqli_stmt_close($stmt);
}

// Ambil data untuk edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int)sanitize($_GET['edit']);
    $stmt = mysqli_prepare($conn, "SELECT * FROM surveillance_overall_performance WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $edit_data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

// Ambil semua data
$data = array();
$result = mysqli_query($conn, "SELECT * FROM surveillance_overall_performance ORDER BY id ASC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}
?>
<!DOCTYPE html>
    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">
        <!-- Message -->
        <?php if ($message): ?>
        <div class="mb-6 p-4 rounded-lg <?php echo $message_type == 'success' ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300'; ?>">
            <i class="fas <?php echo $message_type == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> mr-2"></i>
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 text-gray-800">
                <?php echo $edit_data ? 'Edit Data' : 'Tambah Data Baru'; ?>
            </h2>
            
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="<?php echo $edit_data ? 'edit' : 'add'; ?>">
                <?php if ($edit_data): ?>
                <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                <?php endif; ?>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Performance Indicator</label>
                        <input type="text" name="indicator" value="<?php echo $edit_data ? htmlspecialchars($edit_data['indicator']) : ''; ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-header-footer-bg" 
                               placeholder="Contoh: Overall CCTV Operational Readiness Performance" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Month (%)</label>
                        <input type="text" name="current_month" value="<?php echo $edit_data ? htmlspecialchars($edit_data['current_month']) : ''; ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-header-footer-bg" 
                               placeholder="Contoh: 100%" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cumulative (%)</label>
                        <input type="text" name="cumulative" value="<?php echo $edit_data ? htmlspecialchars($edit_data['cumulative']) : ''; ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-header-footer-bg" 
                               placeholder="Contoh: 100%">
                    </div>
                </div>
                
                <div class="flex space-x-4">
                    <button type="submit" class="bg-header-footer-bg hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium">
                        <i class="fas <?php echo $edit_data ? 'fa-save' : 'fa-plus'; ?> mr-2"></i>
                        <?php echo $edit_data ? 'Update' : 'Tambah'; ?>
                    </button>
                    
                    <?php if ($edit_data): ?>
                    <a href="?cancel" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Data Surveillance Overall Performance</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance Indicator</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Month</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cumulative</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($data as $index => $row): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $index + 1; ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($row['indicator']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['current_month']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['cumulative']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="?edit=<?php echo $row['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="?delete=<?php echo $row['id']; ?>" class="text-red-600 hover:text-red-900" 
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Auto hide message after 5 seconds
        setTimeout(function() {
            const message = document.querySelector('.mb-6');
            if (message) {
                message.style.display = 'none';
            }
        }, 5000);
    </script>

<?php require_once 'template_footer.php'; ?>
