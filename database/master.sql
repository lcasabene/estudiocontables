-- =============================================
-- BASE DE DATOS MAESTRA (SaaS)
-- =============================================
CREATE DATABASE IF NOT EXISTS `saas_master` 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE `saas_master`;

-- Tabla de estudios contables
CREATE TABLE IF NOT EXISTS `estudios` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de conexiones de base de datos por estudio
CREATE TABLE IF NOT EXISTS `estudio_db` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `estudio_id` INT UNSIGNED NOT NULL,
    `db_host` VARCHAR(255) NOT NULL DEFAULT '127.0.0.1',
    `db_name` VARCHAR(255) NOT NULL,
    `db_user` VARCHAR(255) NOT NULL,
    `db_pass` VARCHAR(255) NOT NULL DEFAULT '',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`estudio_id`) REFERENCES `estudios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Rate limiting table
CREATE TABLE IF NOT EXISTS `login_attempts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `ip_address` VARCHAR(45) NOT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `attempted_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_ip_slug` (`ip_address`, `slug`)
) ENGINE=InnoDB;

-- =============================================
-- INSERTAR ESTUDIO PRINCIPAL
-- =============================================
INSERT INTO `estudios` (`nombre`, `slug`, `activo`) VALUES
('Estudio Contable Casabene', 'estudio', 1);

INSERT INTO `estudio_db` (`estudio_id`, `db_host`, `db_name`, `db_user`, `db_pass`) VALUES
(1, '127.0.0.1', 'estudiocontable', 'root', '');
