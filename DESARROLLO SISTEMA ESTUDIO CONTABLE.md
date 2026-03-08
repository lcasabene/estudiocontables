# **SISTEMA SAAS DE GESTIÓN PARA ESTUDIOS CONTABLES**

Arquitectura Multi-Tenant con Base de Datos por Estudio

## **1\. OBJETIVO**

Desarrollar un sistema web SaaS para estudios contables con arquitectura multi-tenant (una base de datos por estudio), portal de clientes, gestión de claves fiscales con cifrado reversible seguro, gestión de documentos, condición fiscal configurable con historial, auditoría completa y borrado lógico obligatorio.

## **2\. STACK TECNOLÓGICO**

Backend: PHP 8+ (PDO obligatorio)

Base de datos: MySQL 8+

Frontend: Bootstrap 5

Tablas dinámicas: DataTables con server-side processing

Seguridad: password\_hash() para usuarios, AES-256-GCM para claves fiscales, CSRF, HTTPS y auditoría obligatoria.

## **3\. ARQUITECTURA MULTI-TENANT**

### **3.1 Base de Datos Maestra (SaaS)**

Tabla: estudios (id, nombre, slug UNIQUE, activo, created\_at, updated\_at)

Tabla: estudio\_db (estudio\_id, db\_host, db\_name, db\_user, db\_pass, created\_at)

Acceso mediante https://app.dominio.com/{slug} para enrutar al tenant correspondiente.

### **3.2 Base de Datos Tenant (una por estudio)**

Cada estudio posee su base de datos aislada para garantizar seguridad y escalabilidad.

## **4\. MODELO DE DATOS TENANT**

### **4.1 Usuarios**

usuarios: id, nombre\_completo, email UNIQUE, password\_hash, rol, activo, created\_at, updated\_at.

### **4.2 Clientes**

clientes: id, razon\_social, cuit UNIQUE, email, telefono, direccion, url\_carpeta\_drive, activo, created\_at, updated\_at.

### **4.3 Relación Usuarios ↔ Clientes**

cliente\_usuarios: id, cliente\_id, usuario\_id, perfil, activo, created\_at, updated\_at. UNIQUE(cliente\_id, usuario\_id).

### **4.4 Condiciones Fiscales**

condiciones\_fiscales: id, nombre, activo, created\_at, updated\_at.

cliente\_condicion\_fiscal: id, cliente\_id, condicion\_fiscal\_id, fecha\_desde, fecha\_hasta, observaciones, activo.

### **4.5 Documentos**

documentos: id, cliente\_id, titulo, tipo, storage, ruta\_archivo, url, mime\_type, tamano, hash\_sha256, activo, created\_at, updated\_at.

### **4.6 Claves Fiscales**

claves\_fiscales: id, cliente\_id, referencia, usuario\_enc, password\_enc, iv, tag, url\_sitio, observaciones, activo, created\_at, updated\_at. Cifrado obligatorio AES-256-GCM.

### **4.7 Auditoría**

audit\_log: id, usuario\_id, accion, entidad, entidad\_id, ip, user\_agent, created\_at. Registrar accesos a claves y modificaciones importantes.

## **5\. PERMISOS**

Admin Estudio: acceso completo.

Empleado: gestión operativa.

Cliente: solo acceso a clientes asignados y sus documentos/claves.

## **6\. BORRADO LÓGICO**

Todas las tablas operativas deben tener campo activo. No se permite borrado físico.

## **7\. SEGURIDAD OBLIGATORIA**

PDO \+ Prepared Statements.

CSRF tokens.

HTTPS obligatorio.

Cookies seguras HttpOnly \+ SameSite.

Rate limiting en login.

Auditoría obligatoria.

## **8\. FASES DE IMPLEMENTACIÓN**

1\. Crear DB maestra.

2\. Crear modelo tenant.

3\. Implementar login \+ routing por slug.

4\. Implementar ABM clientes.

5\. Implementar condiciones fiscales.

6\. Implementar claves con cifrado.

7\. Implementar documentos.

8\. Implementar portal cliente.

9\. Implementar auditoría completa.

