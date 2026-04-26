<?php

namespace Controllers;

use Core\Database;
use Core\Session;
use Core\Audit;

class UsuarioController
{
    public function index(): void
    {
        $pdo = Database::tenant();
        $usuarios = $pdo->query("SELECT * FROM usuarios WHERE activo = 1 ORDER BY nombre_completo")->fetchAll();
        view('usuarios.index', ['usuarios' => $usuarios]);
    }

    public function store(): void
    {
        $pdo = Database::tenant();

        $nombre   = trim($_POST['nombre_completo'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $rol      = $_POST['rol'] ?? 'empleado';
        $whatsapp = trim($_POST['whatsapp'] ?? '') ?: null;

        if ($nombre === '' || $email === '' || $password === '') {
            Session::flash('error', 'Todos los campos son obligatorios.');
            redirect(tenant_url('usuarios'));
            return;
        }

        if (!in_array($rol, ['admin', 'empleado', 'cliente'])) {
            $rol = 'empleado';
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_completo, email, password_hash, rol, whatsapp)
                                   VALUES (:nombre, :email, :password_hash, :rol, :whatsapp)");
            $stmt->execute([
                'nombre'        => $nombre,
                'email'         => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'rol'           => $rol,
                'whatsapp'      => $whatsapp,
            ]);

            $id = (int)$pdo->lastInsertId();

            // If role is cliente, assign to selected clients
            if ($rol === 'cliente' && !empty($_POST['clientes'])) {
                $cuStmt = $pdo->prepare("INSERT INTO cliente_usuarios (cliente_id, usuario_id, perfil) VALUES (:cid, :uid, 'titular')");
                foreach ($_POST['clientes'] as $clienteId) {
                    $cuStmt->execute(['cid' => (int)$clienteId, 'uid' => $id]);
                }
            }

            Audit::log('crear', 'usuarios', $id);
            Session::flash('success', 'Usuario creado exitosamente.');
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                Session::flash('error', 'Ya existe un usuario con ese email.');
            } else {
                Session::flash('error', 'Error al crear el usuario.');
            }
        }

        redirect(tenant_url('usuarios'));
    }

    public function update(int $id): void
    {
        $pdo = Database::tenant();

        $nombre   = trim($_POST['nombre_completo'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $rol      = $_POST['rol'] ?? 'empleado';
        $password = $_POST['password'] ?? '';
        $whatsapp = trim($_POST['whatsapp'] ?? '') ?: null;

        if ($nombre === '' || $email === '') {
            Session::flash('error', 'Nombre y email son obligatorios.');
            redirect(tenant_url('usuarios'));
            return;
        }

        try {
            if ($password !== '') {
                $stmt = $pdo->prepare("UPDATE usuarios SET nombre_completo = :nombre, email = :email,
                                       password_hash = :password_hash, rol = :rol, whatsapp = :whatsapp, updated_at = NOW()
                                       WHERE id = :id AND activo = 1");
                $stmt->execute([
                    'nombre'        => $nombre,
                    'email'         => $email,
                    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                    'rol'           => $rol,
                    'whatsapp'      => $whatsapp,
                    'id'            => $id,
                ]);
            } else {
                $stmt = $pdo->prepare("UPDATE usuarios SET nombre_completo = :nombre, email = :email,
                                       rol = :rol, whatsapp = :whatsapp, updated_at = NOW()
                                       WHERE id = :id AND activo = 1");
                $stmt->execute([
                    'nombre'   => $nombre,
                    'email'    => $email,
                    'rol'      => $rol,
                    'whatsapp' => $whatsapp,
                    'id'       => $id,
                ]);
            }

            Audit::log('editar', 'usuarios', $id);
            Session::flash('success', 'Usuario actualizado.');
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                Session::flash('error', 'Ya existe un usuario con ese email.');
            } else {
                Session::flash('error', 'Error al actualizar el usuario.');
            }
        }

        redirect(tenant_url('usuarios'));
    }

    public function delete(int $id): void
    {
        if ($id === Session::userId()) {
            Session::flash('error', 'No puede eliminar su propio usuario.');
            redirect(tenant_url('usuarios'));
            return;
        }

        $pdo = Database::tenant();
        $pdo->prepare("UPDATE usuarios SET activo = 0, updated_at = NOW() WHERE id = :id")
            ->execute(['id' => $id]);

        Audit::log('eliminar', 'usuarios', $id);
        Session::flash('success', 'Usuario eliminado.');
        redirect(tenant_url('usuarios'));
    }
}
