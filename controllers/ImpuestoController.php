<?php

namespace Controllers;

use Core\Database;
use Core\Session;
use Core\Audit;

class ImpuestoController
{
    public function index(): void
    {
        $pdo = Database::tenant();
        $impuestos = $pdo->query("SELECT * FROM impuestos ORDER BY nombre ASC")->fetchAll();

        view('configuracion.impuestos', [
            'pageTitle' => 'Configuración — Impuestos',
            'impuestos' => $impuestos,
        ]);
    }

    public function store(): void
    {
        $pdo = Database::tenant();
        $nombre = trim($_POST['nombre'] ?? '');

        if ($nombre === '') {
            Session::flash('error', 'El nombre no puede estar vacío.');
            redirect(tenant_url('configuracion/impuestos'));
            return;
        }

        $stmt = $pdo->prepare("INSERT INTO impuestos (nombre) VALUES (:nombre)");
        try {
            $stmt->execute(['nombre' => $nombre]);
            Audit::log('crear', 'impuestos', (int)$pdo->lastInsertId());
            Session::flash('success', "Impuesto \"{$nombre}\" creado.");
        } catch (\PDOException $e) {
            Session::flash('error', 'Ya existe un impuesto con ese nombre.');
        }

        redirect(tenant_url('configuracion/impuestos'));
    }

    public function update(int $id): void
    {
        $pdo = Database::tenant();
        $nombre = trim($_POST['nombre'] ?? '');
        $activo = isset($_POST['activo']) ? 1 : 0;

        if ($nombre === '') {
            Session::flash('error', 'El nombre no puede estar vacío.');
            redirect(tenant_url('configuracion/impuestos'));
            return;
        }

        $stmt = $pdo->prepare("UPDATE impuestos SET nombre = :nombre, activo = :activo WHERE id = :id");
        $stmt->execute(['nombre' => $nombre, 'activo' => $activo, 'id' => $id]);
        Audit::log('editar', 'impuestos', $id);
        Session::flash('success', 'Impuesto actualizado.');
        redirect(tenant_url('configuracion/impuestos'));
    }

    public function delete(int $id): void
    {
        $pdo = Database::tenant();

        // Check if in use
        $uso = $pdo->prepare("SELECT COUNT(*) FROM exenciones WHERE impuesto_id = :id AND activo = 1");
        $uso->execute(['id' => $id]);
        if ((int)$uso->fetchColumn() > 0) {
            Session::flash('error', 'No se puede eliminar: hay exenciones asociadas a este impuesto.');
            redirect(tenant_url('configuracion/impuestos'));
            return;
        }

        $pdo->prepare("UPDATE impuestos SET activo = 0 WHERE id = :id")->execute(['id' => $id]);
        Audit::log('eliminar', 'impuestos', $id);
        Session::flash('success', 'Impuesto desactivado.');
        redirect(tenant_url('configuracion/impuestos'));
    }
}
