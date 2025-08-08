<?php
session_start();

// Fungsi untuk mengecek apakah admin sudah login
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && 
           $_SESSION['admin_logged_in'] === true && 
           isset($_SESSION['admin_id']) && 
           isset($_SESSION['admin_username']);
}

// Fungsi untuk memaksa login jika belum login
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Fungsi untuk redirect ke dashboard jika sudah login
function redirectIfLoggedIn() {
    if (isAdminLoggedIn()) {
        header('Location: dashboard.php');
        exit();
    }
}

// Fungsi untuk mendapatkan data admin yang sedang login
function getCurrentAdmin() {
    if (isAdminLoggedIn()) {
        return [
            'id' => $_SESSION['admin_id'],
            'username' => $_SESSION['admin_username'],
            'role' => $_SESSION['admin_role'] ?? 'admin',
            'name' => $_SESSION['admin_name'] ?? $_SESSION['admin_username']
        ];
    }
    return null;
}

// Fungsi untuk logout
function adminLogout() {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}
?> 