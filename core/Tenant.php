<?php

namespace Core;

class Tenant
{
    private static ?array $currentEstudio = null;

    public static function resolve(string $slug): bool
    {
        $pdo = Database::master();
        $stmt = $pdo->prepare("SELECT e.*, ed.db_host, ed.db_name, ed.db_user, ed.db_pass 
                               FROM estudios e 
                               JOIN estudio_db ed ON e.id = ed.estudio_id 
                               WHERE e.slug = :slug AND e.activo = 1");
        $stmt->execute(['slug' => $slug]);
        $estudio = $stmt->fetch();

        if (!$estudio) {
            return false;
        }

        self::$currentEstudio = $estudio;
        Database::setTenant($estudio['db_host'], $estudio['db_name'], $estudio['db_user'], $estudio['db_pass']);
        Session::set('tenant_slug', $slug);
        Session::set('tenant_id', $estudio['id']);
        Session::set('tenant_name', $estudio['nombre']);

        return true;
    }

    public static function current(): ?array
    {
        return self::$currentEstudio;
    }

    public static function slug(): ?string
    {
        return Session::get('tenant_slug');
    }

    public static function id(): ?int
    {
        return Session::get('tenant_id');
    }

    public static function name(): ?string
    {
        return Session::get('tenant_name');
    }

    public static function ensureResolved(): void
    {
        if (!Database::hasTenant()) {
            $slug = Session::get('tenant_slug');
            if ($slug) {
                self::resolve($slug);
            }
        }
    }
}
