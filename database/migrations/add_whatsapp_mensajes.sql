-- =============================================
-- MIGRACIÓN: Tabla whatsapp_mensajes
-- Ejecutar en phpMyAdmin sobre la BD estudiocontable
-- =============================================

CREATE TABLE IF NOT EXISTS `whatsapp_mensajes` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `from_number` VARCHAR(30) NOT NULL,
    `contact_name` VARCHAR(150) DEFAULT NULL,
    `tipo` VARCHAR(30) NOT NULL DEFAULT 'text',
    `body` TEXT DEFAULT NULL,
    `opcion_id` VARCHAR(100) DEFAULT NULL,
    `payload` JSON DEFAULT NULL,
    `leido` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
