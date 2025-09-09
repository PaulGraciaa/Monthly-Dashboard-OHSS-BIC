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
$query = "SELECT * FROM fire_safety_emergency_details WHERE id = ?";
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
    $serial_number = (int)$_POST['serial_number'];
    $incident_date = $_POST['incident_date'];
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $sub_category = mysqli_real_escape_string($conn, $_POST['sub_category']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $display_order = (int)$_POST['display_order'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (empty($category) || empty($sub_category) || empty($description) || empty($location)) {
        $error = 'Semua field wajib diisi';
    } else {
        $query = "UPDATE fire_safety_emergency_details SET serial_number = ?, incident_date = ?, category = ?, sub_category = ?, description = ?, location = ?, display_order = ?, is_active = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "isssssiii", $serial_number, $incident_date, $category, $sub_category, $description, $location, $display_order, $is_active, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = 'Data berhasil diperbarui';
            // Update data yang ditampilkan
            $data['serial_number'] = $serial_number;
            $data['incident_date'] = $incident_date;
            $data['category'] = $category;
            $data['sub_category'] = $sub_category;
            $data['description'] = $description;
            $data['location'] = $location;
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
    <title>Edit Emergency Details</title>
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
                    <h1 class="h2">Edit Emergency Details</h1>
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
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="serial_number" class="form-label">Serial Number <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="serial_number" name="serial_number" value="<?= $data['serial_number'] ?>" required min="1">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="incident_date" class="form-label">Incident Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="incident_date" name="incident_date" value="<?= $data['incident_date'] ?>" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="">Pilih Category</option>
                                        <option value="Fire Incident" <?= ($data['category'] == 'Fire Incident') ? 'selected' : '' ?>>Fire Incident</option>
                                        <option value="Non-Rescue" <?= ($data['category'] == 'Non-Rescue') ? 'selected' : '' ?>>Non-Rescue</option>
                                        <option value="Technical Call" <?= ($data['category'] == 'Technical Call') ? 'selected' : '' ?>>Technical Call</option>
                                        <option value="Fire Call" <?= ($data['category'] == 'Fire Call') ? 'selected' : '' ?>>Fire Call</option>
                                        <option value="Operational Standby" <?= ($data['category'] == 'Operational Standby') ? 'selected' : '' ?>>Operational Standby</option>
                                        <option value="Spillage" <?= ($data['category'] == 'Spillage') ? 'selected' : '' ?>>Spillage</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="display_order" class="form-label">Display Order</label>
                                    <input type="number" class="form-control" id="display_order" name="display_order" value="<?= $data['display_order'] ?>" min="0">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="sub_category" class="form-label">Sub Category <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="sub_category" name="sub_category" value="<?= htmlspecialchars($data['sub_category']) ?>" required>
                                    <div class="form-text">Contoh: May Day Activity, Snake Catching, Hydrant Leakage, dll</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="location" name="location" value="<?= htmlspecialchars($data['location']) ?>" required>
                                    <div class="form-text">Lokasi kejadian</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($data['description']) ?></textarea>
                                <div class="form-text">Deskripsi detail kejadian</div>
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
