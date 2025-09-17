-- SQL for fire_safety_training table
CREATE TABLE fire_safety_training (
    id INT AUTO_INCREMENT PRIMARY KEY,
    serial_number INT NOT NULL,
    training_date DATE NOT NULL,
    location VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
);

-- Example insert data
INSERT INTO fire_safety_training (serial_number, training_date, location, subject, display_order, is_active) VALUES
(1, '2025-09-01', 'Main Hall', 'Fire Extinguisher Training', 1, 1),
(2, '2025-09-05', 'Warehouse', 'Evacuation Training', 2, 1),
(3, '2025-09-10', 'Office', 'First Aid Training', 3, 1);
