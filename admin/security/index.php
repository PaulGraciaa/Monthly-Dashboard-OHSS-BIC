<?php
require_once '../auth.php';
require_once '../../config/database.php';
requireAdminLogin();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Management - OHSS Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .tab-btn.active { background-color: #1f2937; }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <div class="min-h-screen">
        <header class="bg-red-600 text-white px-6 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">Security Management</h1>
                <a href="../dashboard.php" class="bg-white text-red-600 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Dashboard
                </a>
            </div>
        </header>
        <div class="container mx-auto px-6 py-8">
            <div class="mb-6 flex gap-2">
                <button class="tab-btn bg-gray-800 text-white px-4 py-2 rounded" onclick="showTab('personnel')">
                    <i class="fas fa-users-cog mr-2"></i>Personnel
                </button>
                <button class="tab-btn bg-gray-800 text-white px-4 py-2 rounded" onclick="showTab('gallery')">
                    <i class="fas fa-images mr-2"></i>Gallery
                </button>
            </div>
            <div id="personnel" class="tab-content active">
                <?php include 'security_management.php'; ?>
            </div>
            <div id="gallery" class="tab-content">
                <?php include 'add_security_gallery.php'; ?>
            </div>
        </div>
    </div>
    <script>
        function showTab(tab) {
            var tabs = document.querySelectorAll('.tab-content');
            for (var i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove('active');
            }
            
            var buttons = document.querySelectorAll('.tab-btn');
            for (var i = 0; i < buttons.length; i++) {
                buttons[i].classList.remove('active');
            }
            
            document.getElementById(tab).classList.add('active');
            var indexMap = { personnel: 0, gallery: 1 };
            var buttons = document.querySelectorAll('.tab-btn');
            var idx = indexMap[tab];
            if (buttons[idx]) buttons[idx].classList.add('active');
        }
        // default
        showTab('personnel');
    </script>
</body>
</html>