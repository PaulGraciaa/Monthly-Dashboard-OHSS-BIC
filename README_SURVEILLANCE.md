# Surveillance Management System

## Deskripsi
Sistem manajemen surveillance yang menyediakan dashboard untuk monitoring performa sistem surveillance, CCTV, ISSS software, dan tim patroli keamanan. Sistem ini terdiri dari halaman utama yang menampilkan data secara dinamis dan panel admin untuk mengelola data.

## Versi
1.3.0

## Fitur Utama

### 1. Surveillance System Overall Performance
- Monitoring performa keseluruhan sistem surveillance
- Data metrik performa bulanan
- Admin panel untuk CRUD data

### 2. Improvements Project Progress
- Tracking progress proyek perbaikan
- Status proyek (Done, In Progress)
- Persentase penyelesaian
- Admin panel untuk CRUD data

### 3. CCTV System
- Monitoring sistem CCTV
- Data kamera operasional dan non-operasional
- Persentase kesiapan sistem
- Maintenance tracking
- Admin panel untuk CRUD data

### 4. ISSS Software Utilization
- Monitoring penggunaan software ISSS
- Data patrol session, duration, dan QR checkpoints
- Metrik bulanan
- Admin panel untuk CRUD data

### 5. Security Team Patrol Performance
- Monitoring performa tim patroli keamanan
- Data patrol duration per tim (A, B, C, D)
- Patrol Truck dan Patrol Bike tracking
- Data Powerhouse
- Total performa bulanan
- Admin panel untuk CRUD data

### 6. Security Team Performance on QR Scanned
- Monitoring performa tim keamanan dalam scanning QR checkpoints
- Data QR checkpoints scanned per tim (A, B, C, D)
- Patrol Truck dan Patrol Bike tracking
- Data Powerhouse
- Total QR checkpoints bulanan
- Admin panel untuk CRUD data

## Struktur File

```
admin/surveillance/
├── index.php                          # Halaman utama admin surveillance
├── overall_performance.php             # CRUD untuk Surveillance System Overall Performance
├── improvements_progress.php          # CRUD untuk Improvements Project Progress
├── cctv_system.php                     # CRUD untuk CCTV System
├── isss_software.php                   # CRUD untuk ISSS Software Utilization
├── security_patrol.php                 # CRUD untuk Security Team Patrol Performance
└── qr_scanned.php                      # CRUD untuk Security Team Performance on QR Scanned

database/
├── surveillance_tables.sql            # SQL schema untuk semua tabel surveillance
└── create_surveillance_tables.php     # Script PHP untuk membuat tabel dan data awal

surveillance.php                        # Halaman utama surveillance (view)
```

## Struktur Database

### Tabel: surveillance_overall_performance
- `id` - Primary key
- `metric_name` - Nama metrik
- `jan` sampai `dec` - Data bulanan
- `created_at`, `updated_at` - Timestamp

### Tabel: surveillance_improvements_progress
- `id` - Primary key
- `project_title` - Judul proyek
- `description` - Deskripsi proyek
- `status` - Status (Done/In Progress)
- `percentage` - Persentase penyelesaian
- `created_at`, `updated_at` - Timestamp

### Tabel: surveillance_cctv_system
- `id` - Primary key
- `category` - Kategori (Deployed CCTV, Maintenance, dll)
- `description` - Deskripsi
- `operational` - Jumlah operasional
- `non_operational` - Jumlah non-operasional
- `readiness_percentage` - Persentase kesiapan
- `notes` - Catatan
- `created_at`, `updated_at` - Timestamp

### Tabel: surveillance_isss_software
- `id` - Primary key
- `metric_name` - Nama metrik
- `jan` sampai `dec` - Data bulanan
- `created_at`, `updated_at` - Timestamp

### Tabel: surveillance_security_patrol
- `id` - Primary key
- `team_name` - Nama tim (Team A-D, Powerhouse, Total)
- `jan` sampai `dec` - Data patrol duration bulanan
- `created_at`, `updated_at` - Timestamp

### Tabel: surveillance_qr_scanned
- `id` - Primary key
- `team_name` - Nama tim (Team A-D, Powerhouse, Total)
- `jan` sampai `dec` - Data QR checkpoints scanned bulanan
- `created_at`, `updated_at` - Timestamp

## Data Awal

### Surveillance System Overall Performance
- Total Number of Cameras Deployed
- Total Number of Cameras Operational
- Total Number of Cameras Non-Operational
- System Readiness Percentage

### Improvements Project Progress
- Expand Cameras at Commercial Area (Pujasera Area)
- Expand Cameras at Resident Area (Shophouse)
- Expand Cameras at Commercial Area (Panasera Area)
- Upgrading Surveillance System at CCTV Room OPS

### CCTV System
- Deployed CCTV Cameras Readiness (IP Type)
- Deployed CCTV Cameras Readiness (Analog Type)
- Total Portable CCTV Cameras
- Preventive Maintenance
- Corrective Maintenance
- CCTV Footage Request

### ISSS Software Utilization
- Total Number Of Patrol Session Conducted
- Total Patrol Duration Conducted (Hours)
- Total QR Checkpoints Scanned

### Security Team Patrol Performance
- Team A – Patrol Truck
- Team A – Patrol Bike
- Team B – Patrol Truck
- Team B – Patrol Bike
- Team C – Patrol Truck
- Team C – Patrol Bike
- Team D – Patrol Truck
- Team D – Patrol Bike
- Powerhouse
- Total

### Security Team Performance on QR Scanned
- Team A – Patrol Truck
- Team A – Patrol Bike
- Team B – Patrol Truck
- Team B – Patrol Bike
- Team C – Patrol Truck
- Team C – Patrol Bike
- Team D – Patrol Truck
- Team D – Patrol Bike
- Powerhouse
- Total

## Cara Penggunaan
>?
### 1. Setup Database
```bash
php database/create_surveillance_tables.php
```

### 2. Akses Admin Panel
1. Login ke admin panel
2. Navigasi ke "Surveillance Management"
3. Pilih modul yang ingin dikelola

### 3. Mengelola Data
- **Tambah Data**: Klik tombol "Tambah" dan isi form
- **Edit Data**: Klik tombol "Edit" pada baris data
- **Hapus Data**: Klik tombol "Hapus" dan konfirmasi

### 4. View Data
- Akses `surveillance.php` untuk melihat data yang sudah diupdate
- Data akan ditampilkan secara dinamis dari database

## Modul yang Sudah Tersedia

✅ **Surveillance System Overall Performance** - CRUD lengkap
✅ **IMPROVEMENTS PROJECT PROGRESS** - CRUD lengkap  
✅ **CCTV System** - CRUD lengkap
✅ **ISSS Software Utilization** - CRUD lengkap
✅ **Security Team Patrol Performance** - CRUD lengkap
✅ **Security Team Performance on QR Scanned** - CRUD lengkap

## Teknologi yang Digunakan

- **Backend**: PHP 8.x
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript
- **CSS Framework**: Tailwind CSS
- **Icons**: Font Awesome
- **Database Connection**: PDO

## Keamanan

- Session management untuk admin
- Input sanitization untuk mencegah XSS
- Prepared statements untuk mencegah SQL injection
- Validasi input di sisi server

## Pengembangan Selanjutnya

- Export data ke Excel/PDF
- Grafik dan visualisasi data
- Notifikasi real-time
- API untuk integrasi dengan sistem lain
- Backup dan restore data otomatis

## Kontribusi

Untuk berkontribusi pada pengembangan sistem ini:
1. Fork repository
2. Buat branch fitur baru
3. Commit perubahan
4. Push ke branch
5. Buat Pull Request

## Lisensi

Sistem ini dikembangkan untuk internal use. Semua hak cipta dilindungi.

---

**Dibuat dengan ❤️ untuk sistem surveillance yang lebih baik**
