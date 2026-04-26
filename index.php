<?php

declare(strict_types=1);

// Load .env file if exists
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if (!getenv($key)) {
                putenv("{$key}={$value}");
            }
        }
    }
}

// Autoloader
spl_autoload_register(function (string $class) {
    $prefixes = [
        'Core\\' => __DIR__ . '/core/',
        'Controllers\\' => __DIR__ . '/controllers/',
        'Models\\' => __DIR__ . '/models/',
    ];
    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) === 0) {
            $relativeClass = substr($class, $len);
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
});

require __DIR__ . '/core/helpers.php';

use Core\Session;
use Core\Router;
use Core\Tenant;
use Core\CSRF;

// Timezone
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Start session
Session::start();

// Parse URI
$appConfig = require __DIR__ . '/config/app.php';
$basePath = rtrim($appConfig['base_path'], '/');
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($basePath !== '' && str_starts_with($requestUri, $basePath)) {
    $uri = substr($requestUri, strlen($basePath)) ?: '/';
} else {
    $uri = $requestUri ?: '/';
}
$uri = '/' . trim($uri, '/');
$method = $_SERVER['REQUEST_METHOD'];

// =============================================
// ROUTES
// =============================================

// Home - redirect to login or list
Router::get('/', function () {
    view('home');
});

// Home v2 (preview)
Router::get('/home1', function () {
    $pdo = Core\Database::master();
    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE publicado = 1 ORDER BY created_at DESC LIMIT 3");
    $stmt->execute();
    $recent_posts = $stmt->fetchAll();
    view('home1', ['recent_posts' => $recent_posts]);
});

// --- BLOG PÚBLICO (must be before /{slug}/... routes) ---
Router::get('/blog', function () {
    $controller = new Controllers\BlogController();
    $controller->publicIndex();
});

Router::get('/blog/{postSlug}', function (string $postSlug) {
    $controller = new Controllers\BlogController();
    $controller->publicShow($postSlug);
});

Router::post('/blog/{postSlug}/comentar', function (string $postSlug) {
    $controller = new Controllers\BlogController();
    $controller->storeComment($postSlug);
});

Router::post('/blog/{postSlug}/votar', function (string $postSlug) {
    $controller = new Controllers\BlogController();
    $controller->vote($postSlug);
});

// Tenant routes: /{slug}/...
Router::get('/{slug}/login', function (string $slug) {
    if (!Tenant::resolve($slug)) {
        http_response_code(404);
        view('errors.404');
        return;
    }
    if (Core\Auth::check()) {
        redirect(tenant_url('dashboard'));
    }
    view('auth.login', ['slug' => $slug]);
});

Router::post('/{slug}/login', function (string $slug) {
    if (!Tenant::resolve($slug)) {
        http_response_code(404);
        view('errors.404');
        return;
    }
    CSRF::check();

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!Core\RateLimit::check("login_{$slug}")) {
        Session::flash('error', 'Demasiados intentos. Intente nuevamente en unos minutos.');
        redirect(base_url("{$slug}/login"));
        return;
    }

    if (Core\Auth::attempt($email, $password)) {
        Core\RateLimit::clear("login_{$slug}");
        CSRF::regenerate();
        redirect(tenant_url('dashboard'));
    } else {
        Core\RateLimit::hit("login_{$slug}");
        Session::flash('error', 'Credenciales inválidas.');
        redirect(base_url("{$slug}/login"));
    }
});

Router::get('/{slug}/logout', function (string $slug) {
    Tenant::resolve($slug);
    Core\Auth::logout();
    redirect(base_url("{$slug}/login"));
});

// Dashboard
Router::get('/{slug}/dashboard', function (string $slug) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireLogin($slug);
    $controller = new Controllers\DashboardController();
    $controller->index();
});

// --- CLIENTES ---
Router::get('/{slug}/clientes', function (string $slug) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin', 'empleado');
    $controller = new Controllers\ClienteController();
    $controller->index();
});

Router::get('/{slug}/clientes/crear', function (string $slug) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin', 'empleado');
    $controller = new Controllers\ClienteController();
    $controller->create();
});

Router::post('/{slug}/clientes/store', function (string $slug) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin', 'empleado');
    CSRF::check();
    $controller = new Controllers\ClienteController();
    $controller->store();
});

Router::get('/{slug}/clientes/{id}/editar', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin', 'empleado');
    $controller = new Controllers\ClienteController();
    $controller->edit((int)$id);
});

Router::post('/{slug}/clientes/{id}/update', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin', 'empleado');
    CSRF::check();
    $controller = new Controllers\ClienteController();
    $controller->update((int)$id);
});

Router::post('/{slug}/clientes/{id}/delete', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin');
    CSRF::check();
    $controller = new Controllers\ClienteController();
    $controller->delete((int)$id);
});

Router::get('/{slug}/clientes/{id}/ver', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin', 'empleado');
    $controller = new Controllers\ClienteController();
    $controller->show((int)$id);
});

// API DataTables - Clientes
Router::get('/{slug}/api/clientes', function (string $slug) {
    if (!Tenant::resolve($slug)) { json_response(['error' => 'Not found'], 404); }
    Core\Auth::requireRole($slug, 'admin', 'empleado');
    $controller = new Controllers\ClienteController();
    $controller->datatable();
});

Router::get('/{slug}/api/clientes/export', function (string $slug) {
    if (!Tenant::resolve($slug)) { json_response(['error' => 'Not found'], 404); }
    Core\Auth::requireRole($slug, 'admin', 'empleado');
    $controller = new Controllers\ClienteController();
    $controller->exportData();
});

// --- CONDICIONES FISCALES ---
Router::get('/{slug}/condiciones-fiscales', function (string $slug) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin', 'empleado');
    $controller = new Controllers\CondicionFiscalController();
    $controller->index();
});

Router::post('/{slug}/condiciones-fiscales/store', function (string $slug) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin');
    CSRF::check();
    $controller = new Controllers\CondicionFiscalController();
    $controller->store();
});

Router::post('/{slug}/condiciones-fiscales/{id}/update', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin');
    CSRF::check();
    $controller = new Controllers\CondicionFiscalController();
    $controller->update((int)$id);
});

Router::post('/{slug}/condiciones-fiscales/{id}/delete', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin');
    CSRF::check();
    $controller = new Controllers\CondicionFiscalController();
    $controller->delete((int)$id);
});

// --- WHATSAPP PANEL ---
Router::get('/{slug}/whatsapp/conversacion/{numero}', function (string $slug, string $numero) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin');
    $controller = new Controllers\WhatsappController();
    $controller->conversacion($numero);
});

Router::post('/{slug}/whatsapp/mensajes/{id}/reenviar-equipo', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin');
    CSRF::check();
    $controller = new Controllers\WhatsappController();
    $controller->reenviarAEquipo((int)$id);
});

Router::get('/{slug}/whatsapp/mensajes', function (string $slug) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin');
    $controller = new Controllers\WhatsappController();
    $controller->mensajes();
});

Router::post('/{slug}/whatsapp/mensajes/{id}/reenviar-menu', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin');
    CSRF::check();
    $controller = new Controllers\WhatsappController();
    $controller->reenviarMenu((int)$id);
});

Router::post('/{slug}/whatsapp/enviar', function (string $slug) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin');
    CSRF::check();
    $controller = new Controllers\WhatsappController();
    $controller->enviarTextoManual();
});

// --- CONFIGURACIÓN ---
Router::get('/{slug}/configuracion', function (string $slug) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin');
    view('configuracion.index', ['pageTitle' => 'Configuración']);
});

Router::get('/{slug}/configuracion/impuestos', function (string $slug) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin');
    $controller = new Controllers\ImpuestoController();
    $controller->index();
});

Router::post('/{slug}/configuracion/impuestos/store', function (string $slug) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin');
    CSRF::check();
    $controller = new Controllers\ImpuestoController();
    $controller->store();
});

Router::post('/{slug}/configuracion/impuestos/{id}/update', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin');
    CSRF::check();
    $controller = new Controllers\ImpuestoController();
    $controller->update((int)$id);
});

Router::post('/{slug}/configuracion/impuestos/{id}/delete', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin');
    CSRF::check();
    $controller = new Controllers\ImpuestoController();
    $controller->delete((int)$id);
});

// --- EXENCIONES ---
Router::get('/{slug}/exenciones/vencimientos', function (string $slug) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin', 'empleado');
    $controller = new Controllers\ExencionController();
    $controller->vencimientos();
});

Router::post('/{slug}/clientes/{id}/exenciones/store', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin', 'empleado');
    CSRF::check();
    $controller = new Controllers\ExencionController();
    $controller->store((int)$id);
});

Router::post('/{slug}/clientes/{id}/exenciones/{exId}/update', function (string $slug, string $id, string $exId) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin', 'empleado');
    CSRF::check();
    $controller = new Controllers\ExencionController();
    $controller->update((int)$id, (int)$exId);
});

Router::post('/{slug}/clientes/{id}/exenciones/{exId}/delete', function (string $slug, string $id, string $exId) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin', 'empleado');
    CSRF::check();
    $controller = new Controllers\ExencionController();
    $controller->delete((int)$id, (int)$exId);
});

Router::get('/{slug}/clientes/{id}/exenciones/{exId}/descargar', function (string $slug, string $id, string $exId) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireLogin($slug);
    $controller = new Controllers\ExencionController();
    $controller->download((int)$id, (int)$exId);
});

// Asignar condición fiscal a cliente
Router::post('/{slug}/clientes/{id}/condicion-fiscal', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin', 'empleado');
    CSRF::check();
    $controller = new Controllers\CondicionFiscalController();
    $controller->assignToClient((int)$id);
});

// --- CLAVES FISCALES ---
Router::get('/{slug}/claves-fiscales', function (string $slug) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin', 'empleado');
    $controller = new Controllers\ClaveFiscalController();
    $controller->all();
});

Router::get('/{slug}/claves-fiscales/todas', function (string $slug) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin', 'empleado');
    $controller = new Controllers\ClaveFiscalController();
    $controller->allKeys();
});

Router::get('/{slug}/api/claves/search', function (string $slug) {
    if (!Tenant::resolve($slug)) { json_response(['error' => 'Not found'], 404); }
    Core\Auth::requireRole($slug, 'admin', 'empleado');
    $controller = new Controllers\ClaveFiscalController();
    $controller->search();
});

Router::get('/{slug}/clientes/{id}/claves', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireLogin($slug);
    $controller = new Controllers\ClaveFiscalController();
    $controller->index((int)$id);
});

Router::post('/{slug}/clientes/{id}/claves/store', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin', 'empleado');
    CSRF::check();
    $controller = new Controllers\ClaveFiscalController();
    $controller->store((int)$id);
});

Router::post('/{slug}/claves/{id}/update', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin', 'empleado');
    CSRF::check();
    $controller = new Controllers\ClaveFiscalController();
    $controller->update((int)$id);
});

Router::post('/{slug}/claves/{id}/delete', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin');
    CSRF::check();
    $controller = new Controllers\ClaveFiscalController();
    $controller->delete((int)$id);
});

Router::get('/{slug}/claves/{id}/decrypt', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { json_response(['error' => 'Not found'], 404); }
    Core\Auth::requireLogin($slug);
    $controller = new Controllers\ClaveFiscalController();
    $controller->decrypt((int)$id);
});

Router::get('/{slug}/claves/{id}/access-log', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { json_response(['error' => 'Not found'], 404); }
    Core\Auth::requireRole($slug, 'admin');
    $controller = new Controllers\ClaveFiscalController();
    $controller->accessLog((int)$id);
});

// --- DOCUMENTOS ---
Router::get('/{slug}/clientes/{id}/documentos', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireLogin($slug);
    $controller = new Controllers\DocumentoController();
    $controller->index((int)$id);
});

Router::post('/{slug}/clientes/{id}/documentos/upload', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin', 'empleado');
    CSRF::check();
    $controller = new Controllers\DocumentoController();
    $controller->upload((int)$id);
});

Router::get('/{slug}/documentos/{id}/download', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireLogin($slug);
    $controller = new Controllers\DocumentoController();
    $controller->download((int)$id);
});

Router::post('/{slug}/documentos/{id}/delete', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin');
    CSRF::check();
    $controller = new Controllers\DocumentoController();
    $controller->delete((int)$id);
});

// --- USUARIOS ---
Router::get('/{slug}/usuarios', function (string $slug) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin');
    $controller = new Controllers\UsuarioController();
    $controller->index();
});

Router::post('/{slug}/usuarios/store', function (string $slug) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin');
    CSRF::check();
    $controller = new Controllers\UsuarioController();
    $controller->store();
});

Router::post('/{slug}/usuarios/{id}/update', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin');
    CSRF::check();
    $controller = new Controllers\UsuarioController();
    $controller->update((int)$id);
});

Router::post('/{slug}/usuarios/{id}/delete', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin');
    CSRF::check();
    $controller = new Controllers\UsuarioController();
    $controller->delete((int)$id);
});

// --- AUDITORÍA ---
Router::get('/{slug}/auditoria', function (string $slug) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin');
    $controller = new Controllers\AuditController();
    $controller->index();
});

Router::get('/{slug}/api/auditoria', function (string $slug) {
    if (!Tenant::resolve($slug)) { json_response(['error' => 'Not found'], 404); }
    Core\Auth::requireRole($slug, 'admin');
    $controller = new Controllers\AuditController();
    $controller->datatable();
});

// --- PORTAL CLIENTE ---
Router::get('/{slug}/portal', function (string $slug) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'cliente');
    $controller = new Controllers\PortalController();
    $controller->index();
});

Router::get('/{slug}/portal/cliente/{id}', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'cliente');
    $controller = new Controllers\PortalController();
    $controller->clientDetail((int)$id);
});

// --- BLOG ADMIN ---
Router::get('/{slug}/blog', function (string $slug) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin');
    $controller = new Controllers\BlogController();
    $controller->adminIndex();
});

Router::get('/{slug}/blog/crear', function (string $slug) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin');
    $controller = new Controllers\BlogController();
    $controller->create();
});

Router::post('/{slug}/blog/store', function (string $slug) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin');
    CSRF::check();
    $controller = new Controllers\BlogController();
    $controller->store();
});

Router::get('/{slug}/blog/{id}/editar', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin');
    $controller = new Controllers\BlogController();
    $controller->edit((int)$id);
});

Router::post('/{slug}/blog/{id}/update', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin');
    CSRF::check();
    $controller = new Controllers\BlogController();
    $controller->update((int)$id);
});

Router::post('/{slug}/blog/{id}/delete', function (string $slug, string $id) {
    if (!Tenant::resolve($slug)) { http_response_code(404); view('errors.404'); return; }
    Core\Auth::requireRole($slug, 'admin');
    CSRF::check();
    $controller = new Controllers\BlogController();
    $controller->delete((int)$id);
});

// Dispatch
Router::dispatch($method, $uri);
