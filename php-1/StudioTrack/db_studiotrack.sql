CREATE DATABASE IF NOT EXISTS studiotrack_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE studiotrack_db;

CREATE TABLE equipos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    tarifa_dia DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 1,
    estado ENUM('Disponible', 'Alquilado', 'Mantenimiento') DEFAULT 'Disponible'
);

INSERT INTO equipos (codigo, nombre, categoria, tarifa_dia, stock, estado) VALUES
('CAM-001', 'Sony FX3 Full-Frame Cinema', 'Cámaras', 85.00, 3, 'Disponible'),
('OPT-014', 'Lente Sigma 24-70mm f/2.8 DG DN', 'Óptica', 35.00, 4, 'Disponible'),
('LUZ-008', 'Aputure Amaran 200x S Bi-Color', 'Iluminación', 40.00, 2, 'Disponible'),
('AUD-003', 'Kit Rode Wireless PRO (2 TX + 1 RX)', 'Audio', 25.00, 0, 'Alquilado');
