<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../../config/database.php';
requireAdminLogin();

// Fungsi sanitize untuk keamanan input
function sanitize($data) {
  return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES);
}

// Handle delete incident
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM ohs_incidents WHERE id = ?');
    $stmt->execute(array($id));
    header('Location: index.php');
    exit;
}

// List incidents
$where = array();
$params = array();
if (!empty($_GET['q'])) {
    $where[] = '(title LIKE ? OR summary LIKE ?)';
    $params[] = '%' . $_GET['q'] . '%';
    $params[] = '%' . $_GET['q'] . '%';
}
if (!empty($_GET['status'])) {
    $where[] = 'status = ?';
    $params[] = $_GET['status'];
}
$sql = 'SELECT * FROM ohs_incidents';
if ($where) { $sql .= ' WHERE ' . implode(' AND ', $where); }
$sql .= ' ORDER BY incident_date DESC, id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

// Jika view adalah ptw, proses data PTW
$view = isset($_GET['view']) ? $_GET['view'] : '';
if ($view === 'ptw') {
    $currentMonth = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');
    $currentYear  = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

    // Navigasi bulan
    $prevMonth = $currentMonth === 1 ? 12 : $currentMonth - 1;
    $prevYear  = $currentMonth === 1 ? $currentYear - 1 : $currentYear;
    $nextMonth = $currentMonth === 12 ? 1 : $currentMonth + 1;
    $nextYear  = $currentMonth === 12 ? $currentYear + 1 : $currentYear;

    // Handle delete PTW
    if (isset($_GET['delete_ptw'])) {
        $id = (int)$_GET['delete_ptw'];
        $stmt = $pdo->prepare('DELETE FROM ptw_records WHERE id = ?');
        $stmt->execute(array($id));
        header('Location: index.php?view=ptw&month=' . $currentMonth . '&year=' . $currentYear);
        exit;
    }

    // Handle create/update PTW
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $contractor_name = sanitize(isset($_POST['contractor_name']) ? $_POST['contractor_name'] : '');
        $num_ptw = (int)(isset($_POST['num_ptw']) ? $_POST['num_ptw'] : 0);
        $general = (int)(isset($_POST['general']) ? $_POST['general'] : 0);
        $hot_work = (int)(isset($_POST['hot_work']) ? $_POST['hot_work'] : 0);
        $lifting = (int)(isset($_POST['lifting']) ? $_POST['lifting'] : 0);
        $excavation = (int)(isset($_POST['excavation']) ? $_POST['excavation'] : 0);
        $electrical = (int)(isset($_POST['electrical']) ? $_POST['electrical'] : 0);
        $work_high = (int)(isset($_POST['work_high']) ? $_POST['work_high'] : 0);
        $radiography = (int)(isset($_POST['radiography']) ? $_POST['radiography'] : 0);
        $manpower = (int)(isset($_POST['manpower']) ? $_POST['manpower'] : 0);
        $month = (int)(isset($_POST['month']) ? $_POST['month'] : $currentMonth);
        $year = (int)(isset($_POST['year']) ? $_POST['year'] : $currentYear);
        $display_order = (int)(isset($_POST['display_order']) ? $_POST['display_order'] : 0);

        if ($id > 0) {
            // Update existing record
            $stmt = $pdo->prepare('UPDATE ptw_records SET contractor_name=?, num_ptw=?, general=?, hot_work=?, lifting=?, excavation=?, electrical=?, work_high=?, radiography=?, manpower=?, month=?, year=?, display_order=? WHERE id=?');
            $stmt->execute(array($contractor_name, $num_ptw, $general, $hot_work, $lifting, $excavation, $electrical, $work_high, $radiography, $manpower, $month, $year, $display_order, $id));
        } else {
            // Insert new record
            $stmt = $pdo->prepare('INSERT INTO ptw_records (contractor_name, num_ptw, general, hot_work, lifting, excavation, electrical, work_high, radiography, manpower, month, year, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute(array($contractor_name, $num_ptw, $general, $hot_work, $lifting, $excavation, $electrical, $work_high, $radiography, $manpower, $month, $year, $display_order));
        }
        
        header('Location: index.php?view=ptw&month=' . $month . '&year=' . $year);
        exit;
    }

    // Get PTW records for current month and year
    $stmt = $pdo->prepare('SELECT * FROM ptw_records WHERE month = ? AND year = ? ORDER BY display_order, contractor_name');
    $stmt->execute(array($currentMonth, $currentYear));
    $records = $stmt->fetchAll();

    // Calculate totals
  $totals = array(
    'num_ptw' => 0,
    'general' => 0,
    'hot_work' => 0,
    'lifting' => 0,
    'excavation' => 0,
    'electrical' => 0,
    'work_high' => 0,
    'radiography' => 0,
    'manpower' => 0
  );
    
    foreach ($records as $r) {
        $totals['num_ptw'] += (int)$r['num_ptw'];
        $totals['general'] += (int)$r['general'];
        $totals['hot_work'] += (int)$r['hot_work'];
        $totals['lifting'] += (int)$r['lifting'];
        $totals['excavation'] += (int)$r['excavation'];
        $totals['electrical'] += (int)$r['electrical'];
        $totals['work_high'] += (int)$r['work_high'];
        $totals['radiography'] += (int)$r['radiography'];
        $totals['manpower'] += (int)$r['manpower'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OHS Incidents - OHSS Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<link rel="icon" type="image/png" href="../../img/logo_safety.png" />
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
                        <a href="../dashboard.php" class="bg-white text-red-600 px-4 py-2 rounded-lg hover:bg-red-100 transition">
                            <i class="fas fa-arrow-left mr-1"></i> Dashboard
                        </a>
                        <a href="../logout.php" class="bg-white hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-150">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

  <main class="max-w-7xl mx-auto px-4">
    <!-- Action Bar -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-2">
            <a href="index.php" class="px-4 py-2 rounded-lg inline-flex items-center gap-2 border transition <?php echo (!isset($_GET['view'])?'bg-gray-800 text-white border-gray-800 hover:bg-gray-900':'bg-white text-gray-700 hover:bg-gray-50'); ?>">
                <i class="fas fa-list"></i><span>Incidents</span>
            </a>
            <a href="index.php?view=ptw" class="px-4 py-2 rounded-lg inline-flex items-center gap-2 border transition <?php echo ($view==='ptw'?'bg-gray-800 text-white border-gray-800 hover:bg-gray-900':'bg-white text-gray-700 hover:bg-gray-50'); ?>">
                <i class="fas fa-clipboard-list"></i><span>PTW</span>
            </a>
        </div>
    </div>

    <?php if ($view === 'ptw'): ?>
      <div class="bg-white rounded-lg shadow p-4 mb-4">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-bold">PTW Records - <?php echo date('F Y', mktime(0, 0, 0, $currentMonth, 1, $currentYear)); ?></h2>
          <div class="flex items-center gap-2">
            <a href="?view=ptw&month=<?php echo $prevMonth; ?>&year=<?php echo $prevYear; ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-1 rounded">
              <i class="fas fa-chevron-left"></i>
            </a>
            <span class="text-gray-700"><?php echo date('F Y', mktime(0, 0, 0, $currentMonth, 1, $currentYear)); ?></span>
            <a href="?view=ptw&month=<?php echo $nextMonth; ?>&year=<?php echo $nextYear; ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-1 rounded">
              <i class="fas fa-chevron-right"></i>
            </a>
          </div>
        </div>
        
        <form method="get" class="flex flex-wrap items-end gap-3 mb-4">
          <input type="hidden" name="view" value="ptw" />
          <div>
            <label class="block text-sm text-gray-700">Bulan</label>
            <input type="number" name="month" min="1" max="12" class="border rounded px-3 py-2 w-24" value="<?php echo $currentMonth; ?>" />
          </div>
          <div>
            <label class="block text-sm text-gray-700">Tahun</label>
            <input type="number" name="year" class="border rounded px-3 py-2 w-28" value="<?php echo $currentYear; ?>" />
          </div>
          <button class="bg-gray-800 text-white px-4 py-2 rounded inline-flex items-center gap-2"><i class="fas fa-filter"></i>Filter</button>
        </form>
      </div>

      <details id="ptw-form-panel" class="mb-4 bg-white rounded-lg shadow transition hover:shadow-md">
        <summary class="cursor-pointer select-none font-semibold text-gray-800 px-4 py-3 border-b flex items-center justify-between">
          <span class="inline-flex items-center gap-2"><i class="fas fa-pen-to-square text-gray-600"></i>Tambah / Edit Data</span>
          <button type="button" onclick="event.stopPropagation(); openNewForm();" class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded inline-flex items-center gap-2"><i class="fas fa-plus"></i>Tambah Baru</button>
        </summary>
        <form method="post" class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4">
          <input type="hidden" name="id" id="form-id" />
          <div>
            <label class="block text-sm text-gray-700">Contractor</label>
            <input type="text" name="contractor_name" id="form-contractor" required class="border rounded px-3 py-2 w-full" placeholder="Nama Kontraktor" />
          </div>
          <div>
            <label class="block text-sm text-gray-700"># PTW</label>
            <input type="number" name="num_ptw" id="form-num_ptw" min="0" class="border rounded px-3 py-2 w-full" placeholder="Jumlah PTW" />
          </div>
          <div>
            <label class="block text-sm text-gray-700">General</label>
            <input type="number" name="general" id="form-general" min="0" class="border rounded px-3 py-2 w-full" />
          </div>
          <div>
            <label class="block text-sm text-gray-700">Hot Work</label>
            <input type="number" name="hot_work" id="form-hot_work" min="0" class="border rounded px-3 py-2 w-full" />
          </div>
          <div>
            <label class="block text-sm text-gray-700">Lifting</label>
            <input type="number" name="lifting" id="form-lifting" min="0" class="border rounded px-3 py-2 w-full" />
          </div>
          <div>
            <label class="block text-sm text-gray-700">Excavation</label>
            <input type="number" name="excavation" id="form-excavation" min="0" class="border rounded px-3 py-2 w-full" />
          </div>
          <div>
            <label class="block text-sm text-gray-700">Electrical</label>
            <input type="number" name="electrical" id="form-electrical" min="0" class="border rounded px-3 py-2 w-full" />
          </div>
          <div>
            <label class="block text-sm text-gray-700">Work High</label>
            <input type="number" name="work_high" id="form-work_high" min="0" class="border rounded px-3 py-2 w-full" />
          </div>
          <div>
            <label class="block text-sm text-gray-700">Radiography</label>
            <input type="number" name="radiography" id="form-radiography" min="0" class="border rounded px-3 py-2 w-full" />
          </div>
          <div>
            <label class="block text-sm text-gray-700">Manpower</label>
            <input type="number" name="manpower" id="form-manpower" min="0" class="border rounded px-3 py-2 w-full" />
          </div>
          <div>
            <label class="block text-sm text-gray-700">Urutan</label>
            <input type="number" name="display_order" id="form-display_order" class="border rounded px-3 py-2 w-full" placeholder="Urutan tampil" />
          </div>
          <div>
            <label class="block text-sm text-gray-700">Bulan</label>
            <input type="number" name="month" id="form-month" value="<?php echo $currentMonth; ?>" class="border rounded px-3 py-2 w-full" />
          </div>
          <div>
            <label class="block text-sm text-gray-700">Tahun</label>
            <input type="number" name="year" id="form-year" value="<?php echo $currentYear; ?>" class="border rounded px-3 py-2 w-full" />
          </div>
          <div class="col-span-2 md:col-span-4 flex items-center gap-3">
            <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded inline-flex items-center gap-2"><i class="fas fa-save"></i>Simpan</button>
            <button type="button" onclick="openNewForm();" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded inline-flex items-center gap-2"><i class="fas fa-rotate"></i>Reset</button>
          </div>
        </form>
      </details>

      <div class="overflow-x-auto bg-white rounded-lg shadow transition hover:shadow-md">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-100">
            <tr class="sticky top-0 z-10">
              <th class="border px-3 py-2">No</th>
              <th class="border px-3 py-2 text-left">Contractor</th>
              <th class="border px-3 py-2">#PTW</th>
              <th class="border px-3 py-2">General</th>
              <th class="border px-3 py-2">Hot</th>
              <th class="border px-3 py-2">Lifting</th>
              <th class="border px-3 py-2">Excav.</th>
              <th class="border px-3 py-2">Electrical</th>
              <th class="border px-3 py-2">Work High</th>
              <th class="border px-3 py-2">Radiography</th>
              <th class="border px-3 py-2">Manpower</th>
              <th class="border px-3 py-2">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($records as $index => $r): ?>
              <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100">
                <td class="border px-3 py-2 text-center"><?php echo $index+1; ?></td>
                <td class="border px-3 py-2"><?php echo htmlspecialchars($r['contractor_name']); ?></td>
                <td class="border px-3 py-2 text-center"><?php echo (int)$r['num_ptw']; ?></td>
                <td class="border px-3 py-2 text-center"><?php echo (int)$r['general']; ?></td>
                <td class="border px-3 py-2 text-center"><?php echo (int)$r['hot_work']; ?></td>
                <td class="border px-3 py-2 text-center"><?php echo (int)$r['lifting']; ?></td>
                <td class="border px-3 py-2 text-center"><?php echo (int)$r['excavation']; ?></td>
                <td class="border px-3 py-2 text-center"><?php echo (int)$r['electrical']; ?></td>
                <td class="border px-3 py-2 text-center"><?php echo (int)$r['work_high']; ?></td>
                <td class="border px-3 py-2 text-center"><?php echo (int)$r['radiography']; ?></td>
                <td class="border px-3 py-2 text-center"><?php echo (int)$r['manpower']; ?></td>
                <td class="border px-3 py-2 text-center">
                  <button onclick='fillForm(<?php echo json_encode($r); ?>)'
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-xs inline-flex items-center gap-2" title="Edit"><i class="fas fa-edit"></i><span>Edit</span></button>
                  <a href="?view=ptw&delete_ptw=<?php echo $r['id']; ?>&month=<?php echo $currentMonth; ?>&year=<?php echo $currentYear; ?>" onclick="return confirm('Hapus data ini?')" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs inline-flex items-center gap-2" title="Hapus"><i class="fas fa-trash"></i><span>Hapus</span></a>
                </td>
              </tr>
            <?php endforeach; ?>
            <tr class="bg-green-50 font-semibold">
              <td class="border px-3 py-2 text-right" colspan="2">Total</td>
              <td class="border px-3 py-2 text-center"><?php echo $totals['num_ptw']; ?></td>
              <td class="border px-3 py-2 text-center"><?php echo $totals['general']; ?></td>
              <td class="border px-3 py-2 text-center"><?php echo $totals['hot_work']; ?></td>
              <td class="border px-3 py-2 text-center"><?php echo $totals['lifting']; ?></td>
              <td class="border px-3 py-2 text-center"><?php echo $totals['excavation']; ?></td>
              <td class="border px-3 py-2 text-center"><?php echo $totals['electrical']; ?></td>
              <td class="border px-3 py-2 text-center"><?php echo $totals['work_high']; ?></td>
              <td class="border px-3 py-2 text-center"><?php echo $totals['radiography']; ?></td>
              <td class="border px-3 py-2 text-center"><?php echo $totals['manpower']; ?></td>
              <td class="border px-3 py-2"></td>
            </tr>
          </tbody>
        </table>
      </div>

      <script>
      function fillForm(row) {
        document.getElementById('form-id').value = row.id;
        document.getElementById('form-contractor').value = row.contractor_name;
        document.getElementById('form-num_ptw').value = row.num_ptw;
        document.getElementById('form-general').value = row.general;
        document.getElementById('form-hot_work').value = row.hot_work;
        document.getElementById('form-lifting').value = row.lifting;
        document.getElementById('form-excavation').value = row.excavation;
        document.getElementById('form-electrical').value = row.electrical;
        document.getElementById('form-work_high').value = row.work_high;
        document.getElementById('form-radiography').value = row.radiography;
        document.getElementById('form-manpower').value = row.manpower;
        document.getElementById('form-display_order').value = row.display_order;
        document.getElementById('form-month').value = row.month;
        document.getElementById('form-year').value = row.year;
        const panel = document.getElementById('ptw-form-panel');
        if (panel) { panel.open = true; }
        document.getElementById('form-contractor').focus();
      }

      function openNewForm() {
        document.getElementById('form-id').value = '';
        document.getElementById('form-contractor').value = '';
        document.getElementById('form-num_ptw').value = '';
        document.getElementById('form-general').value = '';
        document.getElementById('form-hot_work').value = '';
        document.getElementById('form-lifting').value = '';
        document.getElementById('form-excavation').value = '';
        document.getElementById('form-electrical').value = '';
        document.getElementById('form-work_high').value = '';
        document.getElementById('form-radiography').value = '';
        document.getElementById('form-manpower').value = '';
        document.getElementById('form-display_order').value = '';
        document.getElementById('form-month').value = '<?php echo $currentMonth; ?>';
        document.getElementById('form-year').value = '<?php echo $currentYear; ?>';
        const panel = document.getElementById('ptw-form-panel');
        if (panel) { panel.open = true; }
        document.getElementById('form-contractor').focus();
      }
      </script>

    <?php else: ?>

      <!-- Search and Filter Section -->
      <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold text-gray-800">Filter Incidents</h2>
          <div class="flex items-center gap-2">
            <a href="create.php" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors duration-150">
              <i class="fas fa-plus mr-2"></i>Add New Incident
            </a>
          </div>
        </div>
        <form class="flex flex-wrap items-end gap-4">
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <div class="relative">
              <input type="text" name="q" value="<?php echo htmlspecialchars(isset($_GET['q']) ? $_GET['q'] : ''); ?>" 
                     class="border rounded-lg pl-10 pr-4 py-2 w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                     placeholder="Search by title or summary..." />
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
              </div>
            </div>
          </div>
          <div class="w-48">
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="border rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
              <option value="">All Status</option>
              <?php foreach (array("draft","published","archived") as $st): ?>
                <option value="<?php echo $st; ?>" <?php echo ((isset($_GET['status']) ? $_GET['status'] : '')===$st)?'selected':''; ?>><?php echo ucfirst($st); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-150">
              <i class="fas fa-filter mr-2"></i>Apply Filter
            </button>
          </div>
        </form>
      </div>

      <!-- Incidents Table -->
      <div class="bg-white rounded-lg shadow-lg">
        <div class="p-6">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Incident List</h2>
          <div class="overflow-x-auto">
            <table class="min-w-full">
              <thead>
                <tr class="bg-gray-50 border-b">
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Title</th>
                  <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Date</th>
                  <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Reporter</th>
                  <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Status</th>
                  <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <?php foreach ($rows as $row): ?>
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                  <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($row['title']); ?></div>
                  </td>
                  <td class="px-6 py-4 text-center text-sm text-gray-500">
                    <?php echo htmlspecialchars($row['incident_date']); ?><?php echo $row['incident_time']? ' '.$row['incident_time']:''; ?>
                  </td>
                  <td class="px-6 py-4 text-center text-sm text-gray-500">
                    <?php echo htmlspecialchars(trim((isset($row['who_name']) ? $row['who_name'] : '').' ('.$row['who_npk'].')')); ?>
                  </td>
                  <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                      <?php echo $row['status']==='published'?'bg-green-100 text-green-800':
                              ($row['status']==='draft'?'bg-yellow-100 text-yellow-800':
                               'bg-gray-100 text-gray-800'); ?>">
                      <?php echo ucfirst($row['status']); ?>
                    </span>
                  </td>
                  <td class="px-6 py-4 text-center">
                    <div class="flex justify-center items-center space-x-2">
                      <a href="edit.php?id=<?php echo $row['id']; ?>" 
                         class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors duration-150">
                        <i class="fas fa-edit mr-1"></i> Edit
                      </a>
                      <a href="?delete=<?php echo $row['id']; ?>" 
                         onclick="return confirm('Are you sure you want to delete this incident?')" 
                         class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-150">
                        <i class="fas fa-trash mr-1"></i> Delete
                      </a>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
                <?php if (count($rows) === 0): ?>
                <tr>
                  <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                    No incidents found. Create a new incident to get started.
                  </td>
                </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </main>
</body>
</html>