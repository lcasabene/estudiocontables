<?php

use Core\CSRF;
use Core\Session;
use Core\Tenant;

function base_url(string $path = ''): string
{
    $config = require __DIR__ . '/../config/app.php';
    return rtrim($config['url'], '/') . '/' . ltrim($path, '/');
}

function tenant_url(string $path = ''): string
{
    $slug = Tenant::slug();
    return base_url($slug . '/' . ltrim($path, '/'));
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function csrf_field(): string
{
    return CSRF::field();
}

function csrf_token(): string
{
    return CSRF::generate();
}

function old(string $key, string $default = ''): string
{
    return e($_POST[$key] ?? $default);
}

function redirect(string $url): void
{
    header("Location: {$url}");
    exit;
}

function flash(string $key, $value): void
{
    Session::flash($key, $value);
}

function get_flash(string $key, $default = null)
{
    return Session::getFlash($key, $default);
}

function view(string $viewPath, array $data = []): void
{
    extract($data);
    $viewFile = __DIR__ . '/../views/' . str_replace('.', '/', $viewPath) . '.php';
    if (!file_exists($viewFile)) {
        throw new RuntimeException("View not found: {$viewPath}");
    }
    require $viewFile;
}

function json_response(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function is_admin(): bool
{
    return Session::userRole() === 'admin';
}

function is_empleado(): bool
{
    return in_array(Session::userRole(), ['admin', 'empleado']);
}

function is_cliente(): bool
{
    return Session::userRole() === 'cliente';
}

function format_date(?string $date): string
{
    if (!$date) return '';
    return date('d/m/Y', strtotime($date));
}

function format_datetime(?string $date): string
{
    if (!$date) return '';
    return date('d/m/Y H:i', strtotime($date));
}
