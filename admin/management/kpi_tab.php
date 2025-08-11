<?php
require_once '../auth.php';
require_once '../../config/database.php';
requireAdminLogin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'update_leading') {
            $id = $_POST['id'];
            $actual_value = sanitize($_POST['actual_value']);
            $target_value = sanitize($_POST['target_value']);
            $notes = sanitize($_POST['notes']);
            
            $stmt = $pdo->prepare("UPDATE kpi_leading SET actual_value = ?, target_value = ?, notes = ? WHERE id = ?");
            if ($stmt->execute([$actual_value, $target_value, $notes, $id])) {
                $message = 'KPI Leading berhasil diperbarui!';
            }
        } elseif ($_POST['action'] == 'update_lagging') {
            $id = $_POST['id'];
            $actual_value = sanitize($_POST['actual_value']);
            $target_value = sanitize($_POST['target_value']);
            $notes = sanitize($_POST['notes']);
            
            $stmt = $pdo->prepare("UPDATE kpi_lagging SET actual_value = ?, target_value = ?, notes = ? WHERE id = ?");
            if ($stmt->execute([$actual_value, $target_value, $notes, $id])) {
                $message = 'KPI Lagging berhasil diperbarui!';
            }
        }
    }
}

$leadingKPIs = $pdo->query("SELECT * FROM kpi_leading ORDER BY indicator_name")->fetchAll();
$laggingKPIs = $pdo->query("SELECT * FROM kpi_lagging ORDER BY indicator_name")->fetchAll();
?>

<?php if ($message): ?>
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
    <?php echo $message; ?>
</div>
<?php endif; ?>

<!-- KPI Leading Indicators -->
<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">
        <i class="fas fa-chart-line text-blue-600 mr-2"></i>Leading Indicators
    </h2>
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Indicator</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actual</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($leadingKPIs as $kpi): ?>
                <tr>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        <?php echo $kpi['indicator_name']; ?>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                        <input type="number" value="<?php echo $kpi['target_value']; ?>" 
                               class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               name="target_value_<?php echo $kpi['id']; ?>">
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                        <input type="number" value="<?php echo $kpi['actual_value']; ?>" 
                               class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               name="actual_value_<?php echo $kpi['id']; ?>">
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-500">
                        <textarea rows="2" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  name="notes_<?php echo $kpi['id']; ?>"><?php echo $kpi['notes']; ?></textarea>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="updateKPI('leading', <?php echo $kpi['id']; ?>)" 
                                class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                            Update
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- KPI Lagging Indicators -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">
        <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>Lagging Indicators
    </h2>
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Indicator</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actual</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($laggingKPIs as $kpi): ?>
                <tr>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        <?php echo $kpi['indicator_name']; ?>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                        <input type="number" value="<?php echo $kpi['target_value']; ?>" 
                               class="border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                               name="target_value_<?php echo $kpi['id']; ?>">
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                        <input type="number" value="<?php echo $kpi['actual_value']; ?>" 
                               class="border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                               name="actual_value_<?php echo $kpi['id']; ?>">
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-500">
                        <textarea rows="2" class="border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                                  name="notes_<?php echo $kpi['id']; ?>"><?php echo $kpi['notes']; ?></textarea>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="updateKPI('lagging', <?php echo $kpi['id']; ?>)" 
                                class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                            Update
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function updateKPI(type, id) {
    const targetValue = document.querySelector(`input[name="target_value_${id}"]`).value;
    const actualValue = document.querySelector(`input[name="actual_value_${id}"]`).value;
    const notes = document.querySelector(`textarea[name="notes_${id}"]`).value;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="action" value="update_${type}">
        <input type="hidden" name="id" value="${id}">
        <input type="hidden" name="target_value" value="${targetValue}">
        <input type="hidden" name="actual_value" value="${actualValue}">
        <input type="hidden" name="notes" value="${notes}">
    `;
    document.body.appendChild(form);
    form.submit();
}
</script>
