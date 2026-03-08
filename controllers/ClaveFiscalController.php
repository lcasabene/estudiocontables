<?php

namespace Controllers;

use Core\Database;
use Core\Session;
use Core\Audit;
use Core\Encryption;

class ClaveFiscalController
{
    public const CATEGORIAS = [
        'afip' => ['label' => 'AFIP', 'icon' => 'bank', 'color' => 'primary'],
        'arba' => ['label' => 'ARBA', 'icon' => 'building-check', 'color' => 'success'],
        'agip' => ['label' => 'AGIP', 'icon' => 'building', 'color' => 'info'],
        'rentas' => ['label' => 'Rentas', 'icon' => 'cash-stack', 'color' => 'warning'],
        'banco' => ['label' => 'Banco', 'icon' => 'safe', 'color' => 'dark'],
        'email' => ['label' => 'Email', 'icon' => 'envelope', 'color' => 'danger'],
        'portal' => ['label' => 'Portal Web', 'icon' => 'globe', 'color' => 'secondary'],
        'otros' => ['label' => 'Otros', 'icon' => 'key', 'color' => 'secondary'],
    ];

    public function allKeys(): void
    {
        $pdo = Database::tenant();

        $stmt = $pdo->query("
            SELECT cf.*, c.razon_social, c.cuit 
            FROM claves_fiscales cf 
            INNER JOIN clientes c ON c.id = cf.cliente_id AND c.activo = 1
            WHERE cf.activo = 1 
            ORDER BY c.razon_social, cf.categoria, cf.referencia
        ");
        $claves = $stmt->fetchAll();

        // Group by client
        $grouped = [];
        foreach ($claves as $clave) {
            $cid = $clave['cliente_id'];
            if (!isset($grouped[$cid])) {
                $grouped[$cid] = [
                    'cliente_id' => $cid,
                    'razon_social' => $clave['razon_social'],
                    'cuit' => $clave['cuit'],
                    'claves' => [],
                ];
            }
            $grouped[$cid]['claves'][] = $clave;
        }

        view('claves.all-keys', [
            'grouped' => $grouped,
            'totalClaves' => count($claves),
            'totalClientes' => count($grouped),
            'categorias' => self::CATEGORIAS,
        ]);
    }

    public function all(): void
    {
        $pdo = Database::tenant();

        // Get all active clients for the dropdown
        $clientes = $pdo->query("SELECT id, razon_social, cuit FROM clientes WHERE activo = 1 ORDER BY razon_social")->fetchAll();

        // If a client is selected via query param, load their keys
        $clienteId = isset($_GET['cliente_id']) ? (int)$_GET['cliente_id'] : null;
        $clienteActivo = null;
        $claves = [];

        if ($clienteId) {
            $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = :id AND activo = 1");
            $stmt->execute(['id' => $clienteId]);
            $clienteActivo = $stmt->fetch();

            if ($clienteActivo) {
                $stmt = $pdo->prepare("SELECT * FROM claves_fiscales WHERE cliente_id = :id AND activo = 1 ORDER BY categoria, referencia");
                $stmt->execute(['id' => $clienteId]);
                $claves = $stmt->fetchAll();
            }
        }

        view('claves.all', [
            'clientes' => $clientes,
            'clienteActivo' => $clienteActivo,
            'claves' => $claves,
            'categorias' => self::CATEGORIAS,
        ]);
    }

    public function search(): void
    {
        $pdo = Database::tenant();
        $q = trim($_GET['q'] ?? '');

        if (strlen($q) < 2) {
            json_response(['results' => []]);
            return;
        }

        $stmt = $pdo->prepare("SELECT c.id, c.razon_social, c.cuit, COUNT(cf.id) as total_claves 
                                FROM clientes c 
                                LEFT JOIN claves_fiscales cf ON cf.cliente_id = c.id AND cf.activo = 1
                                WHERE c.activo = 1 AND (c.razon_social LIKE :q OR c.cuit LIKE :q2)
                                GROUP BY c.id 
                                ORDER BY c.razon_social LIMIT 10");
        $stmt->execute(['q' => "%{$q}%", 'q2' => "%{$q}%"]);

        json_response(['results' => $stmt->fetchAll()]);
    }

    public function index(int $clienteId): void
    {
        $pdo = Database::tenant();

        $cliente = $pdo->prepare("SELECT * FROM clientes WHERE id = :id AND activo = 1");
        $cliente->execute(['id' => $clienteId]);
        $cliente = $cliente->fetch();

        if (!$cliente) {
            http_response_code(404);
            view('errors.404');
            return;
        }

        $stmt = $pdo->prepare("SELECT * FROM claves_fiscales WHERE cliente_id = :id AND activo = 1 ORDER BY categoria, referencia");
        $stmt->execute(['id' => $clienteId]);
        $claves = $stmt->fetchAll();

        view('claves.index', [
            'cliente' => $cliente,
            'claves' => $claves,
            'categorias' => self::CATEGORIAS,
        ]);
    }

    public function store(int $clienteId): void
    {
        $pdo = Database::tenant();

        $referencia = trim($_POST['referencia'] ?? '');
        $categoria = trim($_POST['categoria'] ?? 'otros');
        $usuario = trim($_POST['usuario'] ?? '');
        $password = $_POST['password_clave'] ?? '';
        $urlSitio = trim($_POST['url_sitio'] ?? '') ?: null;
        $observaciones = trim($_POST['observaciones'] ?? '') ?: null;

        $redirectTo = $_POST['redirect_to'] ?? tenant_url("clientes/{$clienteId}/claves");

        if ($referencia === '' || $usuario === '' || $password === '') {
            Session::flash('error', 'Referencia, usuario y contraseña son obligatorios.');
            redirect($redirectTo);
            return;
        }

        if (!array_key_exists($categoria, self::CATEGORIAS)) {
            $categoria = 'otros';
        }

        // Check duplicate referencia for this client
        $dupCheck = $pdo->prepare("SELECT id FROM claves_fiscales WHERE cliente_id = :cid AND referencia = :ref AND activo = 1");
        $dupCheck->execute(['cid' => $clienteId, 'ref' => $referencia]);
        if ($dupCheck->fetch()) {
            Session::flash('error', 'Ya existe una clave con esa referencia para este cliente.');
            redirect($redirectTo);
            return;
        }

        $encUser = Encryption::encrypt($usuario);
        $encPass = Encryption::encrypt($password);

        $stmt = $pdo->prepare("INSERT INTO claves_fiscales 
            (cliente_id, referencia, categoria, usuario_enc, password_enc, iv, tag, url_sitio, observaciones) 
            VALUES (:cliente_id, :referencia, :categoria, :usuario_enc, :password_enc, :iv, :tag, :url_sitio, :observaciones)");

        $stmt->execute([
            'cliente_id' => $clienteId,
            'referencia' => $referencia,
            'categoria' => $categoria,
            'usuario_enc' => $encUser['data'] . '|' . $encUser['iv'] . '|' . $encUser['tag'],
            'password_enc' => $encPass['data'] . '|' . $encPass['iv'] . '|' . $encPass['tag'],
            'iv' => $encUser['iv'],
            'tag' => $encUser['tag'],
            'url_sitio' => $urlSitio,
            'observaciones' => $observaciones,
        ]);

        $id = (int)$pdo->lastInsertId();
        Audit::log('crear_clave_fiscal', 'claves_fiscales', $id);
        Session::flash('success', 'Clave fiscal guardada exitosamente.');
        redirect($redirectTo);
    }

    public function update(int $id): void
    {
        $pdo = Database::tenant();

        $stmt = $pdo->prepare("SELECT * FROM claves_fiscales WHERE id = :id AND activo = 1");
        $stmt->execute(['id' => $id]);
        $clave = $stmt->fetch();

        if (!$clave) {
            http_response_code(404);
            view('errors.404');
            return;
        }

        $redirectTo = $_POST['redirect_to'] ?? tenant_url("clientes/{$clave['cliente_id']}/claves");
        $referencia = trim($_POST['referencia'] ?? '');
        $categoria = trim($_POST['categoria'] ?? $clave['categoria']);
        $usuario = trim($_POST['usuario'] ?? '');
        $password = $_POST['password_clave'] ?? '';
        $urlSitio = trim($_POST['url_sitio'] ?? '') ?: null;
        $observaciones = trim($_POST['observaciones'] ?? '') ?: null;

        if (!array_key_exists($categoria, self::CATEGORIAS)) {
            $categoria = 'otros';
        }

        $updateFields = [
            'referencia' => $referencia,
            'categoria' => $categoria,
            'url_sitio' => $urlSitio,
            'observaciones' => $observaciones,
            'id' => $id,
        ];

        $sql = "UPDATE claves_fiscales SET referencia = :referencia, categoria = :categoria, 
                url_sitio = :url_sitio, observaciones = :observaciones, updated_at = NOW()";

        if ($usuario !== '') {
            $encUser = Encryption::encrypt($usuario);
            $sql .= ", usuario_enc = :usuario_enc, iv = :iv, tag = :tag";
            $updateFields['usuario_enc'] = $encUser['data'] . '|' . $encUser['iv'] . '|' . $encUser['tag'];
            $updateFields['iv'] = $encUser['iv'];
            $updateFields['tag'] = $encUser['tag'];
        }

        if ($password !== '') {
            $encPass = Encryption::encrypt($password);
            $sql .= ", password_enc = :password_enc";
            $updateFields['password_enc'] = $encPass['data'] . '|' . $encPass['iv'] . '|' . $encPass['tag'];
        }

        $sql .= " WHERE id = :id AND activo = 1";
        $pdo->prepare($sql)->execute($updateFields);

        Audit::log('editar_clave_fiscal', 'claves_fiscales', $id);
        Session::flash('success', 'Clave fiscal actualizada.');
        redirect($redirectTo);
    }

    public function delete(int $id): void
    {
        $pdo = Database::tenant();

        $stmt = $pdo->prepare("SELECT cliente_id FROM claves_fiscales WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $clave = $stmt->fetch();

        $pdo->prepare("UPDATE claves_fiscales SET activo = 0, updated_at = NOW() WHERE id = :id")
            ->execute(['id' => $id]);

        Audit::log('eliminar_clave_fiscal', 'claves_fiscales', $id);
        Session::flash('success', 'Clave fiscal eliminada.');

        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (str_contains($referer, 'claves-fiscales')) {
            redirect(tenant_url("claves-fiscales?cliente_id={$clave['cliente_id']}"));
        } else {
            redirect(tenant_url("clientes/{$clave['cliente_id']}/claves"));
        }
    }

    public function decrypt(int $id): void
    {
        $pdo = Database::tenant();

        $stmt = $pdo->prepare("SELECT * FROM claves_fiscales WHERE id = :id AND activo = 1");
        $stmt->execute(['id' => $id]);
        $clave = $stmt->fetch();

        if (!$clave) {
            json_response(['error' => 'Clave no encontrada'], 404);
            return;
        }

        // Check client access for cliente role
        if (is_cliente()) {
            $access = $pdo->prepare("SELECT 1 FROM cliente_usuarios WHERE cliente_id = :cid AND usuario_id = :uid AND activo = 1");
            $access->execute(['cid' => $clave['cliente_id'], 'uid' => Session::userId()]);
            if (!$access->fetch()) {
                json_response(['error' => 'Acceso denegado'], 403);
                return;
            }
        }

        try {
            // Parse stored format: data|iv|tag
            $userParts = explode('|', $clave['usuario_enc']);
            $passParts = explode('|', $clave['password_enc']);

            $usuario = Encryption::decrypt($userParts[0], $userParts[1], $userParts[2]);
            $password = Encryption::decrypt($passParts[0], $passParts[1], $passParts[2]);

            // Update last access timestamp
            $pdo->prepare("UPDATE claves_fiscales SET ultimo_acceso = NOW() WHERE id = :id")
                ->execute(['id' => $id]);

            Audit::log('ver_clave_fiscal', 'claves_fiscales', $id);

            json_response([
                'usuario' => $usuario,
                'password' => $password,
            ]);
        } catch (\Exception $e) {
            json_response(['error' => 'Error al descifrar la clave.'], 500);
        }
    }

    public function accessLog(int $id): void
    {
        $pdo = Database::tenant();

        $logs = $pdo->prepare("SELECT al.*, u.nombre_completo 
                               FROM audit_log al 
                               LEFT JOIN usuarios u ON al.usuario_id = u.id 
                               WHERE al.entidad = 'claves_fiscales' AND al.entidad_id = :id 
                               AND al.accion = 'ver_clave_fiscal'
                               ORDER BY al.created_at DESC LIMIT 20");
        $logs->execute(['id' => $id]);

        json_response(['logs' => $logs->fetchAll()]);
    }
}
