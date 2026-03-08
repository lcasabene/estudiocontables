<?php $pageTitle = 'Dashboard'; ob_start(); ?>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card p-3">
            <div class="stat-value"><?= (int)$totalClientes ?></div>
            <div class="stat-label"><i class="bi bi-people"></i> Clientes Activos</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card p-3" style="border-left-color: #10b981;">
            <div class="stat-value"><?= (int)$totalUsuarios ?></div>
            <div class="stat-label"><i class="bi bi-person-gear"></i> Usuarios</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card p-3" style="border-left-color: #f59e0b;">
            <div class="stat-value"><?= (int)$totalDocumentos ?></div>
            <div class="stat-label"><i class="bi bi-file-earmark"></i> Documentos</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card p-3" style="border-left-color: #ef4444;">
            <div class="stat-value"><?= (int)$totalClaves ?></div>
            <div class="stat-label"><i class="bi bi-key"></i> Claves Fiscales</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history"></i> Actividad Reciente</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Entidad</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentActivity)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Sin actividad reciente</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentActivity as $log): ?>
                        <tr>
                            <td class="small"><?= format_datetime($log['created_at']) ?></td>
                            <td><?= e($log['nombre_completo'] ?? 'Sistema') ?></td>
                            <td><span class="badge bg-info"><?= e($log['accion']) ?></span></td>
                            <td><?= e($log['entidad']) ?> <?= $log['entidad_id'] ? '#' . $log['entidad_id'] : '' ?></td>
                            <td class="small text-muted"><?= e($log['ip'] ?? '') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
