-- Database OHSS Dashboard
CREATE DATABASE IF NOT EXISTS ohss_dashboard CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ohss_dashboard;

-- Tabel Admin Users
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

-- Tabel KPI Leading Indicators
CREATE TABLE kpi_leading (
    id INT AUTO_INCREMENT PRIMARY KEY,
    indicator_name VARCHAR(255) NOT NULL,
    target_value INT NULL DEFAULT NULL,
    actual_value INT DEFAULT 0,
    month INT NOT NULL,
    year INT NOT NULL,
    status ENUM('on_track', 'behind', 'completed') DEFAULT 'on_track',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_indicator_month (indicator_name, month, year)
);

-- Tabel KPI Lagging Indicators
CREATE TABLE kpi_lagging (
    id INT AUTO_INCREMENT PRIMARY KEY,
    indicator_name VARCHAR(255) NOT NULL,
    target_value INT NULL DEFAULT NULL,
    actual_value INT DEFAULT 0,
    month INT NOT NULL,
    year INT NOT NULL,
    status ENUM('good', 'warning', 'critical') DEFAULT 'good',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_indicator_month (indicator_name, month, year)
);

-- Tabel Dashboard Statistics
CREATE TABLE dashboard_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stat_name VARCHAR(100) NOT NULL,
    stat_value VARCHAR(100) NOT NULL,
    stat_description TEXT,
    stat_icon VARCHAR(50),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel News/Announcements
CREATE TABLE news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    publish_date DATE NOT NULL,
    end_date DATE,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    priority INT DEFAULT 0,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Tabel Activities
CREATE TABLE activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    activity_date DATE NOT NULL,
    image_path VARCHAR(255),
    image_alt VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    display_order INT DEFAULT 0,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Tabel Media/Gallery
CREATE TABLE media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_name VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    file_size INT NOT NULL,
    alt_text VARCHAR(255),
    category VARCHAR(100),
    uploaded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Tabel Configuration
CREATE TABLE config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    config_key VARCHAR(100) UNIQUE NOT NULL,
    config_value TEXT,
    config_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Security Content
CREATE TABLE security_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_name VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    image_path VARCHAR(255),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Tabel untuk data Security Personnel
CREATE TABLE security_personnel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    position VARCHAR(100) NOT NULL,
    personnel_count INT DEFAULT 0,
    personnel_names TEXT,
    photo_path VARCHAR(255),
    photo_alt VARCHAR(255),
    description TEXT,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Tabel untuk galeri foto security
CREATE TABLE security_gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    photo_path VARCHAR(255) NOT NULL,
    photo_alt VARCHAR(255),
    category VARCHAR(100) DEFAULT 'patrol',
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Tabel Fire Safety Content
CREATE TABLE firesafety_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_name VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    image_path VARCHAR(255),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Tabel Surveillance Content
CREATE TABLE surveillance_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_name VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    image_path VARCHAR(255),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Insert default admin user (password: admin123)
INSERT INTO admin_users (username, password, email, full_name, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@batamindo.com', 'Administrator', 'super_admin');

-- Insert default dashboard statistics
INSERT INTO dashboard_stats (stat_name, stat_value, stat_description, stat_icon, display_order) VALUES 
('Total Safe Manhours (To date)', '6,280,088', 'Total jam kerja aman hingga saat ini', 'fas fa-user-shield', 1),
('Safe Manhours (2025)', '1,713,312', 'Jam kerja aman tahun 2025', 'fas fa-calendar-check', 2),
('Total Manpower (To Date)', '1,132', 'Total tenaga kerja hingga saat ini', 'fas fa-users', 3);

-- Insert default KPI Leading Indicators
INSERT INTO kpi_leading (indicator_name, target_value, actual_value, month, year) VALUES 
('OHSS Meeting', 20, 17, 7, 2025),
('OHSS Daily Toolbox Talk', 250, 215, 7, 2025),
('General Security & ERT Briefing', 800, 722, 7, 2025),
('Top Management Evaluation (Visit)', 5, 0, 7, 2025),
('Action Tracking Register', 10, 7, 7, 2025),
('Annual Internal Audit', 2, 1, 7, 2025),
('Annual External Audit', 2, 2, 7, 2025),
('PPE Compliance', 15, 8, 7, 2025),
('HIRADC/JRA/JSA', 250, 201, 7, 2025),
('Permit to work (PTW)', 1000, 856, 7, 2025),
('Daily OHS Report', 400, 371, 7, 2025),
('OHSS weekly Report', 50, 43, 7, 2025),
('OHSS Monthly Report', 15, 14, 7, 2025),
('OHSS Inspection', 20, 13, 7, 2025),
('OHSS Induction', 150, 114, 7, 2025),
('Safety Committee meeting (P2K3)', 5, 2, 7, 2025),
('Emergency Drill', 80, 59, 7, 2025),
('ERT Report (Fire Safety)', 10, 7, 7, 2025),
('Surveillance', 15, 10, 7, 2025),
('Contractor Safety Report', 150, 129, 7, 2025);

-- Insert default KPI Lagging Indicators
INSERT INTO kpi_lagging (indicator_name, target_value, actual_value, month, year) VALUES 
('Fatality Accident', 0, 0, 7, 2025),
('Lost Time Injury', 0, 0, 7, 2025),
('Restricted Workday Case', 0, 0, 7, 2025),
('Medical Treatment', 0, 0, 7, 2025),
('First aid case', 0, 0, 7, 2025),
('Traffic Accident', 0, 0, 7, 2025),
('Property Damage', 0, 1, 7, 2025),
('fire case', 0, 2, 7, 2025),
('environmental case', 0, 0, 7, 2025),
('Near Miss', 0, 1, 7, 2025),
('occupational disease', 0, 0, 7, 2025),
('security case', 0, 0, 7, 2025);

-- Insert default news
INSERT INTO news (title, content, publish_date, status, priority) VALUES 
('Perayaan Kemerdekaan Indonesia', 'Perayaan Kemerdekaan Indonesia akan dilaksanakan pada tanggal 17 Agustus 2025 di Kawasan Batamindo', '2025-08-17', 'published', 1),
('Safety road campaign', 'Safety road campaign yang akan dilaksanakan di Kawasan Batamindo', '2025-08-01', 'published', 2);

-- Insert default activities
INSERT INTO activities (title, description, activity_date, image_path, status, display_order) VALUES 
('Safety induction PT Niscala indo nusa', 'Safety induction untuk karyawan PT Niscala indo nusa', '2025-07-03', 'img/activity one.png', 'active', 1),
('Monitoring project lift lot 517 PT dynacast', 'Monitoring proyek lift di lot 517 PT dynacast', '2025-07-03', 'img/2.png', 'active', 2),
('OHSS Monthly Meeting', 'Rapat bulanan OHSS', '2025-07-29', 'img/meeting.png', 'active', 3),
('Meeting regarding OHSS Dashboard & Safety Induction revision', 'Rapat terkait revisi Dashboard OHSS dan Safety Induction', '2025-07-09', 'img/p.jpg', 'active', 4),
('Stop work Primajasa worker at lot 302', 'Penghentian kerja pekerja Primajasa di lot 302', '2025-07-03', 'img/primajasa.png', 'active', 5),
('Physical fitness training (Binsik) for the Security Team', 'Pelatihan kebugaran fisik (Binsik) untuk Tim Security, langsung dihadiri dan diawasi oleh CSO', '2025-07-05', 'img/binsik.png', 'active', 6),
('Installation of a \'No Parking\' banner at the rear side of Indomaret', 'Pemasangan banner \'No Parking\' di sisi belakang Indomaret', '2025-07-05', 'img/indoma.png', 'active', 7),
('Emergency Response to Fire Incident at Stall Jakarta (16), Pujasera Town Centre', 'Tanggapan darurat terhadap insiden kebakaran di Stall Jakarta (16), Pujasera Town Centre', '2025-07-05', 'img/stall.png', 'active', 8);

-- Insert default configuration
INSERT INTO config (config_key, config_value, config_description) VALUES 
('company_name', 'Batamindo Investment Cakrawala', 'Nama perusahaan'),
('dashboard_title', 'Dashboard OHSS Monthly', 'Judul dashboard'),
('report_code', 'BIC / OHSS-25-034-006-179', 'Kode laporan'),
('cut_off_date', '01 July –31 July 2025', 'Tanggal cut off'),
('performance_positive', '90', 'Persentase performa positif'),
('performance_negative', '5', 'Persentase performa negatif'),
('performance_others', '5', 'Persentase performa lainnya');

-- Insert default Security content
INSERT INTO security_content (section_name, title, content, display_order) VALUES 
('patrol', 'Security Patrol', 'Regular patrol activities conducted by security personnel to ensure safety and security of the facility', 1),
('access_control', 'Access Control', 'Strict access control measures implemented at all entry points', 2),
('incident_report', 'Security Incident Report', 'Monthly security incident reports and analysis', 3),
('training', 'Security Training', 'Ongoing security training programs for security personnel', 4);

-- Insert default Security Personnel
INSERT INTO security_personnel (position, personnel_count, personnel_names, description, display_order) VALUES 
('Executive', 5, 'Juanris Saragih\nYuhendrizaI\nAfrizal M\nArif Kuswandi\nDessy Syofinai', 'Tim eksekutif security yang bertanggung jawab atas pengawasan keseluruhan', 1),
('Inspector', 4, 'Juah Sembiring\nUmar Baki\nAgus Widarto\nNelwan', 'Tim inspektor yang melakukan pemeriksaan rutin', 2),
('Investigator', 4, 'Zakaria\nSaid Syafi''il\nRatno\nLukman Nur Arifi', 'Tim investigator untuk kasus-kasus keamanan', 3),
('Patrol team', 28, 'Team A: 7 personel\nTeam B: 7 personel\nTeam C: 7 personel\nTeam D: 7 personel', 'Tim patroli yang bertugas 24 jam', 4),
('Radio Operator', 4, 'Baharuddin\nSyamsul Huda\nNoorkholiq\nJonson Manalu', 'Operator radio untuk komunikasi tim', 5),
('Security', 124, 'Sumber Daya Dian (SDM)', 'Personel security umum', 6);

-- Insert default Security Gallery
INSERT INTO security_gallery (title, description, photo_path, photo_alt, category, display_order) VALUES 
('Patroli Siang Hari', 'Patroli siang hari di area tower, memastikan keamanan lingkungan.', 'img/a.jpg', 'Patroli 1', 'patrol', 1),
('Pemeriksaan Panel Listrik', 'Pemeriksaan rutin panel listrik oleh dua personel security.', 'img/p.jpg', 'Patroli 2', 'inspection', 2),
('Sweeping Area Pagar', 'Sweeping area pagar pembatas untuk mencegah akses tidak sah.', 'img/a.jpg', 'Patroli 3', 'patrol', 3),
('Monitoring Malam Hari', 'Monitoring malam hari, patroli keliling area tower.', 'img/p.jpg', 'Patroli 4', 'monitoring', 4),
('Koordinasi Tim Security', 'Koordinasi tim security sebelum patroli dimulai.', 'img/a.jpg', 'Patroli 5', 'coordination', 5),
('Pengecekan Fasilitas', 'Pengecekan kondisi sekitar tower dan fasilitas pendukung.', 'img/p.jpg', 'Patroli 6', 'inspection', 6),
('Patroli Motor', 'Patroli motor di jalur akses utama menuju tower.', 'img/a.jpg', 'Patroli 7', 'patrol', 7),
('Pemeriksaan Area Belakang', 'Pemeriksaan area belakang tower pada sore hari.', 'img/p.jpg', 'Patroli 8', 'inspection', 8),
('Sweeping Area Terbuka', 'Sweeping area terbuka untuk deteksi potensi gangguan.', 'img/a.jpg', 'Patroli 9', 'patrol', 9);

-- Insert default Fire Safety content
INSERT INTO firesafety_content (section_name, title, content, display_order) VALUES 
('equipment', 'Fire Safety Equipment', 'Regular inspection and maintenance of fire safety equipment', 1),
('drills', 'Fire Drills', 'Monthly fire drills conducted to ensure emergency preparedness', 2),
('training', 'Fire Safety Training', 'Fire safety training for all employees', 3),
('inspection', 'Fire Safety Inspection', 'Regular fire safety inspections and compliance checks', 4);

-- Insert default Surveillance content
INSERT INTO surveillance_content (section_name, title, content, display_order) VALUES 
('cctv', 'CCTV Monitoring', '24/7 CCTV monitoring system coverage and maintenance', 1),
('incidents', 'Surveillance Incidents', 'Incidents captured through surveillance systems', 2),
('maintenance', 'System Maintenance', 'Regular maintenance and updates of surveillance systems', 3),
('coverage', 'Coverage Areas', 'Surveillance coverage areas and blind spot analysis', 4); 

-- =============================================
-- PTW Records (untuk menghubungkan Bagian 1 & 2)
-- =============================================
CREATE TABLE IF NOT EXISTS ptw_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contractor_name VARCHAR(255) NOT NULL,
    num_ptw INT DEFAULT 0,
    general INT DEFAULT 0,
    hot_work INT DEFAULT 0,
    lifting INT DEFAULT 0,
    excavation INT DEFAULT 0,
    electrical INT DEFAULT 0,
    work_high INT DEFAULT 0,
    radiography INT DEFAULT 0,
    manpower INT DEFAULT 0,
    month INT NOT NULL,
    year INT NOT NULL,
    display_order INT DEFAULT 0,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE SET NULL,
    UNIQUE KEY uniq_record (contractor_name, month, year)
);

-- Seed PTW untuk July 2025 sesuai tampilan OHS.php saat ini
INSERT INTO ptw_records (contractor_name, num_ptw, general, hot_work, lifting, excavation, electrical, work_high, radiography, manpower, month, year, display_order)
VALUES
('PT.Dredolf Indonesia', 11, 2, 2, 1, 2, 2, 2, 0, 120, 7, 2025, 1),
('PT Endorshine Energy Solutions', 5, 1, 1, 1, 0, 1, 1, 0, 55, 7, 2025, 2),
('PT.Cipta Prima jasa', 3, 2, 1, 0, 0, 0, 0, 0, 15, 7, 2025, 3),
('PT.Mitsubhisi Jaya Elevator and Escalator', 7, 2, 1, 1, 1, 1, 1, 0, 20, 7, 2025, 4),
('PT.PrimaJasa Tunas Mandiri', 17, 6, 2, 0, 4, 4, 1, 0, 165, 7, 2025, 5),
('PT.Total Persada Indonesia', 5, 2, 1, 0, 0, 1, 1, 0, 225, 7, 2025, 6),
('PT.Semarak Kontruksi Batam', 5, 2, 2, 1, 2, 0, 1, 0, 68, 7, 2025, 7),
('PT.Berkah Alam Tabantang', 10, 4, 3, 0, 2, 1, 1, 0, 30, 7, 2025, 8),
('PT.Global Karya Bangun', 3, 1, 1, 0, 0, 1, 0, 0, 21, 7, 2025, 9),
('PT.Marindo alfa sentosa', 3, 1, 0, 0, 1, 1, 0, 0, 15, 7, 2025, 10),
('PT.Niscala Indonusa', 2, 1, 1, 0, 0, 0, 0, 0, 15, 7, 2025, 11);

-- =============================================
-- OHS Incidents (Bagian 3 - terpisah)
-- =============================================
CREATE TABLE IF NOT EXISTS ohs_incidents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    incident_date DATE NOT NULL,
    incident_time TIME,
    who_name VARCHAR(100),
    who_npk VARCHAR(50),
    summary TEXT,
    result TEXT,
    root_causes TEXT,
    key_takeaways TEXT,
    corrective_actions TEXT,
    map_image_path VARCHAR(255),
    photo_image_path VARCHAR(255),
    status ENUM('draft','published','archived') DEFAULT 'published',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Seed contoh insiden sesuai OHS.php
INSERT INTO ohs_incidents (
    title, incident_date, incident_time, who_name, who_npk, summary, result, root_causes, key_takeaways, corrective_actions, map_image_path, photo_image_path, status
) VALUES (
    'Lesson Learned: Incident at Trash Storage Checkpoint B (First Aid Case)', '2025-07-04', '21:05:00',
    'Security Officer Didit Cahyono', '25567',
    'Officer attempted to close a damaged metal door (~40–50kg) at Checkpoint B. Door collapsed, trapping his right thumb and ring finger → laceration injuries.',
    'Treated at BIP Clinic. No property damage or lost time reported.',
    '- Lack of pre-task hazard assessment and lighting check during night shift.\n- Checkpoint placed near a known hazard (damaged structure).\n- Inadequate hazard reporting and delayed action on known damage.\n- No barricade/warning signs on damaged infrastructure.\n- Lack of training in handling damaged or unstable equipment.',
    '- Always assess risk before acting, especially on damaged equipment.\n- Ensure hazard reporting is immediate and followed up.\n- Night shift operations must be supported by adequate lighting and supervision.\n- Checkpoint placement must avoid hazardous zones.\n- Preventive maintenance and housekeeping are critical to safety.',
    '✅ Barricade and signage installed on damaged structures (Done)\n🕒 Refresher training on line of fire awareness (In Progress)\n📢 Protocol for quick hazard reporting under development (Done)\n✅ Barcode scanner relocated to safer area (Done)\n🕒 Routine hazard inspection at all checkpoints (In Progress)',
    NULL, NULL, 'published'
);

ALTER TABLE kpi_leading MODIFY target_value INT NULL, MODIFY notes TEXT NULL;
ALTER TABLE kpi_lagging MODIFY target_value INT NULL, MODIFY notes TEXT NULL;