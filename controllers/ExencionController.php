<?php

namespace Controllers;

use Core\Database;
use Core\Session;
use Core\Audit;
use Core\Tenant;

class ExencionController
{
    private string $uploadDir;

    public function __construct()
    {
        $this->uploadDir = __DIR__ . '/../storage/uploads/' . (Tenant::slug() ?? 'default') . '/exenciones/';
    }

    public function vencimientos(): void
    {
        $pdo = Database::tenant();

        $dias = (int)($_GET['dias'] ?? 60);
        if (!in_array($dias, [30, 60, 90, 180, 0])) {
            $dias = 60;
        }

        if ($dias === 0) {
            // Todas las exenciones activas
            $stmt = $pdo->query("SELECT ex.*, imp.nombre as impuesto_nombre,
                                        c.razon_social, c.cuit,
                                        DATEDIFF(ex.fecha_hasta, CURDATE()) as dias_restantes
                                 FROM exenciones ex
                                 JOIN impuestos imp ON ex.impuesto_id = imp.id
                                 JOIN clientes c ON ex.cliente_id = c.id
                                 WHERE ex.activo = 1 AND c.activo = 1
                                 ORDER BY ex.fecha_hasta ASC");
        } else {
            $stmt = $pdo->prepare("SELECT ex.*, imp.nombre as impuesto_nombre,
                                          c.razon_social, c.cuit, c.id as cliente_id_val,
                                          DATEDIFF(ex.fecha_hasta, CURDATE()) as dias_restantes
                                   FROM exenciones ex
                                   JOIN impuestos imp ON ex.impuesto_id = imp.id
                                   JOIN clientes c ON ex.cliente_id = c.id
                                   WHERE ex.activo = 1 AND c.activo = 1
                                     AND ex.fecha_hasta IS NOT NULL
                                     AND ex.fecha_hasta BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :dias DAY)
                                   ORDER BY ex.fecha_hasta ASC");
            $stmt->execute(['dias' => $dias]);
        }

        $exenciones = $stmt->fetchAll();

        view('exenciones.index', [
            'exenciones' => $exenciones,
            'dias'       => $dias,
            'pageTitle'  => 'Vencimientos de Exenciones',
        ]);
    }

    public function store(int $clienteId): void
    {
        $pdo = Database::tenant();

        $impuestoId = (int)($_POST['impuesto_id'] ?? 0);
        $fechaDesde = trim($_POST['fecha_desde'] ?? '') ?: null;
        $fechaHasta = trim($_POST['fecha_hasta'] ?? '') ?: null;
        $observaciones = trim($_POST['observaciones'] ?? '') ?: null;

        if (!$impuestoId) {
            Session::flash('error', 'Debe seleccionar un impuesto.');
            redirect(tenant_url("clientes/{$clienteId}/editar"));
            return;
        }

        $archivo = null;

        if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['archivo'];

            if (!is_dir($this->uploadDir . $clienteId)) {
                mkdir($this->uploadDir . $clienteId, 0755, true);
            }

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];

            if (!in_array($ext, $allowed)) {
                Session::flash('error', 'Tipo de archivo no permitido.');
                redirect(tenant_url("clientes/{$clienteId}/editar"));
                return;
            }

            $filename = uniqid('exen_') . '.' . $ext;
            $dest = $this->uploadDir . $clienteId . '/' . $filename;

            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $archivo = 'exenciones/' . $clienteId . '/' . $filename;
            }
        }

        $stmt = $pdo->prepare("INSERT INTO exenciones (cliente_id, impuesto_id, fecha_desde, fecha_hasta, archivo, observaciones)
                               VALUES (:cliente_id, :impuesto_id, :fecha_desde, :fecha_hasta, :archivo, :observaciones)");
        $stmt->execute([
            'cliente_id'    => $clienteId,
            'impuesto_id'   => $impuestoId,
            'fecha_desde'   => $fechaDesde,
            'fecha_hasta'   => $fechaHasta,
            'archivo'       => $archivo,
            'observaciones' => $observaciones,
        ]);

        Audit::log('crear', 'exenciones', (int)$pdo->lastInsertId());
        Session::flash('success', 'Exención cargada correctamente.');
        redirect(tenant_url("clientes/{$clienteId}/editar") . '#exenciones');
    }

    public function update(int $clienteId, int $exencionId): void
    {
        $pdo = Database::tenant();

        $impuestoId = (int)($_POST['impuesto_id'] ?? 0);
        $fechaDesde = trim($_POST['fecha_desde'] ?? '') ?: null;
        $fechaHasta = trim($_POST['fecha_hasta'] ?? '') ?: null;
        $observaciones = trim($_POST['observaciones'] ?? '') ?: null;

        if (!$impuestoId) {
            Session::flash('error', 'Debe seleccionar un impuesto.');
            redirect(tenant_url("clientes/{$clienteId}/editar") . '#exenciones');
            return;
        }

        // Verify the exemption belongs to this client
        $check = $pdo->prepare("SELECT id, archivo FROM exenciones WHERE id = :id AND cliente_id = :cliente_id AND activo = 1");
        $check->execute(['id' => $exencionId, 'cliente_id' => $clienteId]);
        $exencion = $check->fetch();

        if (!$exencion) {
            Session::flash('error', 'Exención no encontrada.');
            redirect(tenant_url("clientes/{$clienteId}/editar") . '#exenciones');
            return;
        }

        $archivo = $exencion['archivo'];

        if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['archivo'];

            if (!is_dir($this->uploadDir . $clienteId)) {
                mkdir($this->uploadDir . $clienteId, 0755, true);
            }

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];

            if (!in_array($ext, $allowed)) {
                Session::flash('error', 'Tipo de archivo no permitido.');
                redirect(tenant_url("clientes/{$clienteId}/editar") . '#exenciones');
                return;
            }

            $filename = uniqid('exen_') . '.' . $ext;
            $dest = $this->uploadDir . $clienteId . '/' . $filename;

            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $archivo = 'exenciones/' . $clienteId . '/' . $filename;
            }
        }

        $stmt = $pdo->prepare("UPDATE exenciones SET impuesto_id = :impuesto_id, fecha_desde = :fecha_desde,
                               fecha_hasta = :fecha_hasta, archivo = :archivo, observaciones = :observaciones,
                               updated_at = NOW()
                               WHERE id = :id AND cliente_id = :cliente_id");
        $stmt->execute([
            'impuesto_id'   => $impuestoId,
            'fecha_desde'   => $fechaDesde,
            'fecha_hasta'   => $fechaHasta,
            'archivo'       => $archivo,
            'observaciones' => $observaciones,
            'id'            => $exencionId,
            'cliente_id'    => $clienteId,
        ]);

        Audit::log('editar', 'exenciones', $exencionId);
        Session::flash('success', 'Exención actualizada correctamente.');
        redirect(tenant_url("clientes/{$clienteId}/editar") . '#exenciones');
    }

    public function delete(int $clienteId, int $exencionId): void
    {
        $pdo = Database::tenant();
        $stmt = $pdo->prepare("UPDATE exenciones SET activo = 0 WHERE id = :id AND cliente_id = :cliente_id");
        $stmt->execute(['id' => $exencionId, 'cliente_id' => $clienteId]);
        Audit::log('eliminar', 'exenciones', $exencionId);
        Session::flash('success', 'Exención eliminada.');
        redirect(tenant_url("clientes/{$clienteId}/editar") . '#exenciones');
    }

    public function download(int $clienteId, int $exencionId): void
    {
        $pdo = Database::tenant();
        $stmt = $pdo->prepare("SELECT archivo FROM exenciones WHERE id = :id AND cliente_id = :cliente_id AND activo = 1");
        $stmt->execute(['id' => $exencionId, 'cliente_id' => $clienteId]);
        $row = $stmt->fetch();

        if (!$row || !$row['archivo']) {
            http_response_code(404);
            echo 'Archivo no encontrado.';
            return;
        }

        $path = __DIR__ . '/../storage/uploads/' . (Tenant::slug() ?? 'default') . '/' . $row['archivo'];

        if (!file_exists($path)) {
            http_response_code(404);
            echo 'Archivo no encontrado.';
            return;
        }

        $mime = mime_content_type($path) ?: 'application/octet-stream';
        header('Content-Type: ' . $mime);
        header('Content-Disposition: inline; filename="' . basename($path) . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
    }
}
