<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Import Tool</h1>";

// Konfigurasi database
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'ohss_dashboard';

try {
    // Buat koneksi tanpa database terlebih dahulu
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected to MySQL server<br>";
    
    // Buat database jika belum ada
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Database '$database' created/verified<br>";
    
    // Pilih database
    $pdo->exec("USE `$database`");
    echo "✅ Using database '$database'<br>";
    
    // Baca file schema.sql
    $schema_file = '../database/schema.sql';
    if (file_exists($schema_file)) {
        echo "✅ Schema file found<br>";
        
        $sql = file_get_contents($schema_file);
        
        // Hapus baris yang membuat database karena sudah dibuat
        $sql = preg_replace('/CREATE DATABASE.*?;/i', '', $sql);
        $sql = preg_replace('/USE.*?;/i', '', $sql);
        
        // Eksekusi query satu per satu
        $queries = explode(';', $sql);
        $success_count = 0;
        $error_count = 0;
        
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                try {
                    $pdo->exec($query);
                    $success_count++;
                } catch (Exception $e) {
                    $error_count++;
                    echo "❌ Error executing query: " . $e->getMessage() . "<br>";
                    echo "Query: " . substr($query, 0, 100) . "...<br>";
                }
            }
        }
        
        echo "✅ Database import completed<br>";
        echo "Successful queries: $success_count<br>";
        echo "Failed queries: $error_count<br>";
        
    } else {
        echo "❌ Schema file not found at: $schema_file<br>";
        
        // Buat tabel admin_users manual jika file schema tidak ada
        echo "Creating admin_users table manually...<br>";
        
        $sql = "CREATE TABLE IF NOT EXISTS admin_users (
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
        echo "✅ admin_users table created<br>";
        
        // Buat admin default
        $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $sql = "INSERT INTO admin_users (username, password, email, full_name, role) VALUES 
                ('admin', ?, 'admin@batamindo.com', 'Administrator', 'super_admin')
                ON DUPLICATE KEY UPDATE password = VALUES(password)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$password_hash]);
        echo "✅ Default admin user created<br>";
    }
    
    // Verifikasi tabel yang dibuat
    echo "<h2>Verifying Tables</h2>";
    $tables = ['admin_users', 'kpi_leading', 'kpi_lagging', 'dashboard_stats', 'news', 'activities'];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "✅ Table '$table' exists<br>";
            } else {
                echo "❌ Table '$table' does not exist<br>";
            }
        } catch (Exception $e) {
            echo "❌ Error checking table '$table': " . $e->getMessage() . "<br>";
        }
    }
    
    // Verifikasi data admin
    echo "<h2>Verifying Admin Data</h2>";
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM admin_users");
        $result = $stmt->fetch();
        echo "Total admin users: " . $result['count'] . "<br>";
        
        if ($result['count'] > 0) {
            $stmt = $pdo->query("SELECT username, email, full_name, role FROM admin_users");
            $users = $stmt->fetchAll();
            
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr><th>Username</th><th>Email</th><th>Full Name</th><th>Role</th></tr>";
            foreach ($users as $user) {
                echo "<tr>";
                echo "<td>" . $user['username'] . "</td>";
                echo "<td>" . $user['email'] . "</td>";
                echo "<td>" . $user['full_name'] . "</td>";
                echo "<td>" . $user['role'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } catch (Exception $e) {
        echo "❌ Error verifying admin data: " . $e->getMessage() . "<br>";
    }
    
    echo "<h2>Import Summary</h2>";
    echo "✅ Database import completed successfully!<br>";
    echo "You can now try to login with:<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
    echo "<br>";
    echo "<a href='login.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login Page</a>";
    echo "&nbsp;&nbsp;";
    echo "<a href='fix_login.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Run Login Diagnosis</a>";
    
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    echo "Please check your database configuration in config/database.php<br>";
}
?>
