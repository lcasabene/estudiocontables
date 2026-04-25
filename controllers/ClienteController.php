<?php

namespace Controllers;

use Core\Database;
use Core\Session;
use Core\Audit;

class ClienteController
{
    public function index(): void
    {
        $pdo = Database::tenant();
        $condiciones = $pdo->query("SELECT * FROM condiciones_fiscales WHERE activo = 1 ORDER BY nombre")->fetchAll();
        view('clientes.index', ['condiciones' => $condiciones]);
    }

    public function datatable(): void
    {
        $pdo = Database::tenant();

        $draw = (int)($_GET['draw'] ?? 1);
        $start = (int)($_GET['start'] ?? 0);
        $length = (int)($_GET['length'] ?? 10);
        $search = $_GET['search']['value'] ?? '';
        $condicionFilter = trim($_GET['condicion_fiscal'] ?? '');

        $totalStmt = $pdo->query("SELECT COUNT(*) FROM clientes WHERE activo = 1");
        $totalRecords = (int)$totalStmt->fetchColumn();

        // Build base query with subquery for condicion fiscal
        $baseSelect = "SELECT c.*, 
                (SELECT cf.nombre FROM cliente_condicion_fiscal ccf 
                 JOIN condiciones_fiscales cf ON ccf.condicion_fiscal_id = cf.id 
                 WHERE ccf.cliente_id = c.id AND ccf.activo = 1 AND ccf.fecha_hasta IS NULL 
                 ORDER BY ccf.fecha_desde DESC LIMIT 1) as condicion_fiscal,
                (SELECT cf.id FROM cliente_condicion_fiscal ccf 
                 JOIN condiciones_fiscales cf ON ccf.condicion_fiscal_id = cf.id 
                 WHERE ccf.cliente_id = c.id AND ccf.activo = 1 AND ccf.fecha_hasta IS NULL 
                 ORDER BY ccf.fecha_desde DESC LIMIT 1) as condicion_fiscal_id
                FROM clientes c";

        $where = "WHERE c.activo = 1";
        $params = [];

        if ($search !== '') {
            $where .= " AND (c.razon_social LIKE :search OR c.cuit LIKE :search2 OR c.email LIKE :search3)";
            $params['search'] = "%{$search}%";
            $params['search2'] = "%{$search}%";
            $params['search3'] = "%{$search}%";
        }

        // Filter by condicion fiscal
        if ($condicionFilter !== '') {
            if ($condicionFilter === '__none__') {
                $where .= " AND NOT EXISTS (
                    SELECT 1 FROM cliente_condicion_fiscal ccf2 
                    WHERE ccf2.cliente_id = c.id AND ccf2.activo = 1 AND ccf2.fecha_hasta IS NULL)";
            } else {
                $where .= " AND EXISTS (
                    SELECT 1 FROM cliente_condicion_fiscal ccf2 
                    JOIN condiciones_fiscales cf2 ON ccf2.condicion_fiscal_id = cf2.id
                    WHERE ccf2.cliente_id = c.id AND ccf2.activo = 1 AND ccf2.fecha_hasta IS NULL 
                    AND cf2.id = :cf_id)";
                $params['cf_id'] = (int)$condicionFilter;
            }
        }

        // Count filtered
        $countSql = "SELECT COUNT(*) FROM clientes c {$where}";
        $filteredStmt = $pdo->prepare($countSql);
        $filteredStmt->execute($params);
        $filteredRecords = (int)$filteredStmt->fetchColumn();

        // Ordering
        $columns = ['id', 'razon_social', 'cuit', 'email', 'condicion_fiscal', 'telefono'];
        $orderIdx = (int)($_GET['order'][0]['column'] ?? 1);
        $orderCol = $columns[$orderIdx] ?? 'razon_social';
        $orderDir = ($_GET['order'][0]['dir'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';

        // Wrap in subquery for ordering by alias
        $sql = "SELECT sub.* FROM ({$baseSelect} {$where}) sub ORDER BY sub.{$orderCol} {$orderDir} LIMIT :start, :length";

        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':start', $start, \PDO::PARAM_INT);
        $stmt->bindValue(':length', $length, \PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll();

        json_response([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    public function exportData(): void
    {
        $pdo = Database::tenant();
        $condicionFilter = trim($_GET['condicion_fiscal'] ?? '');
        $search = trim($_GET['search'] ?? '');

        $where = "WHERE c.activo = 1";
        $params = [];

        if ($search !== '') {
            $where .= " AND (c.razon_social LIKE :search OR c.cuit LIKE :search2 OR c.email LIKE :search3)";
            $params['search'] = "%{$search}%";
            $params['search2'] = "%{$search}%";
            $params['search3'] = "%{$search}%";
        }

        if ($condicionFilter !== '') {
            if ($condicionFilter === '__none__') {
                $where .= " AND NOT EXISTS (
                    SELECT 1 FROM cliente_condicion_fiscal ccf2 
                    WHERE ccf2.cliente_id = c.id AND ccf2.activo = 1 AND ccf2.fecha_hasta IS NULL)";
            } else {
                $where .= " AND EXISTS (
                    SELECT 1 FROM cliente_condicion_fiscal ccf2 
                    JOIN condiciones_fiscales cf2 ON ccf2.condicion_fiscal_id = cf2.id
                    WHERE ccf2.cliente_id = c.id AND ccf2.activo = 1 AND ccf2.fecha_hasta IS NULL 
                    AND cf2.id = :cf_id)";
                $params['cf_id'] = (int)$condicionFilter;
            }
        }

        $sql = "SELECT c.id, c.razon_social, c.cuit, c.email, c.telefono, c.direccion,
                (SELECT cf.nombre FROM cliente_condicion_fiscal ccf 
                 JOIN condiciones_fiscales cf ON ccf.condicion_fiscal_id = cf.id 
                 WHERE ccf.cliente_id = c.id AND ccf.activo = 1 AND ccf.fecha_hasta IS NULL 
                 ORDER BY ccf.fecha_desde DESC LIMIT 1) as condicion_fiscal
                FROM clientes c {$where} ORDER BY c.razon_social";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        json_response(['data' => $stmt->fetchAll()]);
    }

    public function create(): void
    {
        $pdo = Database::tenant();
        $condiciones = $pdo->query("SELECT * FROM condiciones_fiscales WHERE activo = 1 ORDER BY nombre")->fetchAll();
        view('clientes.form', ['cliente' => null, 'condiciones' => $condiciones]);
    }

    public function store(): void
    {
        $pdo = Database::tenant();
        $slug = \Core\Tenant::slug();

        $data = $this->validateInput();

        $stmt = $pdo->prepare("INSERT INTO clientes (razon_social, cuit, email, telefono, direccion, url_carpeta_drive, situacion_ib, jurisdiccion_sede) 
                               VALUES (:razon_social, :cuit, :email, :telefono, :direccion, :url_carpeta_drive, :situacion_ib, :jurisdiccion_sede)"  );
        
        try {
            $stmt->execute($data);
            $clienteId = (int)$pdo->lastInsertId();

            // Assign fiscal condition if provided
            if (!empty($_POST['condicion_fiscal_id'])) {
                $cfStmt = $pdo->prepare("INSERT INTO cliente_condicion_fiscal (cliente_id, condicion_fiscal_id, fecha_desde) 
                                         VALUES (:cliente_id, :condicion_fiscal_id, :fecha_desde)");
                $cfStmt->execute([
                    'cliente_id' => $clienteId,
                    'condicion_fiscal_id' => (int)$_POST['condicion_fiscal_id'],
                    'fecha_desde' => $_POST['fecha_desde_cf'] ?: date('Y-m-d'),
                ]);
            }

            Audit::log('crear', 'clientes', $clienteId);
            Session::flash('success', 'Cliente creado exitosamente.');
            redirect(tenant_url('clientes'));
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                Session::flash('error', 'Ya existe un cliente con ese CUIT.');
            } else {
                Session::flash('error', 'Error al crear el cliente.');
            }
            redirect(tenant_url('clientes/crear'));
        }
    }

    public function edit(int $id): void
    {
        $pdo = Database::tenant();
        $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = :id AND activo = 1");
        $stmt->execute(['id' => $id]);
        $cliente = $stmt->fetch();

        if (!$cliente) {
            http_response_code(404);
            view('errors.404');
            return;
        }

        $condiciones = $pdo->query("SELECT * FROM condiciones_fiscales WHERE activo = 1 ORDER BY nombre")->fetchAll();
        $impuestos = $pdo->query("SELECT * FROM impuestos WHERE activo = 1 ORDER BY nombre")->fetchAll();

        $cfStmt = $pdo->prepare("SELECT ccf.*, cf.nombre as condicion_nombre 
                                  FROM cliente_condicion_fiscal ccf 
                                  JOIN condiciones_fiscales cf ON ccf.condicion_fiscal_id = cf.id 
                                  WHERE ccf.cliente_id = :id AND ccf.activo = 1 
                                  ORDER BY ccf.fecha_desde DESC");
        $cfStmt->execute(['id' => $id]);
        $historialCF = $cfStmt->fetchAll();

        $exStmt = $pdo->prepare("SELECT ex.*, imp.nombre as impuesto_nombre
                                  FROM exenciones ex
                                  JOIN impuestos imp ON ex.impuesto_id = imp.id
                                  WHERE ex.cliente_id = :id AND ex.activo = 1
                                  ORDER BY ex.created_at DESC");
        $exStmt->execute(['id' => $id]);
        $exenciones = $exStmt->fetchAll();

        view('clientes.form', [
            'cliente'    => $cliente,
            'condiciones' => $condiciones,
            'historialCF' => $historialCF,
            'impuestos'  => $impuestos,
            'exenciones' => $exenciones,
        ]);
    }

    public function update(int $id): void
    {
        $pdo = Database::tenant();
        $slug = \Core\Tenant::slug();
        $data = $this->validateInput();
        $data['id'] = $id;

        $stmt = $pdo->prepare("UPDATE clientes SET razon_social = :razon_social, cuit = :cuit, email = :email, 
                               telefono = :telefono, direccion = :direccion, url_carpeta_drive = :url_carpeta_drive,
                               situacion_ib = :situacion_ib, jurisdiccion_sede = :jurisdiccion_sede,
                               updated_at = NOW() WHERE id = :id AND activo = 1");
        
        try {
            $stmt->execute($data);
            Audit::log('editar', 'clientes', $id);
            Session::flash('success', 'Cliente actualizado exitosamente.');
            redirect(tenant_url("clientes/{$id}/editar"));
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                Session::flash('error', 'Ya existe un cliente con ese CUIT.');
            } else {
                Session::flash('error', 'Error al actualizar el cliente.');
            }
            redirect(tenant_url("clientes/{$id}/editar"));
        }
    }

    public function delete(int $id): void
    {
        $pdo = Database::tenant();
        $stmt = $pdo->prepare("UPDATE clientes SET activo = 0, updated_at = NOW() WHERE id = :id");
        $stmt->execute(['id' => $id]);
        Audit::log('eliminar', 'clientes', $id);
        Session::flash('success', 'Cliente eliminado exitosamente.');
        redirect(tenant_url('clientes'));
    }

    public function show(int $id): void
    {
        $pdo = Database::tenant();
        $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = :id AND activo = 1");
        $stmt->execute(['id' => $id]);
        $cliente = $stmt->fetch();

        if (!$cliente) {
            http_response_code(404);
            view('errors.404');
            return;
        }

        // Get current fiscal condition
        $cfStmt = $pdo->prepare("SELECT ccf.*, cf.nombre as condicion_nombre 
                                  FROM cliente_condicion_fiscal ccf 
                                  JOIN condiciones_fiscales cf ON ccf.condicion_fiscal_id = cf.id 
                                  WHERE ccf.cliente_id = :id AND ccf.activo = 1 
                                  ORDER BY ccf.fecha_desde DESC");
        $cfStmt->execute(['id' => $id]);
        $historialCF = $cfStmt->fetchAll();

        // Get documents count
        $docStmt = $pdo->prepare("SELECT COUNT(*) FROM documentos WHERE cliente_id = :id AND activo = 1");
        $docStmt->execute(['id' => $id]);
        $totalDocs = (int)$docStmt->fetchColumn();

        // Get keys count
        $keyStmt = $pdo->prepare("SELECT COUNT(*) FROM claves_fiscales WHERE cliente_id = :id AND activo = 1");
        $keyStmt->execute(['id' => $id]);
        $totalClaves = (int)$keyStmt->fetchColumn();

        view('clientes.show', [
            'cliente' => $cliente,
            'historialCF' => $historialCF,
            'totalDocs' => $totalDocs,
            'totalClaves' => $totalClaves,
        ]);
    }

    private function validateInput(): array
    {
        return [
            'razon_social'      => trim($_POST['razon_social'] ?? ''),
            'cuit'              => trim($_POST['cuit'] ?? ''),
            'email'             => trim($_POST['email'] ?? '') ?: null,
            'telefono'          => trim($_POST['telefono'] ?? '') ?: null,
            'direccion'         => trim($_POST['direccion'] ?? '') ?: null,
            'url_carpeta_drive' => trim($_POST['url_carpeta_drive'] ?? '') ?: null,
            'situacion_ib'      => trim($_POST['situacion_ib'] ?? '') ?: null,
            'jurisdiccion_sede' => trim($_POST['jurisdiccion_sede'] ?? '') ?: null,
        ];
    }
}
