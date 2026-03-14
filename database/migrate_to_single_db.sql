-- =============================================
-- MIGRACIÓN: De 2 bases (saas_master + tenant) a 1 sola base
-- 
-- INSTRUCCIONES:
-- 1. Hacer backup de ambas bases antes de ejecutar
-- 2. Ejecutar este script en MySQL/phpMyAdmin
-- 3. Actualizar config/database.php si es necesario
-- 4. Actualizar el registro en estudio_db para que apunte a la nueva DB
-- =============================================

-- Paso 1: Crear la base unificada si no existe
CREATE DATABASE IF NOT EXISTS `estudiocontable` 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

-- Paso 2: Copiar tablas master desde saas_master
-- (Si saas_master no existe, saltear estos pasos)

-- Copiar estructura y datos de estudios
CREATE TABLE IF NOT EXISTS `estudiocontable`.`estudios` LIKE `saas_master`.`estudios`;
INSERT IGNORE INTO `estudiocontable`.`estudios` SELECT * FROM `saas_master`.`estudios`;

CREATE TABLE IF NOT EXISTS `estudiocontable`.`estudio_db` LIKE `saas_master`.`estudio_db`;
INSERT IGNORE INTO `estudiocontable`.`estudio_db` SELECT * FROM `saas_master`.`estudio_db`;

CREATE TABLE IF NOT EXISTS `estudiocontable`.`login_attempts` LIKE `saas_master`.`login_attempts`;
INSERT IGNORE INTO `estudiocontable`.`login_attempts` SELECT * FROM `saas_master`.`login_attempts`;

-- Paso 3: Copiar tablas tenant

CREATE TABLE IF NOT EXISTS `estudiocontable`.`usuarios` LIKE `tenant_estudio`.`usuarios`;
INSERT IGNORE INTO `estudiocontable`.`usuarios` SELECT * FROM `tenant_estudio`.`usuarios`;

CREATE TABLE IF NOT EXISTS `estudiocontable`.`clientes` LIKE `tenant_estudio`.`clientes`;
INSERT IGNORE INTO `estudiocontable`.`clientes` SELECT * FROM `tenant_estudio`.`clientes`;

CREATE TABLE IF NOT EXISTS `estudiocontable`.`cliente_usuarios` LIKE `tenant_estudio`.`cliente_usuarios`;
INSERT IGNORE INTO `estudiocontable`.`cliente_usuarios` SELECT * FROM `tenant_estudio`.`cliente_usuarios`;

CREATE TABLE IF NOT EXISTS `estudiocontable`.`condiciones_fiscales` LIKE `tenant_estudio`.`condiciones_fiscales`;
INSERT IGNORE INTO `estudiocontable`.`condiciones_fiscales` SELECT * FROM `tenant_estudio`.`condiciones_fiscales`;

CREATE TABLE IF NOT EXISTS `estudiocontable`.`cliente_condicion_fiscal` LIKE `tenant_estudio`.`cliente_condicion_fiscal`;
INSERT IGNORE INTO `estudiocontable`.`cliente_condicion_fiscal` SELECT * FROM `tenant_estudio`.`cliente_condicion_fiscal`;

CREATE TABLE IF NOT EXISTS `estudiocontable`.`documentos` LIKE `tenant_estudio`.`documentos`;
INSERT IGNORE INTO `estudiocontable`.`documentos` SELECT * FROM `tenant_estudio`.`documentos`;

CREATE TABLE IF NOT EXISTS `estudiocontable`.`claves_fiscales` LIKE `tenant_estudio`.`claves_fiscales`;
INSERT IGNORE INTO `estudiocontable`.`claves_fiscales` SELECT * FROM `tenant_estudio`.`claves_fiscales`;

CREATE TABLE IF NOT EXISTS `estudiocontable`.`audit_log` LIKE `tenant_estudio`.`audit_log`;
INSERT IGNORE INTO `estudiocontable`.`audit_log` SELECT * FROM `tenant_estudio`.`audit_log`;

-- Paso 4: Agregar columnas nuevas si no existen (categoria, ultimo_acceso en claves_fiscales)
ALTER TABLE `estudiocontable`.`claves_fiscales` 
    ADD COLUMN IF NOT EXISTS `categoria` VARCHAR(50) NOT NULL DEFAULT 'otros' AFTER `referencia`,
    ADD COLUMN IF NOT EXISTS `ultimo_acceso` DATETIME DEFAULT NULL AFTER `observaciones`;

-- Paso 5: Actualizar estudio_db para que apunte a la base unificada
UPDATE `estudiocontable`.`estudio_db` SET `db_name` = 'estudiocontable' WHERE `estudio_id` = 1;

-- =============================================
-- LISTO! Ahora la app usa una sola base de datos.
-- Las bases saas_master y tenant_estudio se pueden 
-- eliminar manualmente después de verificar que todo funcione.
-- =============================================
