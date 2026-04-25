-- =============================================
-- MIGRACIÓN: Situación IB + Impuestos + Exenciones
-- Ejecutar en phpMyAdmin sobre la BD estudiocontable
-- =============================================

-- 1. Agregar campos de Ingresos Brutos a clientes
ALTER TABLE `clientes`
    ADD COLUMN `situacion_ib` ENUM('Convenio Multilateral','Contribuyente Directo') DEFAULT NULL AFTER `url_carpeta_drive`,
    ADD COLUMN `jurisdiccion_sede` VARCHAR(100) DEFAULT NULL AFTER `situacion_ib`;

-- 2. Tabla de impuestos
CREATE TABLE IF NOT EXISTS `impuestos` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(150) NOT NULL,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos iniciales de impuestos
INSERT INTO `impuestos` (`nombre`) VALUES
('Ingresos Brutos'),
('IVA'),
('Ganancias'),
('Bienes Personales'),
('Monotributo'),
('Impuesto a los Débitos y Créditos Bancarios'),
('Impuesto Inmobiliario'),
('Automotores'),
('Sellos');

-- 3. Tabla de exenciones por cliente
CREATE TABLE IF NOT EXISTS `exenciones` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `cliente_id` INT UNSIGNED NOT NULL,
    `impuesto_id` INT UNSIGNED NOT NULL,
    `fecha_desde` DATE DEFAULT NULL,
    `fecha_hasta` DATE DEFAULT NULL,
    `archivo` VARCHAR(500) DEFAULT NULL,
    `observaciones` TEXT DEFAULT NULL,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`impuesto_id`) REFERENCES `impuestos`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
