<?php

namespace Controllers;

use Core\Database;
use Core\Session;
use Core\Audit;

class CondicionFiscalController
{
    public function index(): void
    {
        $pdo = Database::tenant();
        $condiciones = $pdo->query("SELECT * FROM condiciones_fiscales WHERE activo = 1 ORDER BY nombre")->fetchAll();
        view('condiciones.index', ['condiciones' => $condiciones]);
    }

    public function store(): void
    {
        $pdo = Database::tenant();
        $nombre = trim($_POST['nombre'] ?? '');

        if ($nombre === '') {
            Session::flash('error', 'El nombre es obligatorio.');
            redirect(tenant_url('condiciones-fiscales'));
            return;
        }

        $stmt = $pdo->prepare("INSERT INTO condiciones_fiscales (nombre) VALUES (:nombre)");
        $stmt->execute(['nombre' => $nombre]);
        $id = (int)$pdo->lastInsertId();

        Audit::log('crear', 'condiciones_fiscales', $id);
        Session::flash('success', 'Condición fiscal creada.');
        redirect(tenant_url('condiciones-fiscales'));
    }

    public function update(int $id): void
    {
        $pdo = Database::tenant();
        $nombre = trim($_POST['nombre'] ?? '');

        if ($nombre === '') {
            Session::flash('error', 'El nombre es obligatorio.');
            redirect(tenant_url('condiciones-fiscales'));
            return;
        }

        $stmt = $pdo->prepare("UPDATE condiciones_fiscales SET nombre = :nombre, updated_at = NOW() WHERE id = :id AND activo = 1");
        $stmt->execute(['nombre' => $nombre, 'id' => $id]);

        Audit::log('editar', 'condiciones_fiscales', $id);
        Session::flash('success', 'Condición fiscal actualizada.');
        redirect(tenant_url('condiciones-fiscales'));
    }

    public function delete(int $id): void
    {
        $pdo = Database::tenant();
        $stmt = $pdo->prepare("UPDATE condiciones_fiscales SET activo = 0, updated_at = NOW() WHERE id = :id");
        $stmt->execute(['id' => $id]);

        Audit::log('eliminar', 'condiciones_fiscales', $id);
        Session::flash('success', 'Condición fiscal eliminada.');
        redirect(tenant_url('condiciones-fiscales'));
    }

    public function assignToClient(int $clienteId): void
    {
        $pdo = Database::tenant();
        $condicionId = (int)($_POST['condicion_fiscal_id'] ?? 0);
        $fechaDesde = $_POST['fecha_desde'] ?? date('Y-m-d');
        $observaciones = trim($_POST['observaciones'] ?? '') ?: null;

        if ($condicionId === 0) {
            Session::flash('error', 'Seleccione una condición fiscal.');
            redirect(tenant_url("clientes/{$clienteId}/editar"));
            return;
        }

        // Close previous active condition
        $pdo->prepare("UPDATE cliente_condicion_fiscal SET fecha_hasta = :fecha, activo = 0 
                       WHERE cliente_id = :cid AND activo = 1 AND fecha_hasta IS NULL")
             ->execute(['fecha' => $fechaDesde, 'cid' => $clienteId]);

        // Insert new
        $stmt = $pdo->prepare("INSERT INTO cliente_condicion_fiscal (cliente_id, condicion_fiscal_id, fecha_desde, observaciones) 
                               VALUES (:cliente_id, :condicion_fiscal_id, :fecha_desde, :observaciones)");
        $stmt->execute([
            'cliente_id' => $clienteId,
            'condicion_fiscal_id' => $condicionId,
            'fecha_desde' => $fechaDesde,
            'observaciones' => $observaciones,
        ]);

        Audit::log('asignar_condicion_fiscal', 'clientes', $clienteId);
        Session::flash('success', 'Condición fiscal asignada.');
        redirect(tenant_url("clientes/{$clienteId}/editar"));
    }
}
