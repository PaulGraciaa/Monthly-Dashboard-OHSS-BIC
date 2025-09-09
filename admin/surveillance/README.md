# Surveillance Management System

Sistem manajemen surveillance untuk Batamindo Industrial Park yang kompatibel dengan PHP 5.3.8.

## Fitur Utama

- **Overall Performance** - Monitoring kinerja sistem surveillance
- **Improvements Progress** - Tracking progress proyek perbaikan
- **CCTV System** - Manajemen sistem CCTV
- **ISSS Software** - Monitoring software utilization
- **QR Scanned** - Tracking QR checkpoint scans
- **Security Patrol** - Monitoring patrol security
- **Roadmap Mapping** - Project roadmap dan progress

## Persyaratan Sistem

- PHP 5.3.8 atau lebih tinggi
- MySQL 5.x atau MariaDB
- Web server (Apache/Nginx)
- Browser modern dengan dukungan CSS3

## Instalasi

1. **Upload Files**
   - Upload semua file ke folder `admin/surveillance/`
   - Pastikan folder memiliki permission yang benar

2. **Database Setup**
   - Import file `create_tables.sql` ke database MySQL
   - Atau jalankan query SQL secara manual

3. **Konfigurasi**
   - Pastikan file `template_header.php` dan `template_footer.php` ada
   - Periksa koneksi database di `config/database.php`

## Struktur File

```
admin/surveillance/
├── index.php                 # Halaman utama surveillance
├── template_header.php       # Template header konsisten
├── template_footer.php       # Template footer
├── overall_performance.php   # Overall Performance management
├── improvements_progress.php # Improvements Progress management
├── cctv_system.php          # CCTV System management
├── isss_software.php        # ISSS Software management
├── qr_scanned.php           # QR Scanned management
├── security_patrol.php      # Security Patrol management
├── roadmap_mapping.php      # Roadmap Mapping management
├── create_tables.sql        # SQL untuk membuat tabel
└── README.md                # Dokumentasi ini
```

## Kompatibilitas PHP 5.3.8

Sistem ini telah dioptimalkan untuk kompatibilitas dengan PHP 5.3.8:

- Menggunakan `mysqli` instead of `PDO`
- Menghindari fitur PHP yang tidak tersedia di versi lama
- Menggunakan array syntax yang kompatibel
- Menghindari null coalescing operator (`??`)
- Menggunakan `isset()` untuk pengecekan variabel

## Struktur Database

### Tabel Utama

1. **surveillance_overall_performance**
   - `id`, `indicator`, `current_month`, `cumulative`

2. **surveillance_improvements_progress**
   - `id`, `project_title`, `description`, `status`, `percentage`

3. **surveillance_cctv_system**
   - `id`, `location`, `camera_type`, `readiness_percentage`, `status`

4. **surveillance_isss_software**
   - `id`, `software_name`, `description`, `status`, `utilization_percentage`

5. **surveillance_qr_scanned**
   - `id`, `checkpoint_name`, `location`, `total_scans`, `status`

6. **surveillance_security_patrol**
   - `id`, `team_name`, `patrol_type`, `total_sessions`, `total_duration`, `status`

7. **surveillance_roadmap_mapping**
   - `id`, `project_name`, `description`, `phase`, `status`, `completion_percentage`

## Penggunaan

1. **Login sebagai Admin**
   - Akses melalui `/admin/surveillance/`
   - Pastikan sudah login sebagai admin

2. **Navigasi**
   - Gunakan menu navigasi di header untuk berpindah antar modul
   - Setiap modul memiliki CRUD operations lengkap

3. **CRUD Operations**
   - **Create**: Tambah data baru
   - **Read**: Lihat semua data dalam tabel
   - **Update**: Edit data yang ada
   - **Delete**: Hapus data (dengan konfirmasi)

## Troubleshooting

### Error Umum

1. **Database Connection Error**
   - Periksa konfigurasi database
   - Pastikan MySQL service berjalan

2. **Permission Denied**
   - Periksa file permissions
   - Pastikan web server dapat membaca file

3. **Template Not Found**
   - Pastikan `template_header.php` dan `template_footer.php` ada
   - Periksa path file

### Log Error

- Periksa error log PHP di web server
- Periksa error log MySQL
- Aktifkan error reporting untuk debugging

## Support

Untuk bantuan teknis atau pertanyaan, silakan hubungi:
- Email: support@batamindo.com
- Phone: +62-xxx-xxx-xxxx

## Version History

- **v1.0** - Initial release dengan PHP 5.3.8 compatibility
- **v1.1** - UI improvements dan bug fixes
- **v1.2** - Template system dan navigasi yang konsisten

## License

© 2024 Batamindo Investment Cakrawala. All rights reserved.

