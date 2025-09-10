<?php
session_start();
require_once '../auth.php';

// Pastikan user sudah login
if (!isAdminLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

require_once '../../config/database.php';

$error = '';
$success = '';
$data = null;
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle different actions
switch ($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $equipment_type = mysqli_real_escape_string($conn, $_POST['equipment_type']);
            $jan_count = (int)$_POST['jan_count'];
            $feb_count = (int)$_POST['feb_count'];
            $mar_count = (int)$_POST['mar_count'];
            $apr_count = (int)$_POST['apr_count'];
            $may_count = (int)$_POST['may_count'];
            $jun_count = (int)$_POST['jun_count'];
            $jul_count = (int)$_POST['jul_count'];
            $aug_count = (int)$_POST['aug_count'];
            $sep_count = (int)$_POST['sep_count'];
            $oct_count = (int)$_POST['oct_count'];
            $nov_count = (int)$_POST['nov_count'];
            $dec_count = (int)$_POST['dec_count'];
            $display_order = (int)$_POST['display_order'];
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            $created_by = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

            // Hitung grand total
            $grand_total = $jan_count + $feb_count + $mar_count + $apr_count + $may_count + $jun_count + 
                           $jul_count + $aug_count + $sep_count + $oct_count + $nov_count + $dec_count;

            if (empty($equipment_type)) {
                $error = 'Equipment Type tidak boleh kosong';
            } else {
                // created_by not present in table schema; do not insert it
                $query = "INSERT INTO fire_equipment_statistics (equipment_type, jan_count, feb_count, mar_count, apr_count, may_count, jun_count, jul_count, aug_count, sep_count, oct_count, nov_count, dec_count, grand_total, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "siiiiiiiiiiiiii", $equipment_type, $jan_count, $feb_count, $mar_count, $apr_count, $may_count, $jun_count, $jul_count, $aug_count, $sep_count, $oct_count, $nov_count, $dec_count, $grand_total, $display_order, $is_active);
                
                if (mysqli_stmt_execute($stmt)) {
                    if (!isset($_SESSION)) { session_start(); }
                    $_SESSION['notif'] = 'Data berhasil ditambahkan';
                    header('Location: index.php');
                    exit();
                } else {
                    $error = 'Gagal menambahkan data: ' . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            }
        }
        break;

    case 'edit':
        if ($id <= 0) {
            header('Location: index.php');
            exit();
        }

        // Ambil data berdasarkan ID
        $query = "SELECT * FROM fire_equipment_statistics WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$data) {
            header('Location: index.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $equipment_type = mysqli_real_escape_string($conn, $_POST['equipment_type']);
            $jan_count = (int)$_POST['jan_count'];
            $feb_count = (int)$_POST['feb_count'];
            $mar_count = (int)$_POST['mar_count'];
            $apr_count = (int)$_POST['apr_count'];
            $may_count = (int)$_POST['may_count'];
            $jun_count = (int)$_POST['jun_count'];
            $jul_count = (int)$_POST['jul_count'];
            $aug_count = (int)$_POST['aug_count'];
            $sep_count = (int)$_POST['sep_count'];
            $oct_count = (int)$_POST['oct_count'];
            $nov_count = (int)$_POST['nov_count'];
            $dec_count = (int)$_POST['dec_count'];
            $display_order = (int)$_POST['display_order'];
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            // Hitung grand total
            $grand_total = $jan_count + $feb_count + $mar_count + $apr_count + $may_count + $jun_count + 
                           $jul_count + $aug_count + $sep_count + $oct_count + $nov_count + $dec_count;

            if (empty($equipment_type)) {
                $error = 'Equipment Type tidak boleh kosong';
            } else {
                $query = "UPDATE fire_equipment_statistics SET equipment_type = ?, jan_count = ?, feb_count = ?, mar_count = ?, apr_count = ?, may_count = ?, jun_count = ?, jul_count = ?, aug_count = ?, sep_count = ?, oct_count = ?, nov_count = ?, dec_count = ?, grand_total = ?, display_order = ?, is_active = ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "siiiiiiiiiiiiiiii", $equipment_type, $jan_count, $feb_count, $mar_count, $apr_count, $may_count, $jun_count, $jul_count, $aug_count, $sep_count, $oct_count, $nov_count, $dec_count, $grand_total, $display_order, $is_active, $id);
                
                if (mysqli_stmt_execute($stmt)) {
                    $success = 'Data berhasil diperbarui';
                    // Update data yang ditampilkan
                    $data['equipment_type'] = $equipment_type;
                    $data['jan_count'] = $jan_count;
                    $data['feb_count'] = $feb_count;
                    $data['mar_count'] = $mar_count;
                    $data['apr_count'] = $apr_count;
                    $data['may_count'] = $may_count;
                    $data['jun_count'] = $jun_count;
                    $data['jul_count'] = $jul_count;
                    $data['aug_count'] = $aug_count;
                    $data['sep_count'] = $sep_count;
                    $data['oct_count'] = $oct_count;
                    $data['nov_count'] = $nov_count;
                    $data['dec_count'] = $dec_count;
                    $data['grand_total'] = $grand_total;
                    $data['display_order'] = $display_order;
                    $data['is_active'] = $is_active;
                } else {
                    $error = 'Gagal memperbarui data: ' . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            }
        }
        break;

    case 'delete':
        if ($id <= 0) {
            header('Location: index.php');
            exit();
        }

        // Hapus data (soft delete - set is_active = 0)
        $query = "UPDATE fire_equipment_statistics SET is_active = 0 WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = 'Data berhasil dihapus';
        } else {
            $_SESSION['error_message'] = 'Gagal menghapus data: ' . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
        header('Location: index.php');
        exit();
        break;

    default:
        // Default action is 'list' - redirect to main index
        header('Location: index.php');
        exit();
}

// Determine page title based on action
$page_title = ($action == 'create') ? 'Tambah' : 'Edit';
$page_title .= ' Fire Equipment Statistics';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #0A4D9E;
        }
        .sidebar .nav-link {
            color: white;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
        }
        .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.2);
        }
        .main-content {
            margin-left: 0;
        }
        @media (min-width: 768px) {
            .main-content {
                margin-left: 250px;
            }
        }
        .card-header {
            background-color: #0A4D9E;
            color: white;
        }
        .btn-primary {
            background-color: #0A4D9E;
            border-color: #0A4D9E;
        }
        .btn-primary:hover {
            background-color: #083a7a;
            border-color: #083a7a;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h5 class="text-white">Admin Panel</h5>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="../dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../management/activities_tab.php">
                                <i class="fas fa-calendar me-2"></i>
                                Activities
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../OHS/index.php">
                                <i class="fas fa-shield-alt me-2"></i>
                                OHS
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../security/index.php">
                                <i class="fas fa-user-shield me-2"></i>
                                Security
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php">
                                <i class="fas fa-fire-extinguisher me-2"></i>
                                Fire Safety
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../surveillance/index.php">
                                <i class="fas fa-video me-2"></i>
                                Surveillance
                            </a>
                        </li>
                        <li class="nav-item mt-3">
                            <a class="nav-link" href="../logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?php echo $page_title; ?></h1>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-<?php echo ($action == 'create') ? 'plus' : 'edit'; ?> me-2"></i>
                            Form <?php echo ($action == 'create') ? 'Tambah' : 'Edit'; ?> Data
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="equipment_type" class="form-label">Equipment Type <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="equipment_type" name="equipment_type" 
                                           value="<?php echo ($action == 'edit') ? htmlspecialchars($data['equipment_type']) : (isset($_POST['equipment_type']) ? htmlspecialchars($_POST['equipment_type']) : ''); ?>" 
                                           required>
                                    <div class="form-text">Contoh: Fire Extinguisher, Hose Reel, dll</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="display_order" class="form-label">Display Order</label>
                                    <input type="number" class="form-control" id="display_order" name="display_order" 
                                           value="<?php echo ($action == 'edit') ? $data['display_order'] : (isset($_POST['display_order']) ? $_POST['display_order'] : '0'); ?>" 
                                           min="0">
                                    <div class="form-text">Urutan tampil data (0 = paling atas)</div>
                                </div>
                            </div>

                            <h6 class="mt-4 mb-3">Monthly Count 2025</h6>
                            <div class="row">
                                <div class="col-md-2 mb-3">
                                    <label for="jan_count" class="form-label">January</label>
                                    <input type="number" class="form-control" id="jan_count" name="jan_count" 
                                           value="<?php echo ($action == 'edit') ? $data['jan_count'] : (isset($_POST['jan_count']) ? $_POST['jan_count'] : '0'); ?>" 
                                           min="0">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="feb_count" class="form-label">February</label>
                                    <input type="number" class="form-control" id="feb_count" name="feb_count" 
                                           value="<?php echo ($action == 'edit') ? $data['feb_count'] : (isset($_POST['feb_count']) ? $_POST['feb_count'] : '0'); ?>" 
                                           min="0">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="mar_count" class="form-label">March</label>
                                    <input type="number" class="form-control" id="mar_count" name="mar_count" 
                                           value="<?php echo ($action == 'edit') ? $data['mar_count'] : (isset($_POST['mar_count']) ? $_POST['mar_count'] : '0'); ?>" 
                                           min="0">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="apr_count" class="form-label">April</label>
                                    <input type="number" class="form-control" id="apr_count" name="apr_count" 
                                           value="<?php echo ($action == 'edit') ? $data['apr_count'] : (isset($_POST['apr_count']) ? $_POST['apr_count'] : '0'); ?>" 
                                           min="0">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="may_count" class="form-label">May</label>
                                    <input type="number" class="form-control" id="may_count" name="may_count" 
                                           value="<?php echo ($action == 'edit') ? $data['may_count'] : (isset($_POST['may_count']) ? $_POST['may_count'] : '0'); ?>" 
                                           min="0">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="jun_count" class="form-label">June</label>
                                    <input type="number" class="form-control" id="jun_count" name="jun_count" 
                                           value="<?php echo ($action == 'edit') ? $data['jun_count'] : (isset($_POST['jun_count']) ? $_POST['jun_count'] : '0'); ?>" 
                                           min="0">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2 mb-3">
                                    <label for="jul_count" class="form-label">July</label>
                                    <input type="number" class="form-control" id="jul_count" name="jul_count" 
                                           value="<?php echo ($action == 'edit') ? $data['jul_count'] : (isset($_POST['jul_count']) ? $_POST['jul_count'] : '0'); ?>" 
                                           min="0">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="aug_count" class="form-label">August</label>
                                    <input type="number" class="form-control" id="aug_count" name="aug_count" 
                                           value="<?php echo ($action == 'edit') ? $data['aug_count'] : (isset($_POST['aug_count']) ? $_POST['aug_count'] : '0'); ?>" 
                                           min="0">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="sep_count" class="form-label">September</label>
                                    <input type="number" class="form-control" id="sep_count" name="sep_count" 
                                           value="<?php echo ($action == 'edit') ? $data['sep_count'] : (isset($_POST['sep_count']) ? $_POST['sep_count'] : '0'); ?>" 
                                           min="0">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="oct_count" class="form-label">October</label>
                                    <input type="number" class="form-control" id="oct_count" name="oct_count" 
                                           value="<?php echo ($action == 'edit') ? $data['oct_count'] : (isset($_POST['oct_count']) ? $_POST['oct_count'] : '0'); ?>" 
                                           min="0">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="nov_count" class="form-label">November</label>
                                    <input type="number" class="form-control" id="nov_count" name="nov_count" 
                                           value="<?php echo ($action == 'edit') ? $data['nov_count'] : (isset($_POST['nov_count']) ? $_POST['nov_count'] : '0'); ?>" 
                                           min="0">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="dec_count" class="form-label">December</label>
                                    <input type="number" class="form-control" id="dec_count" name="dec_count" 
                                           value="<?php echo ($action == 'edit') ? $data['dec_count'] : (isset($_POST['dec_count']) ? $_POST['dec_count'] : '0'); ?>" 
                                           min="0">
                                </div>
                            </div>

                            <?php if ($action == 'edit'): ?>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Grand Total</label>
                                    <input type="text" class="form-control" value="<?php echo $data['grand_total']; ?>" readonly>
                                    <div class="form-text">Total otomatis dihitung dari semua bulan</div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                       <?php echo (($action == 'edit' && $data['is_active']) || ($action == 'create' && (!isset($_POST['is_active']) || $_POST['is_active']))) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_active">
                                    Aktif
                                </label>
                                <div class="form-text">Centang untuk menampilkan data ini</div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="index.php" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-times me-2"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i><?php echo ($action == 'create') ? 'Simpan' : 'Simpan Perubahan'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
