-- =============================================
-- MIGRACIÓN: Dirección y enviado_por en whatsapp_mensajes
-- Ejecutar en phpMyAdmin sobre la BD estudiocontable
-- =============================================

ALTER TABLE `whatsapp_mensajes`
    ADD COLUMN `direccion` ENUM('entrada','salida') NOT NULL DEFAULT 'entrada' AFTER `tipo`,
    ADD COLUMN `enviado_por` VARCHAR(150) DEFAULT NULL AFTER `leido`;
