<?php
session_start();
require_once '../../auth.php';

// Pastikan user sudah login
if (!isLoggedIn()) {
    header('Location: ../../login.php');
    exit();
}

require_once '../../../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $jan_value = (int)$_POST['jan_value'];
    $feb_value = (int)$_POST['feb_value'];
    $mar_value = (int)$_POST['mar_value'];
    $apr_value = (int)$_POST['apr_value'];
    $may_value = (int)$_POST['may_value'];
    $jun_value = (int)$_POST['jun_value'];
    $jul_value = (int)$_POST['jul_value'];
    $aug_value = (int)$_POST['aug_value'];
    $sep_value = (int)$_POST['sep_value'];
    $oct_value = (int)$_POST['oct_value'];
    $nov_value = (int)$_POST['nov_value'];
    $dec_value = (int)$_POST['dec_value'];
    $display_order = (int)$_POST['display_order'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $created_by = $_SESSION['user_id'];

    // Hitung grand total
    $grand_total = $jan_value + $feb_value + $mar_value + $apr_value + $may_value + $jun_value + 
                   $jul_value + $aug_value + $sep_value + $oct_value + $nov_value + $dec_value;

    if (empty($category)) {
        $error = 'Category tidak boleh kosong';
    } else {
        $query = "INSERT INTO fire_safety_emergency_activation (category, jan_value, feb_value, mar_value, apr_value, may_value, jun_value, jul_value, aug_value, sep_value, oct_value, nov_value, dec_value, grand_total, display_order, is_active, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "siiiiiiiiiiiiiiii", $category, $jan_value, $feb_value, $mar_value, $apr_value, $may_value, $jun_value, $jul_value, $aug_value, $sep_value, $oct_value, $nov_value, $dec_value, $grand_total, $display_order, $is_active, $created_by);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = 'Data berhasil ditambahkan';
            header('Location: ../index.php');
            exit();
        } else {
            $error = 'Gagal menambahkan data: ' . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Emergency Activation</title>
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
                            <a class="nav-link" href="../../dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../../management/activities_tab.php">
                                <i class="fas fa-calendar me-2"></i>
                                Activities
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../../OHS/index.php">
                                <i class="fas fa-shield-alt me-2"></i>
                                OHS
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../../security/index.php">
                                <i class="fas fa-user-shield me-2"></i>
                                Security
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="../index.php">
                                <i class="fas fa-fire-extinguisher me-2"></i>
                                Fire Safety
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../../surveillance/index.php">
                                <i class="fas fa-video me-2"></i>
                                Surveillance
                            </a>
                        </li>
                        <li class="nav-item mt-3">
                            <a class="nav-link" href="../../logout.php">
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
                    <h1 class="h2">Tambah Emergency Activation</h1>
                    <a href="../index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= $success ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-plus me-2"></i>
                            Form Tambah Data
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="category" name="category" value="<?= isset($_POST['category']) ? htmlspecialchars($_POST['category']) : '' ?>" required>
                                    <div class="form-text">Contoh: Fire Incident, Non-Rescue, Technical Call, dll</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="display_order" class="form-label">Display Order</label>
                                    <input type="number" class="form-control" id="display_order" name="display_order" value="<?= isset($_POST['display_order']) ? $_POST['display_order'] : '0' ?>" min="0">
                                    <div class="form-text">Urutan tampil data (0 = paling atas)</div>
                                </div>
                            </div>

                            <h6 class="mt-4 mb-3">Monthly Values 2025</h6>
                            <div class="row">
                                <div class="col-md-2 mb-3">
                                    <label for="jan_value" class="form-label">January</label>
                                    <input type="number" class="form-control" id="jan_value" name="jan_value" value="<?= isset($_POST['jan_value']) ? $_POST['jan_value'] : '0' ?>" min="0">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="feb_value" class="form-label">February</label>
                                    <input type="number" class="form-control" id="feb_value" name="feb_value" value="<?= isset($_POST['feb_value']) ? $_POST['feb_value'] : '0' ?>" min="0">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="mar_value" class="form-label">March</label>
                                    <input type="number" class="form-control" id="mar_value" name="mar_value" value="<?= isset($_POST['mar_value']) ? $_POST['mar_value'] : '0' ?>" min="0">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="apr_value" class="form-label">April</label>
                                    <input type="number" class="form-control" id="apr_value" name="apr_value" value="<?= isset($_POST['apr_value']) ? $_POST['apr_value'] : '0' ?>" min="0">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="may_value" class="form-label">May</label>
                                    <input type="number" class="form-control" id="may_value" name="may_value" value="<?= isset($_POST['may_value']) ? $_POST['may_value'] : '0' ?>" min="0">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="jun_value" class="form-label">June</label>
                                    <input type="number" class="form-control" id="jun_value" name="jun_value" value="<?= isset($_POST['jun_value']) ? $_POST['jun_value'] : '0' ?>" min="0">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2 mb-3">
                                    <label for="jul_value" class="form-label">July</label>
                                    <input type="number" class="form-control" id="jul_value" name="jul_value" value="<?= isset($_POST['jul_value']) ? $_POST['jul_value'] : '0' ?>" min="0">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="aug_value" class="form-label">August</label>
                                    <input type="number" class="form-control" id="aug_value" name="aug_value" value="<?= isset($_POST['aug_value']) ? $_POST['aug_value'] : '0' ?>" min="0">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="sep_value" class="form-label">September</label>
                                    <input type="number" class="form-control" id="sep_value" name="sep_value" value="<?= isset($_POST['sep_value']) ? $_POST['sep_value'] : '0' ?>" min="0">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="oct_value" class="form-label">October</label>
                                    <input type="number" class="form-control" id="oct_value" name="oct_value" value="<?= isset($_POST['oct_value']) ? $_POST['oct_value'] : '0' ?>" min="0">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="nov_value" class="form-label">November</label>
                                    <input type="number" class="form-control" id="nov_value" name="nov_value" value="<?= isset($_POST['nov_value']) ? $_POST['nov_value'] : '0' ?>" min="0">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="dec_value" class="form-label">December</label>
                                    <input type="number" class="form-control" id="dec_value" name="dec_value" value="<?= isset($_POST['dec_value']) ? $_POST['dec_value'] : '0' ?>" min="0">
                                </div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" <?= isset($_POST['is_active']) ? 'checked' : 'checked' ?>>
                                <label class="form-check-label" for="is_active">
                                    Aktif
                                </label>
                                <div class="form-text">Centang untuk menampilkan data ini</div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="../index.php" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-times me-2"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan
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
