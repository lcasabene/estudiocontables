-- =============================================
-- BASE DE DATOS TENANT (una por estudio)
-- =============================================
CREATE DATABASE IF NOT EXISTS `estudiocontable` 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE `estudiocontable`;

-- Usuarios del estudio
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nombre_completo` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `rol` ENUM('admin', 'empleado', 'cliente') NOT NULL DEFAULT 'empleado',
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Clientes del estudio
CREATE TABLE IF NOT EXISTS `clientes` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `razon_social` VARCHAR(255) NOT NULL,
    `cuit` VARCHAR(13) NOT NULL UNIQUE,
    `email` VARCHAR(255) DEFAULT NULL,
    `telefono` VARCHAR(50) DEFAULT NULL,
    `direccion` TEXT DEFAULT NULL,
    `url_carpeta_drive` VARCHAR(500) DEFAULT NULL,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Relación Usuarios ↔ Clientes
CREATE TABLE IF NOT EXISTS `cliente_usuarios` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `cliente_id` INT UNSIGNED NOT NULL,
    `usuario_id` INT UNSIGNED NOT NULL,
    `perfil` VARCHAR(100) DEFAULT 'titular',
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_cliente_usuario` (`cliente_id`, `usuario_id`),
    FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Condiciones fiscales
CREATE TABLE IF NOT EXISTS `condiciones_fiscales` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(255) NOT NULL,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Historial condición fiscal por cliente
CREATE TABLE IF NOT EXISTS `cliente_condicion_fiscal` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `cliente_id` INT UNSIGNED NOT NULL,
    `condicion_fiscal_id` INT UNSIGNED NOT NULL,
    `fecha_desde` DATE NOT NULL,
    `fecha_hasta` DATE DEFAULT NULL,
    `observaciones` TEXT DEFAULT NULL,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`condicion_fiscal_id`) REFERENCES `condiciones_fiscales`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Documentos
CREATE TABLE IF NOT EXISTS `documentos` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `cliente_id` INT UNSIGNED NOT NULL,
    `titulo` VARCHAR(255) NOT NULL,
    `tipo` VARCHAR(100) DEFAULT NULL,
    `storage` ENUM('local', 'drive', 'url') NOT NULL DEFAULT 'local',
    `ruta_archivo` VARCHAR(500) DEFAULT NULL,
    `url` VARCHAR(500) DEFAULT NULL,
    `mime_type` VARCHAR(100) DEFAULT NULL,
    `tamano` BIGINT UNSIGNED DEFAULT NULL,
    `hash_sha256` VARCHAR(64) DEFAULT NULL,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Claves fiscales (cifrado AES-256-GCM)
CREATE TABLE IF NOT EXISTS `claves_fiscales` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `cliente_id` INT UNSIGNED NOT NULL,
    `referencia` VARCHAR(255) NOT NULL,
    `usuario_enc` TEXT NOT NULL,
    `password_enc` TEXT NOT NULL,
    `iv` VARCHAR(50) NOT NULL,
    `tag` VARCHAR(50) NOT NULL,
    `url_sitio` VARCHAR(500) DEFAULT NULL,
    `observaciones` TEXT DEFAULT NULL,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Auditoría
CREATE TABLE IF NOT EXISTS `audit_log` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT UNSIGNED DEFAULT NULL,
    `accion` VARCHAR(100) NOT NULL,
    `entidad` VARCHAR(100) NOT NULL,
    `entidad_id` INT UNSIGNED DEFAULT NULL,
    `ip` VARCHAR(45) DEFAULT NULL,
    `user_agent` VARCHAR(500) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_entidad` (`entidad`, `entidad_id`),
    INDEX `idx_usuario` (`usuario_id`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB;

-- =============================================
-- DATOS INICIALES
-- =============================================

-- Condiciones fiscales por defecto
INSERT INTO `condiciones_fiscales` (`nombre`) VALUES
('Responsable Inscripto'),
('Monotributista'),
('Exento'),
('Consumidor Final'),
('Sujeto No Categorizado');

-- Usuario admin por defecto (password: admin123)
INSERT INTO `usuarios` (`nombre_completo`, `email`, `password_hash`, `rol`) VALUES
('Administrador', 'admin@estudio.com', '$2y$10$hRl/ZFvKADVk2LvpYCtmnu5ecXpDYUsTxkbKKpAp0/X0AJoMv3o0C', 'admin');
