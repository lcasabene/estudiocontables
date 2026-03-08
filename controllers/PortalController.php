<?php

namespace Controllers;

use Core\Database;
use Core\Session;
use Core\Audit;

class PortalController
{
    public function index(): void
    {
        $pdo = Database::tenant();
        $userId = Session::userId();

        $stmt = $pdo->prepare("SELECT c.* FROM clientes c 
                               JOIN cliente_usuarios cu ON c.id = cu.cliente_id 
                               WHERE cu.usuario_id = :uid AND cu.activo = 1 AND c.activo = 1 
                               ORDER BY c.razon_social");
        $stmt->execute(['uid' => $userId]);
        $clientes = $stmt->fetchAll();

        Audit::log('acceso_portal', 'usuarios', $userId);
        view('portal.index', ['clientes' => $clientes]);
    }

    public function clientDetail(int $clienteId): void
    {
        $pdo = Database::tenant();
        $userId = Session::userId();

        // Verify access
        $access = $pdo->prepare("SELECT 1 FROM cliente_usuarios WHERE cliente_id = :cid AND usuario_id = :uid AND activo = 1");
        $access->execute(['cid' => $clienteId, 'uid' => $userId]);
        if (!$access->fetch()) {
            http_response_code(403);
            die('Acceso denegado.');
        }

        $cliente = $pdo->prepare("SELECT * FROM clientes WHERE id = :id AND activo = 1");
        $cliente->execute(['id' => $clienteId]);
        $cliente = $cliente->fetch();

        if (!$cliente) {
            http_response_code(404);
            view('errors.404');
            return;
        }

        // Fiscal condition
        $cfStmt = $pdo->prepare("SELECT ccf.*, cf.nombre as condicion_nombre 
                                  FROM cliente_condicion_fiscal ccf 
                                  JOIN condiciones_fiscales cf ON ccf.condicion_fiscal_id = cf.id 
                                  WHERE ccf.cliente_id = :id AND ccf.activo = 1 
                                  ORDER BY ccf.fecha_desde DESC LIMIT 1");
        $cfStmt->execute(['id' => $clienteId]);
        $condicionActual = $cfStmt->fetch();

        // Documents
        $docs = $pdo->prepare("SELECT * FROM documentos WHERE cliente_id = :id AND activo = 1 ORDER BY created_at DESC");
        $docs->execute(['id' => $clienteId]);
        $documentos = $docs->fetchAll();

        // Keys
        $keys = $pdo->prepare("SELECT * FROM claves_fiscales WHERE cliente_id = :id AND activo = 1 ORDER BY referencia");
        $keys->execute(['id' => $clienteId]);
        $claves = $keys->fetchAll();

        Audit::log('ver_detalle_portal', 'clientes', $clienteId);

        view('portal.detail', [
            'cliente' => $cliente,
            'condicionActual' => $condicionActual,
            'documentos' => $documentos,
            'claves' => $claves,
        ]);
    }
}
