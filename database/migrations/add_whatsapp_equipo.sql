-- =============================================
-- MIGRACIÓN: WhatsApp equipo
-- Ejecutar en phpMyAdmin sobre la BD estudiocontable
-- =============================================

-- 1. Agregar campo whatsapp a usuarios
ALTER TABLE `usuarios` ADD COLUMN `whatsapp` VARCHAR(30) DEFAULT NULL AFTER `email`;

-- 2. Tabla de reenvíos al equipo
CREATE TABLE IF NOT EXISTS `whatsapp_reenvios` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `mensaje_id` INT UNSIGNED NOT NULL,
    `destinatario_numero` VARCHAR(30) NOT NULL,
    `destinatario_nombre` VARCHAR(150) DEFAULT NULL,
    `enviado_por` VARCHAR(150) DEFAULT NULL,
    `nota` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`mensaje_id`) REFERENCES `whatsapp_mensajes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
