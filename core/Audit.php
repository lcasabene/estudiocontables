<?php

namespace Core;

class Audit
{
    public static function log(string $accion, string $entidad, ?int $entidadId = null): void
    {
        if (!Database::hasTenant()) {
            return;
        }

        $pdo = Database::tenant();
        $stmt = $pdo->prepare("INSERT INTO audit_log (usuario_id, accion, entidad, entidad_id, ip, user_agent, created_at) 
                               VALUES (:usuario_id, :accion, :entidad, :entidad_id, :ip, :user_agent, NOW())");
        $stmt->execute([
            'usuario_id' => Session::userId(),
            'accion' => $accion,
            'entidad' => $entidad,
            'entidad_id' => $entidadId,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
        ]);
    }
}
