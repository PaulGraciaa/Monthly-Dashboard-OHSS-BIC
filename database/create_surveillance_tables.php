<?php
require_once __DIR__ . '/../config/database.php';

try {
    // Buat tabel surveillance_overall_performance
    $sql = "CREATE TABLE IF NOT EXISTS `surveillance_overall_performance` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `indicator` varchar(255) NOT NULL,
        `current_month` varchar(50) NOT NULL,
        `cumulative` varchar(50) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    echo "✓ Tabel surveillance_overall_performance berhasil dibuat<br>";
    
    // Cek apakah data sudah ada
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM surveillance_overall_performance");
    $count = $stmt->fetch()['count'];
    
    if ($count == 0) {
        // Insert data awal
        $insertData = [
            ['Overall CCTV Operational Readiness Performance', '100%', ''],
            ['Overall CCTV Preventive Maintenance (PM) Performance', '100%', '100%'],
            ['Overall CCTV Corrective Maintenance (CM) Performance', '100%', '100%'],
            ['Utilisation of ISSS – Guard Tour Patrol', 'QR Checkpoint Scanned', '5741'],
            ['Utilisation of ISSS – Guard Tour Patrol', 'Patrol Hours', '833']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO surveillance_overall_performance (indicator, current_month, cumulative) VALUES (?, ?, ?)");
        
        foreach ($insertData as $data) {
            $stmt->execute($data);
        }
        
        echo "✓ Data awal berhasil dimasukkan<br>";
    } else {
        echo "✓ Data sudah ada di database<br>";
    }
    
    // Buat tabel surveillance_improvements_progress
    $sql = "CREATE TABLE IF NOT EXISTS `surveillance_improvements_progress` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `project_title` varchar(255) NOT NULL,
        `description` text,
        `status` varchar(50) NOT NULL,
        `percentage` varchar(100) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    echo "✓ Tabel surveillance_improvements_progress berhasil dibuat<br>";
    
    // Cek apakah data improvements progress sudah ada
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM surveillance_improvements_progress");
    $count = $stmt->fetch()['count'];
    
    if ($count == 0) {
        // Insert data awal improvements progress
        $insertData = [
            ['Expand Cameras at Commercial Area (Pujasera Area)', 'Was deployed 6 from 6 camera.', 'Done', 'Cumulative: 100%'],
            ['Expand Cameras at Resident Area (Shophouse)', 'Was deployed 6 from 6 camera, one camera will be installing in April', 'Done', 'Cumulative: 100%<br>Increase this month: 25%'],
            ['Expand Cameras at Commercial Area (Panasera Area)', '', 'In Progress', 'Cumulative: 40%<br>Increase this month: 0%'],
            ['Upgrading Surveillance System at CCTV Room OPS', 'Was installed server rack, waiting other part and equipment', 'In Progress', 'Cumulative: 70%<br>Increase this month: 20%']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO surveillance_improvements_progress (project_title, description, status, percentage) VALUES (?, ?, ?, ?)");
        
        foreach ($insertData as $data) {
            $stmt->execute($data);
        }
        
                        echo "✓ Data improvements progress awal berhasil dimasukkan<br>";
            } else {
                echo "✓ Data improvements progress sudah ada di database<br>";
            }
            
            // Buat tabel surveillance_cctv_system
            $sql = "CREATE TABLE IF NOT EXISTS `surveillance_cctv_system` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `category` varchar(255) NOT NULL,
                `description` varchar(255) NOT NULL,
                `operational` varchar(50) DEFAULT NULL,
                `non_operational` varchar(50) DEFAULT NULL,
                `readiness_percentage` varchar(50) DEFAULT NULL,
                `notes` text,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            
            $pdo->exec($sql);
            echo "✓ Tabel surveillance_cctv_system berhasil dibuat<br>";
            
            // Cek apakah data CCTV system sudah ada
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM surveillance_cctv_system");
            $count = $stmt->fetch()['count'];
            
            if ($count == 0) {
                // Insert data awal CCTV system
                $insertData = [
                    ['Deployed CCTV Cameras Readiness', 'CCTV Camera (IP Type)', '152', '00', '100%', ''],
                    ['Deployed CCTV Cameras Readiness', 'CCTV Fixed Camera (Analog Type)', '11', '00', '100%', ''],
                    ['Total Portable CCTV Cameras', 'Portable CCTV Cameras', '00', '00', '0%', ''],
                    ['Preventive Maintenance', 'Scheduled', '01', '01', '0% / 100%', ''],
                    ['Corrective Maintenance', 'No. of Faults', '15', '15', '0% / 100%', ''],
                    ['CCTV Footage Request', 'No. of Request', '11', '11', '0% / 100%', '']
                ];
                
                $stmt = $pdo->prepare("INSERT INTO surveillance_cctv_system (category, description, operational, non_operational, readiness_percentage, notes) VALUES (?, ?, ?, ?, ?, ?)");
                
                foreach ($insertData as $data) {
                    $stmt->execute($data);
                }
                
                echo "✓ Data CCTV system awal berhasil dimasukkan<br>";
            } else {
                echo "✓ Data CCTV system sudah ada di database<br>";
            }
            
            // Buat tabel surveillance_isss_software
            $sql = "CREATE TABLE IF NOT EXISTS `surveillance_isss_software` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `metric_name` varchar(255) NOT NULL,
                `jan` varchar(50) DEFAULT NULL,
                `feb` varchar(50) DEFAULT NULL,
                `mar` varchar(50) DEFAULT NULL,
                `apr` varchar(50) DEFAULT NULL,
                `may` varchar(50) DEFAULT NULL,
                `jun` varchar(50) DEFAULT NULL,
                `jul` varchar(50) DEFAULT NULL,
                `aug` varchar(50) DEFAULT NULL,
                `sep` varchar(50) DEFAULT NULL,
                `oct` varchar(50) DEFAULT NULL,
                `nov` varchar(50) DEFAULT NULL,
                `dec` varchar(50) DEFAULT NULL,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            
            $pdo->exec($sql);
            echo "✓ Tabel surveillance_isss_software berhasil dibuat<br>";
            
            // Cek apakah data ISSS software sudah ada
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM surveillance_isss_software");
            $count = $stmt->fetch()['count'];
            
            if ($count == 0) {
                // Insert data awal ISSS software
                $insertData = [
                    ['Total Number Of Patrol Session Conducted', '335', '299', '283', '306', '327', '311', '', '', '', '', '', ''],
                    ['Total Patrol Duration Conducted (Hours)', '732', '687', '673', '749', '769', '833', '', '', '', '', '', ''],
                    ['Total QR Checkpoints Scanned', '5693', '5392', '4704', '6102', '5616', '5741', '', '', '', '', '', '']
                ];
                
                $stmt = $pdo->prepare("INSERT INTO surveillance_isss_software (metric_name, jan, feb, mar, apr, may, jun, jul, aug, sep, oct, nov, `dec`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                foreach ($insertData as $data) {
                    $stmt->execute($data);
                }
                
                echo "✓ Data ISSS software awal berhasil dimasukkan<br>";
            } else {
                echo "✓ Data ISSS software sudah ada di database<br>";
            }
            
            // Buat tabel surveillance_security_patrol
            $sql = "CREATE TABLE IF NOT EXISTS `surveillance_security_patrol` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `team_name` varchar(255) NOT NULL,
                `jan` varchar(50) DEFAULT NULL,
                `feb` varchar(50) DEFAULT NULL,
                `mar` varchar(50) DEFAULT NULL,
                `apr` varchar(50) DEFAULT NULL,
                `may` varchar(50) DEFAULT NULL,
                `jun` varchar(50) DEFAULT NULL,
                `jul` varchar(50) DEFAULT NULL,
                `aug` varchar(50) DEFAULT NULL,
                `sep` varchar(50) DEFAULT NULL,
                `oct` varchar(50) DEFAULT NULL,
                `nov` varchar(50) DEFAULT NULL,
                `dec` varchar(50) DEFAULT NULL,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            
            $pdo->exec($sql);
            echo "✓ Tabel surveillance_security_patrol berhasil dibuat<br>";
            
            // Cek apakah data security patrol sudah ada
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM surveillance_security_patrol");
            $count = $stmt->fetch()['count'];
            
            if ($count == 0) {
                // Insert data awal security patrol
                $insertData = [
                    ['Team A – Patrol Truck', '69', '21', '44', '62', '52', '72', '', '', '', '', '', ''],
                    ['Team A – Patrol Bike', '40', '41', '42', '47', '53', '41', '', '', '', '', '', ''],
                    ['Team B – Patrol Truck', '83', '43', '58', '56', '42', '57', '', '', '', '', '', ''],
                    ['Team B – Patrol Bike', '0', '0', '16', '36', '35', '50', '', '', '', '', '', ''],
                    ['Team C – Patrol Truck', '86', '40', '69', '114', '90', '112', '', '', '', '', '', ''],
                    ['Team C – Patrol Bike', '6', '1', '1', '11', '9', '5', '', '', '', '', '', ''],
                    ['Team D – Patrol Truck', '62', '21', '74', '79', '94', '96', '', '', '', '', '', ''],
                    ['Team D – Patrol Bike', '28', '39', '28', '20', '14', '53', '', '', '', '', '', ''],
                    ['Powerhouse', '343', '332', '337', '320', '377', '342', '', '', '', '', '', ''],
                    ['Total', '717', '538', '669', '745', '766', '828', '', '', '', '', '', '']
                ];
                
                $stmt = $pdo->prepare("INSERT INTO surveillance_security_patrol (team_name, jan, feb, mar, apr, may, jun, jul, aug, sep, oct, nov, `dec`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                foreach ($insertData as $data) {
                    $stmt->execute($data);
                }
                
                            echo "✓ Data security patrol awal berhasil dimasukkan<br>";
        } else {
            echo "✓ Data security patrol sudah ada di database<br>";
        }
        
        // Buat tabel surveillance_qr_scanned
        $sql = "CREATE TABLE IF NOT EXISTS `surveillance_qr_scanned` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `team_name` varchar(255) NOT NULL,
            `jan` varchar(50) DEFAULT NULL,
            `feb` varchar(50) DEFAULT NULL,
            `mar` varchar(50) DEFAULT NULL,
            `apr` varchar(50) DEFAULT NULL,
            `may` varchar(50) DEFAULT NULL,
            `jun` varchar(50) DEFAULT NULL,
            `jul` varchar(50) DEFAULT NULL,
            `aug` varchar(50) DEFAULT NULL,
            `sep` varchar(50) DEFAULT NULL,
            `oct` varchar(50) DEFAULT NULL,
            `nov` varchar(50) DEFAULT NULL,
            `dec` varchar(50) DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $pdo->exec($sql);
        echo "✓ Tabel surveillance_qr_scanned berhasil dibuat<br>";
        
        // Cek apakah data QR scanned sudah ada
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM surveillance_qr_scanned");
        $count = $stmt->fetch()['count'];
        
        if ($count == 0) {
            // Insert data awal QR scanned
            $insertData = [
                ['Team A – Patrol Truck', '622', '833', '604', '1036', '429', '1076', '', '', '', '', '', ''],
                ['Team A – Patrol Bike', '1093', '634', '1265', '725', '440', '312', '', '', '', '', '', ''],
                ['Team B – Patrol Truck', '1166', '1348', '156', '672', '399', '825', '', '', '', '', '', ''],
                ['Team B – Patrol Bike', '0', '0', '486', '905', '634', '302', '', '', '', '', '', ''],
                ['Team C – Patrol Truck', '644', '959', '519', '696', '1063', '1391', '', '', '', '', '', ''],
                ['Team C – Patrol Bike', '308', '28', '29', '364', '196', '53', '', '', '', '', '', ''],
                ['Team D – Patrol Truck', '605', '126', '696', '920', '702', '', '', '', '', '', '', ''],
                ['Team D – Patrol Bike', '319', '726', '145', '348', '665', '336', '', '', '', '', '', ''],
                ['Powerhouse', '936', '738', '804', '660', '870', '744', '', '', '', '', '', ''],
                ['Total', '5693', '5392', '4704', '6102', '5616', '5741', '', '', '', '', '', '']
            ];
            
            $stmt = $pdo->prepare("INSERT INTO surveillance_qr_scanned (team_name, jan, feb, mar, apr, may, jun, jul, aug, sep, oct, nov, `dec`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            foreach ($insertData as $data) {
                $stmt->execute($data);
            }
            
            echo "✓ Data QR scanned awal berhasil dimasukkan<br>";
        } else {
            echo "✓ Data QR scanned sudah ada di database<br>";
        }
        
        // Buat tabel surveillance_roadmap_mapping
        $sql = "CREATE TABLE IF NOT EXISTS `surveillance_roadmap_mapping` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `location_name` varchar(255) NOT NULL,
            `location_number` int(11) NOT NULL,
            `description` text,
            `image_path` varchar(255) DEFAULT 'img/map.png',
            `cctv_coverage` varchar(50) DEFAULT 'Yes',
            `status` varchar(50) DEFAULT 'Active',
            `notes` text,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `location_number` (`location_number`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $pdo->exec($sql);
        echo "✓ Tabel surveillance_roadmap_mapping berhasil dibuat<br>";
        
        // Cek apakah data roadmap mapping sudah ada
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM surveillance_roadmap_mapping");
        $count = $stmt->fetch()['count'];
        
        if ($count == 0) {
            // Insert data awal roadmap mapping
            $insertData = [
                ['BIP Parking Areas', 1, 'Parking area with CCTV monitoring', 'img/map.png', 'Yes', 'Active', ''],
                ['Multi Purpose Hall (MPH)', 2, 'Multi-purpose facility with surveillance', 'img/map.png', 'Yes', 'Active', ''],
                ['Community Centre', 3, 'Community center area monitoring', 'img/map.png', 'Yes', 'Active', ''],
                ['Panasera Areas', 4, 'Panasera commercial area surveillance', 'img/map.png', 'Yes', 'Active', ''],
                ['Power House #01', 5, 'Power house facility monitoring', 'img/map.png', 'Yes', 'Active', ''],
                ['Power House #4', 6, 'Power house facility monitoring', 'img/map.png', 'Yes', 'Active', ''],
                ['Power House #03', 7, 'Power house facility monitoring', 'img/map.png', 'Yes', 'Active', ''],
                ['STP WWTP areas', 8, 'Sewage treatment plant monitoring', 'img/map.png', 'Yes', 'Active', ''],
                ['WTP area', 9, 'Water treatment plant monitoring', 'img/map.png', 'Yes', 'Active', ''],
                ['Dormitory block', 10, 'Dormitory area surveillance', 'img/map.png', 'Yes', 'Active', ''],
                ['OPS Area', 11, 'Operations area monitoring', 'img/map.png', 'Yes', 'Active', ''],
                ['Wisma Batamindo', 12, 'Wisma Batamindo facility monitoring', 'img/map.png', 'Yes', 'Active', '']
            ];
            
            $stmt = $pdo->prepare("INSERT INTO surveillance_roadmap_mapping (location_name, location_number, description, image_path, cctv_coverage, status, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            foreach ($insertData as $data) {
                $stmt->execute($data);
            }
            
            echo "✓ Data roadmap mapping awal berhasil dimasukkan<br>";
        } else {
            echo "✓ Data roadmap mapping sudah ada di database<br>";
        }
        
        echo "<br><strong>Setup database surveillance berhasil!</strong><br>";
        echo "<a href='../admin/surveillance/overall_performance.php'>Klik di sini untuk mengakses halaman admin</a>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
