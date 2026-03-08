<?php

namespace Core;

class Auth
{
    public static function attempt(string $email, string $password): bool
    {
        $pdo = Database::tenant();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email AND activo = 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }

        Session::set('user_id', (int)$user['id']);
        Session::set('user_name', $user['nombre_completo']);
        Session::set('user_email', $user['email']);
        Session::set('user_role', $user['rol']);

        Audit::log('login', 'usuarios', (int)$user['id']);

        return true;
    }

    public static function logout(): void
    {
        Audit::log('logout', 'usuarios', Session::userId());
        Session::destroy();
    }

    public static function check(): bool
    {
        return Session::isLoggedIn();
    }

    public static function requireLogin(string $slug): void
    {
        if (!self::check()) {
            header("Location: /{$slug}/login");
            exit;
        }
    }

    public static function requireRole(string $slug, string ...$roles): void
    {
        self::requireLogin($slug);
        if (!in_array(Session::userRole(), $roles)) {
            http_response_code(403);
            die('Access denied. Insufficient permissions.');
        }
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        return [
            'id' => Session::userId(),
            'name' => Session::get('user_name'),
            'email' => Session::get('user_email'),
            'role' => Session::userRole(),
        ];
    }
}
