-- =============================================
-- SETUP UNIFICADO - BASE DE DATOS ÚNICA
-- Todas las tablas (master + tenant) en una sola DB
-- =============================================
CREATE DATABASE IF NOT EXISTS `estudiocontable` 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE `estudiocontable`;

-- =============================================
-- TABLAS MASTER (gestión multi-tenant)
-- =============================================

CREATE TABLE IF NOT EXISTS `estudios` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

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

CREATE TABLE IF NOT EXISTS `login_attempts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `ip_address` VARCHAR(45) NOT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `attempted_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_ip_slug` (`ip_address`, `slug`)
) ENGINE=InnoDB;

-- =============================================
-- TABLAS TENANT (operativas)
-- =============================================

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

CREATE TABLE IF NOT EXISTS `condiciones_fiscales` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(255) NOT NULL,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

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

CREATE TABLE IF NOT EXISTS `claves_fiscales` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `cliente_id` INT UNSIGNED NOT NULL,
    `referencia` VARCHAR(255) NOT NULL,
    `categoria` VARCHAR(50) NOT NULL DEFAULT 'otros',
    `usuario_enc` TEXT NOT NULL,
    `password_enc` TEXT NOT NULL,
    `iv` VARCHAR(50) NOT NULL,
    `tag` VARCHAR(50) NOT NULL,
    `url_sitio` VARCHAR(500) DEFAULT NULL,
    `observaciones` TEXT DEFAULT NULL,
    `ultimo_acceso` DATETIME DEFAULT NULL,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

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

-- Luis Ariel Casabene (apunta a la misma base de datos)
INSERT INTO `estudios` (`nombre`, `slug`, `activo`) VALUES
('Luis Ariel Casabene', 'estudio', 1);

-- IMPORTANTE: db_name apunta a la misma DB unificada
INSERT INTO `estudio_db` (`estudio_id`, `db_host`, `db_name`, `db_user`, `db_pass`) VALUES
(1, '127.0.0.1', 'estudiocontable', 'root', '');

-- Condiciones fiscales
INSERT INTO `condiciones_fiscales` (`nombre`) VALUES
('Responsable Inscripto'),
('Monotributista'),
('Exento'),
('Consumidor Final'),
('Sujeto No Categorizado');

-- =============================================
-- BLOG
-- =============================================
CREATE TABLE IF NOT EXISTS `blog_posts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `titulo` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `contenido` TEXT NOT NULL,
    `resumen` VARCHAR(500) DEFAULT NULL,
    `imagen_url` VARCHAR(500) DEFAULT NULL,
    `publicado` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_comments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `post_id` INT NOT NULL,
    `nombre` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) DEFAULT NULL,
    `comentario` TEXT NOT NULL,
    `aprobado` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`post_id`) REFERENCES `blog_posts`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_post_votes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `post_id` INT NOT NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `vote` ENUM('like','dislike') NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_post_ip` (`post_id`, `ip_address`),
    FOREIGN KEY (`post_id`) REFERENCES `blog_posts`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuario admin (password: admin123)
INSERT INTO `usuarios` (`nombre_completo`, `email`, `password_hash`, `rol`) VALUES
('Administrador', 'admin@estudio.com', '$2y$10$hRl/ZFvKADVk2LvpYCtmnu5ecXpDYUsTxkbKKpAp0/X0AJoMv3o0C', 'admin');
