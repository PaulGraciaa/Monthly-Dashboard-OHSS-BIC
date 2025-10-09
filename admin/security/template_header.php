<?php
// session_start() akan dipanggil di setiap file utama (personnel.php, gallery.php, dll)
// Ini adalah praktik yang lebih baik untuk memastikannya dipanggil di awal.
require_once '../auth.php';
require_once '../../config/database.php';
requireAdminLogin();

// Fungsi sanitasi sederhana akan digunakan dari sini
if (!function_exists('sanitize')) {
    function sanitize($data) {
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? sanitize($page_title) : 'Security Management'; ?> - OHSS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FF0000',
                        secondary: '#1a1a1a',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="bg-gradient-to-r from-red-600 to-red-800">
        <header class="text-white py-2">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-4">
                        <img src="../../img/batamindo.png" alt="Batamindo" class="h-10 w-auto bg-white p-1 rounded">
                        <div>
                            <h1 class="text-xl font-bold text-white leading-tight">Batamindo Industrial Park</h1>
                            <p class="text-red-200 text-sm leading-tight">OHS Security System Management</p>
                        </div>
                    </div>
                    <div class="hidden md:flex items-center space-x-3">
                        <div class="text-right">
                            <p class="text-sm text-white">Welcome, Admin</p>
                            <p class="text-xs text-red-200"><?php echo date('l, d F Y'); ?></p>
                        </div>
                        <a href="../logout.php" class="bg-white hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors duration-150">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <div class="border-t border-red-500/30">
            <div class="max-w-7xl mx-auto px-4 py-1">
                <nav class="flex space-x-2">
                    <a href="../dashboard.php" class="text-red-100 hover:text-white px-2.5 py-1.5 rounded-md text-sm font-medium">
                        <i class="fas fa-chart-line mr-1"></i> Dashboard
                    </a>
                    <a href="gallery.php" class="text-red-100 hover:text-white px-2.5 py-1.5 rounded-md text-sm font-medium">
                        <i class="fas fa-images mr-1"></i> Security Gallery
                    </a>
                    <a href="personnel.php" class="text-red-100 hover:text-white px-2.5 py-1.5 rounded-md text-sm font-medium">
                        <i class="fas fa-users mr-1"></i> Personnel
                    </a>
                    <a href="incident_lesson.php" class="text-red-100 hover:text-white px-2.5 py-1.5 rounded-md text-sm font-medium">
                        <i class="fas fa-book mr-1"></i> Incident & Lessons
                    </a>
                </nav>
            </div>
        </div>
    </div>
    <div class="container mx-auto px-4 py-6">
        <?php // Menghapus 'if (!isset($_SESSION)) { session_start(); }' dari sini. ?>
        <?php if (!empty($_SESSION['notif'])): ?>
        <style>
        @keyframes notifSlideIn {
            0% { opacity: 0; transform: translateY(-30px) scale(0.95); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes notifFadeOut {
            to { opacity: 0; transform: translateY(-10px) scale(0.98); }
        }
        .notif-animate-in { animation: notifSlideIn 0.5s cubic-bezier(.4,0,.2,1); }
        .notif-animate-out { animation: notifFadeOut 0.5s cubic-bezier(.4,0,.2,1) forwards; }
        </style>
        <div id="notifBox" class="fixed top-8 right-8 z-50 min-w-[260px] max-w-xs bg-white border border-green-400 shadow-2xl rounded-xl flex items-center px-5 py-4 gap-3 notif-animate-in" style="box-shadow:0 8px 32px 0 rgba(34,197,94,0.15);">
            <div class="flex-shrink-0">
                <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-green-100">
                    <i class="fas fa-check text-green-600 text-xl"></i>
                </span>
            </div>
            <div class="flex-1 text-green-800 font-semibold text-sm">
                <?php echo $_SESSION['notif']; unset($_SESSION['notif']); ?>
            </div>
            <button onclick="closeNotif()" class="ml-2 text-green-400 hover:text-green-700 focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <script>
        function closeNotif() {
            var notif = document.getElementById('notifBox');
            if (notif) {
                notif.classList.remove('notif-animate-in');
                notif.classList.add('notif-animate-out');
                setTimeout(function() {
                    if (notif.parentNode) {
                        notif.parentNode.removeChild(notif);
                    }
                }, 500);
            }
        }
        setTimeout(function() {
            closeNotif();
        }, 5000);
        </script>
        <?php endif; ?>