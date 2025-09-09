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
$data = null;

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: ../index.php');
    exit();
}

// Ambil data berdasarkan ID
$query = "SELECT * FROM fire_safety_performance WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data) {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $summary_text = mysqli_real_escape_string($conn, $_POST['summary_text']);
    $display_order = (int)$_POST['display_order'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (empty($summary_text)) {
        $error = 'Summary text tidak boleh kosong';
    } else {
        $query = "UPDATE fire_safety_performance SET summary_text = ?, display_order = ?, is_active = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "siii", $summary_text, $display_order, $is_active, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = 'Data berhasil diperbarui';
            // Update data yang ditampilkan
            $data['summary_text'] = $summary_text;
            $data['display_order'] = $display_order;
            $data['is_active'] = $is_active;
        } else {
            $error = 'Gagal memperbarui data: ' . mysqli_error($conn);
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
    <title>Edit Fire Safety Performance Summary</title>
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
                    <h1 class="h2">Edit Fire Safety Performance Summary</h1>
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
                            <i class="fas fa-edit me-2"></i>
                            Form Edit Data
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="summary_text" class="form-label">Summary Text <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="summary_text" name="summary_text" rows="4" required><?= htmlspecialchars($data['summary_text']) ?></textarea>
                                <div class="form-text">Masukkan teks summary untuk Fire Safety Performance</div>
                            </div>

                            <div class="mb-3">
                                <label for="display_order" class="form-label">Display Order</label>
                                <input type="number" class="form-control" id="display_order" name="display_order" value="<?= $data['display_order'] ?>" min="0">
                                <div class="form-text">Urutan tampil data (0 = paling atas)</div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" <?= $data['is_active'] ? 'checked' : '' ?>>
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
                                    <i class="fas fa-save me-2"></i>Simpan Perubahan
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
