<?php

namespace Controllers;

use Core\Database;
use Core\Session;
use Core\Audit;
use Core\Tenant;

class DocumentoController
{
    private string $uploadDir;

    public function __construct()
    {
        $this->uploadDir = __DIR__ . '/../storage/uploads/' . (Tenant::slug() ?? 'default') . '/';
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

        $stmt = $pdo->prepare("SELECT * FROM documentos WHERE cliente_id = :id AND activo = 1 ORDER BY created_at DESC");
        $stmt->execute(['id' => $clienteId]);
        $documentos = $stmt->fetchAll();

        view('documentos.index', ['cliente' => $cliente, 'documentos' => $documentos]);
    }

    public function upload(int $clienteId): void
    {
        $pdo = Database::tenant();

        if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Error al subir el archivo.');
            redirect(tenant_url("clientes/{$clienteId}/documentos"));
            return;
        }

        $file = $_FILES['archivo'];
        $titulo = trim($_POST['titulo'] ?? '') ?: $file['name'];
        $tipo = trim($_POST['tipo'] ?? '') ?: null;

        // Create upload directory
        $clientDir = $this->uploadDir . $clienteId . '/';
        if (!is_dir($clientDir)) {
            mkdir($clientDir, 0755, true);
        }

        // Generate unique filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('doc_') . '.' . $ext;
        $filePath = $clientDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            Session::flash('error', 'Error al guardar el archivo.');
            redirect(tenant_url("clientes/{$clienteId}/documentos"));
            return;
        }

        $hash = hash_file('sha256', $filePath);

        $stmt = $pdo->prepare("INSERT INTO documentos 
            (cliente_id, titulo, tipo, storage, ruta_archivo, mime_type, tamano, hash_sha256) 
            VALUES (:cliente_id, :titulo, :tipo, 'local', :ruta_archivo, :mime_type, :tamano, :hash_sha256)");

        $stmt->execute([
            'cliente_id' => $clienteId,
            'titulo' => $titulo,
            'tipo' => $tipo,
            'ruta_archivo' => $filePath,
            'mime_type' => $file['type'],
            'tamano' => $file['size'],
            'hash_sha256' => $hash,
        ]);

        $id = (int)$pdo->lastInsertId();
        Audit::log('subir_documento', 'documentos', $id);
        Session::flash('success', 'Documento subido exitosamente.');
        redirect(tenant_url("clientes/{$clienteId}/documentos"));
    }

    public function download(int $id): void
    {
        $pdo = Database::tenant();

        $stmt = $pdo->prepare("SELECT * FROM documentos WHERE id = :id AND activo = 1");
        $stmt->execute(['id' => $id]);
        $doc = $stmt->fetch();

        if (!$doc || !file_exists($doc['ruta_archivo'])) {
            http_response_code(404);
            view('errors.404');
            return;
        }

        // Check client access for cliente role
        if (is_cliente()) {
            $access = $pdo->prepare("SELECT 1 FROM cliente_usuarios WHERE cliente_id = :cid AND usuario_id = :uid AND activo = 1");
            $access->execute(['cid' => $doc['cliente_id'], 'uid' => Session::userId()]);
            if (!$access->fetch()) {
                http_response_code(403);
                die('Access denied.');
            }
        }

        Audit::log('descargar_documento', 'documentos', $id);

        header('Content-Type: ' . ($doc['mime_type'] ?? 'application/octet-stream'));
        header('Content-Disposition: attachment; filename="' . basename($doc['titulo']) . '"');
        header('Content-Length: ' . filesize($doc['ruta_archivo']));
        readfile($doc['ruta_archivo']);
        exit;
    }

    public function delete(int $id): void
    {
        $pdo = Database::tenant();

        $stmt = $pdo->prepare("SELECT * FROM documentos WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $doc = $stmt->fetch();

        if (!$doc) {
            http_response_code(404);
            view('errors.404');
            return;
        }

        $pdo->prepare("UPDATE documentos SET activo = 0, updated_at = NOW() WHERE id = :id")
            ->execute(['id' => $id]);

        Audit::log('eliminar_documento', 'documentos', $id);
        Session::flash('success', 'Documento eliminado.');
        redirect(tenant_url("clientes/{$doc['cliente_id']}/documentos"));
    }
}
