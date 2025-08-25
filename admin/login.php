<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true && 
    isset($_SESSION['admin_id']) && isset($_SESSION['admin_username'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ? AND is_active = 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_role'] = $user['role'];
                $_SESSION['admin_name'] = $user['full_name'];
                
                // Update last login
                $updateStmt = $pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                $updateStmt->execute([$user['id']]);
                
                // Redirect dengan header langsung
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Username atau password salah!';
            }
        } catch (Exception $e) {
            $error = 'Error database: ' . $e->getMessage();
        }
    } else {
        $error = 'Silakan isi username dan password!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - OHSS Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#dc2626',
                        'primary-dark': '#991b1b',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.5s ease-out',
                        'pulse-slow': 'pulse 3s infinite',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                    },
                },
            },
        };
    </script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        }
        .glass-effect {
            backdrop-filter: blur(16px) saturate(180%);
            background-color: rgba(255, 255, 255, 0.9);
        }
    </style>
</head>
<body class="min-h-screen font-sans bg-gray-50 overflow-hidden">
    <!-- Background Pattern -->
    <div class="fixed inset-0 z-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23000000\' fill-opacity=\'0.1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
        </div>
    </div>

    <div class="min-h-screen flex items-center justify-center px-4 relative z-10">
        <div class="max-w-md w-full animate-fade-in-up">
            <div class="glass-effect rounded-2xl shadow-2xl p-8 border border-white/20">
                <!-- Logo and Header -->
                <div class="text-center mb-10">
                    <div class="relative w-28 h-28 mx-auto mb-6">
                        <div class="absolute inset-0 bg-red-100 rounded-2xl animate-pulse-slow"></div>
                        <img src="../img/batamindo.png" alt="Batamindo Logo" class="relative w-full h-full object-contain p-4">
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Welcome Back</h2>
                    <p class="text-gray-500">OHS Security System Management</p>
                </div>

                <!-- Error Message -->
                <?php if ($error): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 animate-fade-in-up">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                            <p class="text-sm"><?php echo $error; ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form method="POST" class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user text-primary mr-2"></i>Username
                        </label>
                        <div class="relative">
                            <input type="text" id="username" name="username" required
                                   class="w-full px-4 py-3 pl-12 rounded-xl border border-gray-200 bg-white/70 text-gray-700 
                                   placeholder-gray-400 transition-all duration-200 focus:border-primary focus:ring-2 
                                   focus:ring-primary/20 focus:bg-white"
                                   placeholder="Enter your username">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <i class="fas fa-user-circle text-primary/80"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock text-primary mr-2"></i>Password
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                   class="w-full px-4 py-3 pl-12 rounded-xl border border-gray-200 bg-white/70 text-gray-700 
                                   placeholder-gray-400 transition-all duration-200 focus:border-primary focus:ring-2 
                                   focus:ring-primary/20 focus:bg-white"
                                   placeholder="Enter your password">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <i class="fas fa-key text-primary/80"></i>
                            </div>
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full gradient-bg text-white font-medium py-3.5 px-4 rounded-xl shadow-lg hover:shadow-xl 
                            transition-all duration-300 transform hover:-translate-y-0.5 flex items-center justify-center space-x-2 mt-8">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Sign In to Dashboard</span>
                    </button>
                </form>

                <!-- Back to Dashboard Link -->
                <div class="text-center mt-8">
                    <a href="../index.php" 
                       class="inline-flex items-center text-gray-500 hover:text-primary transition-colors duration-200 text-sm">
                        <i class="fas fa-arrow-left mr-2"></i>
                        <span>Back to Dashboard</span>
                    </a>
                </div>

                <!-- Information Card -->
                <div class="mt-8 p-4 bg-gray-50/50 rounded-xl border border-gray-100">
                    <div class="flex items-center text-gray-600 mb-2">
                        <i class="fas fa-info-circle text-primary mr-2"></i>
                        <h4 class="text-sm font-medium">Default Credentials</h4>
                    </div>
                    <div class="text-sm text-gray-500 space-y-1">
                        <p><span class="font-medium">Username:</span> admin</p>
                        <p><span class="font-medium">Password:</span> admin123</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-8 text-gray-400 text-sm">
                <p>&copy; <?php echo date('Y'); ?> Batamindo Investment Cakrawala. All rights reserved</p>
            </div>
        </div>
    </div>
</body>
</html>