# Estudio Contable Casabene

Sistema web de gestión para el Estudio Contable Casabene.

## Stack Tecnológico
- **Backend:** PHP 8+ (PDO)
- **Base de datos:** MySQL 8+
- **Frontend:** Bootstrap 5
- **Tablas:** DataTables (server-side processing)
- **Cifrado:** AES-256-GCM para claves fiscales

## Instalación

### 1. Requisitos
- XAMPP con PHP 8+ y MySQL 8+
- Extensión OpenSSL habilitada
- mod_rewrite habilitado en Apache

### 2. Configurar Base de Datos

Ejecutar los scripts SQL en orden:

```bash
# 1. Crear base de datos maestra
mysql -u root < database/master.sql

# 2. Crear tablas tenant
mysql -u root < database/tenant.sql
```

### 3. Configurar Apache

Asegurar que `mod_rewrite` está habilitado y que el `AllowOverride All` está configurado para el directorio.

### 4. Configurar Aplicación

Editar `config/database.php` con las credenciales de MySQL.
Editar `config/app.php` con la URL base y la clave de cifrado.

### 5. Acceder

- **URL Principal:** `http://localhost/estudiocontable`
- **Login:** `http://localhost/estudiocontable/estudio/login`

## Estructura del Proyecto

```
estudiocontable/
├── config/          # Configuración (app, database)
├── controllers/     # Controladores MVC
├── core/            # Clases base (Database, Router, Auth, CSRF, etc.)
├── database/        # Scripts SQL (master + tenant)
├── storage/         # Archivos subidos
├── views/           # Vistas PHP
│   ├── layouts/     # Layout principal
│   ├── auth/        # Login
│   ├── dashboard/   # Panel principal
│   ├── clientes/    # ABM Clientes
│   ├── claves/      # Claves fiscales
│   ├── condiciones/ # Condiciones fiscales
│   ├── documentos/  # Gestión de documentos
│   ├── usuarios/    # ABM Usuarios
│   ├── auditoria/   # Logs de auditoría
│   ├── portal/      # Portal del cliente
│   └── errors/      # Páginas de error
├── .htaccess        # URL rewriting
└── index.php        # Entry point + rutas
```

## Roles y Permisos

| Función | Admin | Empleado | Cliente |
|---------|-------|----------|---------|
| Dashboard | ✅ | ✅ | ✅ |
| Gestión Clientes | ✅ | ✅ | ❌ |
| Eliminar Clientes | ✅ | ❌ | ❌ |
| Condiciones Fiscales | ✅ CRUD | ✅ Ver | ❌ |
| Claves Fiscales | ✅ | ✅ | ✅ (solo asignados) |
| Documentos | ✅ | ✅ | ✅ (solo asignados) |
| Usuarios | ✅ | ❌ | ❌ |
| Auditoría | ✅ | ❌ | ❌ |
| Portal Cliente | ❌ | ❌ | ✅ |

## Seguridad

- PDO con Prepared Statements (inyección SQL)
- Tokens CSRF en todos los formularios
- Contraseñas con `password_hash()` (bcrypt)
- Claves fiscales cifradas con AES-256-GCM
- Cookies HttpOnly + SameSite
- Rate limiting en login
- Auditoría completa de acciones
- Borrado lógico (sin borrado físico)
