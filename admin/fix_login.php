<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Diagnosis Login System</h1>";

// 1. Cek koneksi database
echo "<h2>1. Testing Database Connection</h2>";
try {
    require_once '../config/database.php';
    echo "✅ Database connection successful<br>";
    echo "Database: " . DB_NAME . "<br>";
    echo "Host: " . DB_HOST . "<br>";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    exit;
}

// 2. Cek apakah tabel admin_users ada
echo "<h2>2. Checking admin_users table</h2>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'admin_users'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Table admin_users exists<br>";
    } else {
        echo "❌ Table admin_users does not exist<br>";
        echo "Creating table...<br>";
        
        $sql = "CREATE TABLE admin_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            role ENUM('super_admin', 'admin', 'editor') DEFAULT 'admin',
            is_active BOOLEAN DEFAULT TRUE,
            last_login DATETIME,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        $pdo->exec($sql);
        echo "✅ Table admin_users created successfully<br>";
    }
} catch (Exception $e) {
    echo "❌ Error checking/creating table: " . $e->getMessage() . "<br>";
}

// 3. Cek apakah ada data admin
echo "<h2>3. Checking admin data</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM admin_users");
    $result = $stmt->fetch();
    echo "Total admin users: " . $result['count'] . "<br>";
    
    if ($result['count'] == 0) {
        echo "❌ No admin users found. Creating default admin...<br>";
        
        // Create default admin with password: admin123
        $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO admin_users (username, password, email, full_name, role) VALUES 
                ('admin', ?, 'admin@batamindo.com', 'Administrator', 'super_admin')";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$password_hash]);
        
        echo "✅ Default admin created successfully<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
    } else {
        // Show existing admin users
        $stmt = $pdo->query("SELECT id, username, email, full_name, role, is_active FROM admin_users");
        $users = $stmt->fetchAll();
        
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Full Name</th><th>Role</th><th>Active</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . $user['username'] . "</td>";
            echo "<td>" . $user['email'] . "</td>";
            echo "<td>" . $user['full_name'] . "</td>";
            echo "<td>" . $user['role'] . "</td>";
            echo "<td>" . ($user['is_active'] ? 'Yes' : 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "❌ Error checking admin data: " . $e->getMessage() . "<br>";
}

// 4. Test password verification
echo "<h2>4. Testing password verification</h2>";
try {
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute(['admin']);
    $user = $stmt->fetch();
    
    if ($user) {
        $test_password = 'admin123';
        if (password_verify($test_password, $user['password'])) {
            echo "✅ Password verification works correctly<br>";
        } else {
            echo "❌ Password verification failed<br>";
            echo "Updating password...<br>";
            
            $new_password_hash = password_hash($test_password, PASSWORD_DEFAULT);
            $update_stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
            $update_stmt->execute([$new_password_hash, 'admin']);
            
            echo "✅ Password updated successfully<br>";
        }
    } else {
        echo "❌ Admin user not found<br>";
    }
} catch (Exception $e) {
    echo "❌ Error testing password: " . $e->getMessage() . "<br>";
}

// 5. Test login process
echo "<h2>5. Testing login process</h2>";
try {
    $username = 'admin';
    $password = 'admin123';
    
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ? AND is_active = 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        echo "✅ Login process works correctly<br>";
        echo "User found: " . $user['full_name'] . "<br>";
        echo "Role: " . $user['role'] . "<br>";
    } else {
        echo "❌ Login process failed<br>";
        if (!$user) {
            echo "User not found or inactive<br>";
        } else {
            echo "Password verification failed<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Error testing login: " . $e->getMessage() . "<br>";
}

// 6. Check session functionality
echo "<h2>6. Testing session functionality</h2>";
try {
    $_SESSION['test'] = 'test_value';
    if (isset($_SESSION['test']) && $_SESSION['test'] === 'test_value') {
        echo "✅ Session functionality works correctly<br>";
    } else {
        echo "❌ Session functionality failed<br>";
    }
    unset($_SESSION['test']);
} catch (Exception $e) {
    echo "❌ Error testing session: " . $e->getMessage() . "<br>";
}

// 7. Check file permissions
echo "<h2>7. Checking file permissions</h2>";
$files_to_check = [
    '../config/database.php',
    'login.php',
    'dashboard.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        if (is_readable($file)) {
            echo "✅ $file is readable<br>";
        } else {
            echo "❌ $file is not readable<br>";
        }
    } else {
        echo "❌ $file does not exist<br>";
    }
}

echo "<h2>8. Summary</h2>";
echo "If all tests above show ✅, then the login system should work correctly.<br>";
echo "Try logging in with:<br>";
echo "Username: admin<br>";
echo "Password: admin123<br>";
echo "<br>";
echo "<a href='login.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login Page</a>";
?>
