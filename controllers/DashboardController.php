<?php

namespace Controllers;

use Core\Database;
use Core\Session;

class DashboardController
{
    public function index(): void
    {
        $pdo = Database::tenant();

        $totalClientes = $pdo->query("SELECT COUNT(*) FROM clientes WHERE activo = 1")->fetchColumn();
        $totalUsuarios = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE activo = 1")->fetchColumn();
        $totalDocumentos = $pdo->query("SELECT COUNT(*) FROM documentos WHERE activo = 1")->fetchColumn();
        $totalClaves = $pdo->query("SELECT COUNT(*) FROM claves_fiscales WHERE activo = 1")->fetchColumn();

        $recentActivity = $pdo->query("SELECT al.*, u.nombre_completo 
                                        FROM audit_log al 
                                        LEFT JOIN usuarios u ON al.usuario_id = u.id 
                                        ORDER BY al.created_at DESC LIMIT 10")->fetchAll();

        view('dashboard.index', [
            'totalClientes' => $totalClientes,
            'totalUsuarios' => $totalUsuarios,
            'totalDocumentos' => $totalDocumentos,
            'totalClaves' => $totalClaves,
            'recentActivity' => $recentActivity,
        ]);
    }
}
