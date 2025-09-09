<?php
session_start();
require_once '../auth.php';

// Pastikan user sudah login
if (!isAdminLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

require_once '../../config/database.php';

// Ambil data Fire Safety Performance Summary
$query = "SELECT * FROM fire_safety_performance WHERE is_active = 1 ORDER BY display_order ASC";
$result = mysqli_query($conn, $query);
$performance_data = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Ambil data Emergency Activation
$query_emergency = "SELECT * FROM fire_safety_emergency_activation WHERE is_active = 1 ORDER BY display_order ASC";
$result_emergency = mysqli_query($conn, $query_emergency);
$emergency_data = mysqli_fetch_all($result_emergency, MYSQLI_ASSOC);

// Ambil data Emergency Details
$query_details = "SELECT * FROM fire_safety_emergency_details WHERE is_active = 1 ORDER BY display_order ASC";
$result_details = mysqli_query($conn, $query_details);
$details_data = mysqli_fetch_all($result_details, MYSQLI_ASSOC);

// Ambil data Fire Safety Enforcement
$query_enforcement = "SELECT * FROM fire_safety_enforcement WHERE is_active = 1 ORDER BY display_order ASC";
$result_enforcement = mysqli_query($conn, $query_enforcement);
$enforcement_data = mysqli_fetch_all($result_enforcement, MYSQLI_ASSOC);

// Ambil data Fire Equipment Maintenance
$query_maintenance = "SELECT * FROM fire_equipment_maintenance WHERE is_active = 1 ORDER BY display_order ASC";
$result_maintenance = mysqli_query($conn, $query_maintenance);
$maintenance_data = mysqli_fetch_all($result_maintenance, MYSQLI_ASSOC);

// Ambil data Fire Equipment Statistics
$query_statistics = "SELECT * FROM fire_equipment_statistics WHERE is_active = 1 ORDER BY display_order ASC";
$result_statistics = mysqli_query($conn, $query_statistics);
$statistics_data = mysqli_fetch_all($result_statistics, MYSQLI_ASSOC);

// Ambil data Fire Safety Repair Impairment
$query_repair = "SELECT * FROM fire_safety_repair_impairment WHERE is_active = 1 ORDER BY display_order ASC";
$result_repair = mysqli_query($conn, $query_repair);
$repair_data = mysqli_fetch_all($result_repair, MYSQLI_ASSOC);

// Ambil data Fire Safety Repair Details
$query_repair_details = "SELECT * FROM fire_safety_repair_details WHERE is_active = 1 ORDER BY display_order ASC";
$result_repair_details = mysqli_query($conn, $query_repair_details);
$repair_details_data = mysqli_fetch_all($result_repair_details, MYSQLI_ASSOC);

// Ambil data Fire Safety Drills
$query_drills = "SELECT * FROM fire_safety_drills WHERE is_active = 1 ORDER BY display_order ASC";
$result_drills = mysqli_query($conn, $query_drills);
$drills_data = mysqli_fetch_all($result_drills, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fire Safety Management - Admin</title>
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
                    <h1 class="h2">Fire Safety Management</h1>
                </div>

                <!-- Fire Safety Performance Summary -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            Fire Safety Performance Summary
                            <a href="performance/create.php" class="btn btn-sm btn-light float-end">
                                <i class="fas fa-plus"></i> Tambah
                            </a>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Summary Text</th>
                                        <th>Display Order</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($performance_data)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada data</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($performance_data as $index => $item): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= htmlspecialchars($item['summary_text']) ?></td>
                                                <td><?= $item['display_order'] ?></td>
                                                <td>
                                                    <a href="performance/edit.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="performance/delete.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                                        <i class="fas fa-trash"></i>
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

                <!-- Emergency Activation -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Emergency Activation
                            <a href="emergency/create.php" class="btn btn-sm btn-light float-end">
                                <i class="fas fa-plus"></i> Tambah
                            </a>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Jan</th>
                                        <th>Feb</th>
                                        <th>Mar</th>
                                        <th>Apr</th>
                                        <th>May</th>
                                        <th>Jun</th>
                                        <th>Total</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($emergency_data)): ?>
                                        <tr>
                                            <td colspan="9" class="text-center">Tidak ada data</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($emergency_data as $item): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($item['category']) ?></td>
                                                <td><?= $item['jan_value'] ?></td>
                                                <td><?= $item['feb_value'] ?></td>
                                                <td><?= $item['mar_value'] ?></td>
                                                <td><?= $item['apr_value'] ?></td>
                                                <td><?= $item['may_value'] ?></td>
                                                <td><?= $item['jun_value'] ?></td>
                                                <td><strong><?= $item['grand_total'] ?></strong></td>
                                                <td>
                                                    <a href="emergency/edit.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="emergency/delete.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                                        <i class="fas fa-trash"></i>
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

                <!-- Emergency Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Emergency Details
                            <a href="details/create.php" class="btn btn-sm btn-light float-end">
                                <i class="fas fa-plus"></i> Tambah
                            </a>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Date</th>
                                        <th>Category</th>
                                        <th>Sub Category</th>
                                        <th>Description</th>
                                        <th>Location</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($details_data)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada data</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($details_data as $item): ?>
                                            <tr>
                                                <td><?= $item['serial_number'] ?></td>
                                                <td><?= date('d-M-y', strtotime($item['incident_date'])) ?></td>
                                                <td><?= htmlspecialchars($item['category']) ?></td>
                                                <td><?= htmlspecialchars($item['sub_category']) ?></td>
                                                <td><?= htmlspecialchars(substr($item['description'], 0, 50)) ?>...</td>
                                                <td><?= htmlspecialchars($item['location']) ?></td>
                                                <td>
                                                    <a href="details/edit.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="details/delete.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                                        <i class="fas fa-trash"></i>
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

                <!-- Fire Safety Enforcement -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-gavel me-2"></i>
                            Fire Safety Enforcement
                            <a href="enforcement/create.php" class="btn btn-sm btn-light float-end">
                                <i class="fas fa-plus"></i> Tambah
                            </a>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Premises Count</th>
                                        <th>Non-Compliance Count</th>
                                        <th>Year</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($enforcement_data)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada data</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($enforcement_data as $item): ?>
                                            <tr>
                                                <td><?= $item['month_name'] ?></td>
                                                <td><?= $item['premises_count'] ?></td>
                                                <td><?= $item['non_compliance_count'] ?></td>
                                                <td><?= $item['year'] ?></td>
                                                <td>
                                                    <a href="enforcement/edit.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="enforcement/delete.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                                        <i class="fas fa-trash"></i>
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

                <!-- Fire Equipment Maintenance -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-tools me-2"></i>
                            Fire Equipment Maintenance
                            <a href="maintenance/create.php" class="btn btn-sm btn-light float-end">
                                <i class="fas fa-plus"></i> Tambah
                            </a>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Date</th>
                                        <th>Location</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($maintenance_data)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada data</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($maintenance_data as $item): ?>
                                            <tr>
                                                <td><?= $item['serial_number'] ?></td>
                                                <td><?= date('d-M-y', strtotime($item['maintenance_date'])) ?></td>
                                                <td><?= htmlspecialchars($item['location']) ?></td>
                                                <td>
                                                    <a href="maintenance/edit.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="maintenance/delete.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                                        <i class="fas fa-trash"></i>
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

                <!-- Fire Equipment Statistics -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>
                            Fire Equipment Statistics
                            <a href="statistics/create.php" class="btn btn-sm btn-light float-end">
                                <i class="fas fa-plus"></i> Tambah
                            </a>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Equipment Type</th>
                                        <th>Jan</th>
                                        <th>Feb</th>
                                        <th>Mar</th>
                                        <th>Apr</th>
                                        <th>May</th>
                                        <th>Jun</th>
                                        <th>Total</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($statistics_data)): ?>
                                        <tr>
                                            <td colspan="9" class="text-center">Tidak ada data</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($statistics_data as $item): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($item['equipment_type']) ?></td>
                                                <td><?= $item['jan_count'] ?></td>
                                                <td><?= $item['feb_count'] ?></td>
                                                <td><?= $item['mar_count'] ?></td>
                                                <td><?= $item['apr_count'] ?></td>
                                                <td><?= $item['may_count'] ?></td>
                                                <td><?= $item['jun_count'] ?></td>
                                                <td><strong><?= $item['grand_total'] ?></strong></td>
                                                <td>
                                                    <a href="statistics/edit.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="statistics/delete.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                                        <i class="fas fa-trash"></i>
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

                <!-- Fire Safety Repair Impairment -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-wrench me-2"></i>
                            Fire Safety Repair Impairment
                            <a href="repair/create.php" class="btn btn-sm btn-light float-end">
                                <i class="fas fa-plus"></i> Tambah
                            </a>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Repair Count</th>
                                        <th>Year</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($repair_data)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada data</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($repair_data as $item): ?>
                                            <tr>
                                                <td><?= $item['month_name'] ?></td>
                                                <td><?= $item['repair_count'] ?></td>
                                                <td><?= $item['year'] ?></td>
                                                <td>
                                                    <a href="repair/edit.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="repair/delete.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                                        <i class="fas fa-trash"></i>
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

                <!-- Fire Safety Repair Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list-alt me-2"></i>
                            Fire Safety Repair Details
                            <a href="repair_details/create.php" class="btn btn-sm btn-light float-end">
                                <i class="fas fa-plus"></i> Tambah
                            </a>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>S/No</th>
                                        <th>Date</th>
                                        <th>Project Name</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($repair_details_data)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Tidak ada data</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($repair_details_data as $item): ?>
                                            <tr>
                                                <td><?= $item['serial_number'] ?></td>
                                                <td><?= date('d-M-y', strtotime($item['repair_date'])) ?></td>
                                                <td><?= htmlspecialchars(substr($item['project_name'], 0, 50)) ?>...</td>
                                                <td><?= htmlspecialchars($item['location']) ?></td>
                                                <td><?= htmlspecialchars(substr($item['status'], 0, 30)) ?>...</td>
                                                <td>
                                                    <a href="repair_details/edit.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="repair_details/delete.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                                        <i class="fas fa-trash"></i>
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

                <!-- Fire Safety Drills -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-running me-2"></i>
                            Fire Safety Drills & Training
                            <a href="drills/create.php" class="btn btn-sm btn-light float-end">
                                <i class="fas fa-plus"></i> Tambah
                            </a>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>S/No</th>
                                        <th>Date</th>
                                        <th>Location</th>
                                        <th>Subject</th>
                                        <th>Type</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($drills_data)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Tidak ada data</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($drills_data as $item): ?>
                                            <tr>
                                                <td><?= $item['serial_number'] ?></td>
                                                <td><?= date('d-M-y', strtotime($item['drill_date'])) ?></td>
                                                <td><?= htmlspecialchars($item['location']) ?></td>
                                                <td><?= htmlspecialchars($item['subject']) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $item['drill_type'] == 'drill' ? 'primary' : 'success' ?>">
                                                        <?= ucfirst($item['drill_type']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="drills/edit.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="drills/delete.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                                        <i class="fas fa-trash"></i>
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
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
