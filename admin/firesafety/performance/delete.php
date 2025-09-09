<?php
session_start();
require_once '../../auth.php';

// Pastikan user sudah login
if (!isLoggedIn()) {
    header('Location: ../../login.php');
    exit();
}

require_once '../../../config/database.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: ../index.php');
    exit();
}

// Hapus data (soft delete - set is_active = 0)
$query = "UPDATE fire_safety_performance SET is_active = 0 WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success_message'] = 'Data berhasil dihapus';
} else {
    $_SESSION['error_message'] = 'Gagal menghapus data: ' . mysqli_error($conn);
}

mysqli_stmt_close($stmt);
header('Location: ../index.php');
exit();
?>
