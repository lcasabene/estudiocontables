<?php

namespace Core;

class CSRF
{
    public static function generate(): string
    {
        if (!Session::has('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('csrf_token');
    }

    public static function field(): string
    {
        $token = self::generate();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    public static function validate(?string $token): bool
    {
        $sessionToken = Session::get('csrf_token');
        if ($sessionToken === null || $token === null) {
            return false;
        }
        return hash_equals($sessionToken, $token);
    }

    public static function check(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            if (!self::validate($token)) {
                http_response_code(403);
                die('Invalid CSRF token.');
            }
        }
    }

    public static function regenerate(): void
    {
        Session::set('csrf_token', bin2hex(random_bytes(32)));
    }
}
