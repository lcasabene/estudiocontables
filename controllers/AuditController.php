<?php

namespace Controllers;

use Core\Database;

class AuditController
{
    public function index(): void
    {
        view('auditoria.index');
    }

    public function datatable(): void
    {
        $pdo = Database::tenant();

        $draw = (int)($_GET['draw'] ?? 1);
        $start = (int)($_GET['start'] ?? 0);
        $length = (int)($_GET['length'] ?? 25);
        $search = $_GET['search']['value'] ?? '';

        $totalRecords = (int)$pdo->query("SELECT COUNT(*) FROM audit_log")->fetchColumn();

        $where = "WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $where .= " AND (al.accion LIKE :s1 OR al.entidad LIKE :s2 OR u.nombre_completo LIKE :s3 OR al.ip LIKE :s4)";
            $params['s1'] = "%{$search}%";
            $params['s2'] = "%{$search}%";
            $params['s3'] = "%{$search}%";
            $params['s4'] = "%{$search}%";
        }

        $filteredStmt = $pdo->prepare("SELECT COUNT(*) FROM audit_log al LEFT JOIN usuarios u ON al.usuario_id = u.id {$where}");
        $filteredStmt->execute($params);
        $filteredRecords = (int)$filteredStmt->fetchColumn();

        $sql = "SELECT al.*, u.nombre_completo 
                FROM audit_log al 
                LEFT JOIN usuarios u ON al.usuario_id = u.id 
                {$where} 
                ORDER BY al.created_at DESC 
                LIMIT :start, :length";

        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':start', $start, \PDO::PARAM_INT);
        $stmt->bindValue(':length', $length, \PDO::PARAM_INT);
        $stmt->execute();

        json_response([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $stmt->fetchAll(),
        ]);
    }
}
