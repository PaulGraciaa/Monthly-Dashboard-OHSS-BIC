# Troubleshooting Login OHSS

## Masalah Login yang Umum

### 1. Login tidak bisa / Error

**Gejala:**
- Halaman login tidak muncul
- Error saat submit form
- Redirect tidak berfungsi

**Solusi:**

#### Langkah 1: Import Database
1. Buka browser dan akses: `http://localhost/Monthly_OHSS/admin/import_database.php`
2. Jalankan tool import database
3. Pastikan semua tabel terbuat dengan benar

#### Langkah 2: Diagnosis Login
1. Buka browser dan akses: `http://localhost/Monthly_OHSS/admin/fix_login.php`
2. Jalankan diagnosis login
3. Perbaiki masalah yang ditemukan

#### Langkah 3: Cek Kredensial Default
```
Username: admin
Password: admin123
```

### 2. Database Connection Error

**Gejala:**
- Error "Koneksi database gagal"
- Halaman tidak bisa diakses

**Solusi:**

#### Cek Konfigurasi Database
1. Buka file `config/database.php`
2. Pastikan konfigurasi benar:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ohss_dashboard');
define('DB_USER', 'root');
define('DB_PASS', '');
```

#### Cek MySQL Service
1. Pastikan MySQL/MariaDB berjalan
2. Cek di XAMPP/Laragon control panel
3. Restart service jika perlu

#### Cek Database
1. Buka phpMyAdmin
2. Pastikan database `ohss_dashboard` ada
3. Pastikan tabel `admin_users` ada

### 3. Password Tidak Bisa Login

**Gejala:**
- Username benar tapi password salah
- Error "Username atau password salah"

**Solusi:**

#### Reset Password Admin
1. Buka phpMyAdmin
2. Pilih database `ohss_dashboard`
3. Pilih tabel `admin_users`
4. Edit user admin
5. Update password dengan hash baru:
```sql
UPDATE admin_users SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE username = 'admin';
```

#### Atau gunakan tool diagnosis
1. Akses `admin/fix_login.php`
2. Tool akan otomatis memperbaiki password

### 4. Session Error

**Gejala:**
- Login berhasil tapi langsung logout
- Session tidak tersimpan

**Solusi:**

#### Cek Session Directory
1. Pastikan folder session bisa ditulis
2. Cek permission folder temp PHP

#### Cek Session Configuration
1. Buka `php.ini`
2. Pastikan session settings benar:
```ini
session.save_handler = files
session.save_path = "/tmp"
```

### 5. File Permission Error

**Gejala:**
- Error "file not found"
- Error permission denied

**Solusi:**

#### Cek File Permissions
1. Pastikan semua file bisa dibaca
2. Set permission yang benar:
```bash
chmod 644 *.php
chmod 755 admin/
```

#### Cek File Path
1. Pastikan path file benar
2. Cek case sensitivity (Linux)

## Langkah-langkah Troubleshooting Lengkap

### Step 1: Import Database
```bash
# Akses di browser
http://localhost/Monthly_OHSS/admin/import_database.php
```

### Step 2: Diagnosis Login
```bash
# Akses di browser
http://localhost/Monthly_OHSS/admin/fix_login.php
```

### Step 3: Test Login
```bash
# Akses di browser
http://localhost/Monthly_OHSS/admin/login.php
```

### Step 4: Cek Error Log
1. Buka error log PHP
2. Cari error terkait login
3. Perbaiki sesuai error

## Kredensial Default

```
Username: admin
Password: admin123
Email: admin@batamindo.com
Role: super_admin
```

## Struktur Database yang Diperlukan

### Tabel admin_users
```sql
CREATE TABLE admin_users (
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
);
```

### Data Admin Default
```sql
INSERT INTO admin_users (username, password, email, full_name, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@batamindo.com', 'Administrator', 'super_admin');
```

## File yang Diperlukan

1. `config/database.php` - Konfigurasi database
2. `admin/login.php` - Halaman login
3. `admin/dashboard.php` - Dashboard setelah login
4. `admin/import_database.php` - Tool import database
5. `admin/fix_login.php` - Tool diagnosis login

## Support

Jika masih mengalami masalah, silakan:

1. Cek error log PHP
2. Cek error log MySQL
3. Pastikan semua file ada dan bisa diakses
4. Pastikan database dan tabel sudah dibuat
5. Pastikan kredensial login benar

## Quick Fix Commands

### Reset Database
```sql
DROP DATABASE IF EXISTS ohss_dashboard;
CREATE DATABASE ohss_dashboard CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Reset Admin User
```sql
DELETE FROM admin_users WHERE username = 'admin';
INSERT INTO admin_users (username, password, email, full_name, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@batamindo.com', 'Administrator', 'super_admin');
```
