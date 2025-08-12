<?php
// All logic is now inside PHP tags, nothing will be output as HTML.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'create_leading') {
            $indicator_name = sanitize($_POST['indicator_name']);
            $actual_value = sanitize($_POST['actual_value']);
            try {
                $stmt = $pdo->prepare("INSERT INTO kpi_leading (indicator_name, actual_value) VALUES (?, ?)");
                if ($stmt->execute([$indicator_name, $actual_value])) {
                    $message = 'KPI Leading berhasil ditambahkan!';
                }
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $message = '<span class=\"text-red-600\">KPI Leading untuk indikator tersebut sudah ada.</span>';
                } else {
                    throw $e;
                }
            }
        } elseif ($_POST['action'] == 'delete_leading') {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM kpi_leading WHERE id = ?");
            if ($stmt->execute([$id])) {
                $message = 'KPI Leading berhasil dihapus!';
            }
        } elseif ($_POST['action'] == 'create_lagging') {
            $indicator_name = sanitize($_POST['indicator_name']);
            $actual_value = sanitize($_POST['actual_value']);
            try {
                $stmt = $pdo->prepare("INSERT INTO kpi_lagging (indicator_name, actual_value) VALUES (?, ?)");
                if ($stmt->execute([$indicator_name, $actual_value])) {
                    $message = 'KPI Lagging berhasil ditambahkan!';
                }
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $message = '<span class=\"text-red-600\">KPI Lagging untuk indikator tersebut sudah ada.</span>';
                } else {
                    throw $e;
                }
            }
        } elseif ($_POST['action'] == 'delete_lagging') {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM kpi_lagging WHERE id = ?");
            if ($stmt->execute([$id])) {
                $message = 'KPI Lagging berhasil dihapus!';
            }
        }
    }
}
?>

<?php
require_once '../auth.php';
require_once '../../config/database.php';
requireAdminLogin();

$message = '';

// Handle create and delete actions for both KPI types
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'create_leading') {
            $indicator_name = sanitize($_POST['indicator_name']);
            $actual_value = sanitize($_POST['actual_value']);
            $month = (int)$_POST['month'];
            $year = (int)$_POST['year'];
            $stmt = $pdo->prepare("INSERT INTO kpi_leading (indicator_name, actual_value, month, year) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$indicator_name, $actual_value, $month, $year])) {
                $message = 'KPI Leading berhasil ditambahkan!';
            }
        } elseif ($_POST['action'] == 'delete_leading') {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM kpi_leading WHERE id = ?");
            if ($stmt->execute([$id])) {
                $message = 'KPI Leading berhasil dihapus!';
            }
        } elseif ($_POST['action'] == 'create_lagging') {
            $indicator_name = sanitize($_POST['indicator_name']);
            $actual_value = sanitize($_POST['actual_value']);
            $month = (int)$_POST['month'];
            $year = (int)$_POST['year'];
            $stmt = $pdo->prepare("INSERT INTO kpi_lagging (indicator_name, actual_value, month, year) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$indicator_name, $actual_value, $month, $year])) {
                $message = 'KPI Lagging berhasil ditambahkan!';
            }
        } elseif ($_POST['action'] == 'delete_lagging') {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM kpi_lagging WHERE id = ?");
            if ($stmt->execute([$id])) {
                $message = 'KPI Lagging berhasil dihapus!';
            }
        } elseif ($_POST['action'] == 'update_leading') {
            $id = $_POST['id'];
            $actual_value = sanitize($_POST['actual_value']);
            $target_value = isset($_POST['target_value']) && $_POST['target_value'] !== '' ? trim($_POST['target_value']) : null;
            $notes = isset($_POST['notes']) && $_POST['notes'] !== '' ? trim($_POST['notes']) : null;
            $stmt = $pdo->prepare("UPDATE kpi_leading SET actual_value = ?, target_value = ?, notes = ? WHERE id = ?");
            if ($stmt->execute([$actual_value, $target_value, $notes, $id])) {
                $message = 'KPI Leading berhasil diperbarui!';
            }
        } elseif ($_POST['action'] == 'update_lagging') {
            $id = $_POST['id'];
            $actual_value = sanitize($_POST['actual_value']);
            $stmt = $pdo->prepare("UPDATE kpi_lagging SET actual_value = ? WHERE id = ?");
            if ($stmt->execute([$actual_value, $id])) {
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
<!-- Form to create new Leading KPI -->
<div class="bg-white rounded-lg shadow-md p-6 mb-4">
    <form method="POST" class="flex flex-wrap gap-4 items-end">
        <input type="hidden" name="action" value="create_leading">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Indicator Name</label>
            <input type="text" name="indicator_name" required class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 w-48">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Actual</label>
            <input type="number" name="actual_value" required class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 w-24">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tambah KPI Leading</button>
    </form>
</div>
<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">
        <i class="fas fa-chart-line text-blue-600 mr-2"></i>Leading Indicators
    </h2>
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Indicator</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actual</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($leadingKPIs as $kpi): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        <?php echo $kpi['indicator_name']; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <input type="number" value="<?php echo $kpi['actual_value']; ?>" 
                               class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 w-24"
                               name="actual_value_<?php echo $kpi['id']; ?>">
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium flex gap-2">
                        <button onclick="updateKPI('leading', <?php echo $kpi['id']; ?>)" type="button"
                                class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                            Update
                        </button>
                        <form method="POST" onsubmit="return confirm('Hapus KPI ini?');" style="display:inline;">
                            <input type="hidden" name="action" value="delete_leading">
                            <input type="hidden" name="id" value="<?php echo $kpi['id']; ?>">
                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-700">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- KPI Lagging Indicators -->
<!-- Form to create new Lagging KPI -->
<div class="bg-white rounded-lg shadow-md p-6 mb-4">
    <form method="POST" class="flex flex-wrap gap-4 items-end">
        <input type="hidden" name="action" value="create_lagging">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Indicator Name</label>
            <input type="text" name="indicator_name" required class="border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 w-48">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Actual</label>
            <input type="number" name="actual_value" required class="border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 w-24">
        </div>
        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Tambah KPI Lagging</button>
    </form>
</div>
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">
        <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>Lagging Indicators
    </h2>
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Indicator</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actual</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($laggingKPIs as $kpi): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        <?php echo $kpi['indicator_name']; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <input type="number" value="<?php echo $kpi['actual_value']; ?>" 
                               class="border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 w-24"
                               name="actual_value_lagging_<?php echo $kpi['id']; ?>">
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium flex gap-2">
                        <button onclick="updateKPI('lagging', <?php echo $kpi['id']; ?>)" type="button"
                                class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                            Update
                        </button>
                        <form method="POST" onsubmit="return confirm('Hapus KPI ini?');" style="display:inline;">
                            <input type="hidden" name="action" value="delete_lagging">
                            <input type="hidden" name="id" value="<?php echo $kpi['id']; ?>">
                            <button type="submit" class="bg-gray-500 text-white px-3 py-1 rounded hover:bg-gray-700">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function updateKPI(type, id) {
    let inputName = type === 'lagging' ? `actual_value_lagging_${id}` : `actual_value_${id}`;
    const actualValue = document.querySelector(`input[name="${inputName}"]`).value;
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="action" value="update_${type}">
        <input type="hidden" name="id" value="${id}">
        <input type="hidden" name="actual_value" value="${actualValue}">
    `;
    document.body.appendChild(form);
    form.submit();
}
</script>
