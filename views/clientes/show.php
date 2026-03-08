<?php $pageTitle = 'Detalle del Cliente'; ob_start(); ?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-person-vcard"></i> <?= e($cliente['razon_social']) ?></span>
                <a href="<?= tenant_url("clientes/{$cliente['id']}/editar") ?>" class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil"></i> Editar
                </a>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small">CUIT</label>
                        <p class="fw-semibold"><?= e($cliente['cuit']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Email</label>
                        <p><?= $cliente['email'] ? e($cliente['email']) : '<span class="text-muted">-</span>' ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Teléfono</label>
                        <p><?= $cliente['telefono'] ? e($cliente['telefono']) : '<span class="text-muted">-</span>' ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Dirección</label>
                        <p><?= $cliente['direccion'] ? e($cliente['direccion']) : '<span class="text-muted">-</span>' ?></p>
                    </div>
                    <?php if ($cliente['url_carpeta_drive']): ?>
                    <div class="col-12">
                        <label class="text-muted small">Carpeta Drive</label>
                        <p><a href="<?= e($cliente['url_carpeta_drive']) ?>" target="_blank"><i class="bi bi-box-arrow-up-right"></i> Abrir carpeta</a></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (!empty($historialCF)): ?>
        <div class="card">
            <div class="card-header"><i class="bi bi-file-earmark-ruled"></i> Condición Fiscal</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Condición</th><th>Desde</th><th>Hasta</th><th>Estado</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historialCF as $hcf): ?>
                        <tr>
                            <td><?= e($hcf['condicion_nombre']) ?></td>
                            <td><?= format_date($hcf['fecha_desde']) ?></td>
                            <td><?= $hcf['fecha_hasta'] ? format_date($hcf['fecha_hasta']) : '-' ?></td>
                            <td>
                                <?= ($hcf['activo'] && !$hcf['fecha_hasta']) 
                                    ? '<span class="badge bg-success">Vigente</span>' 
                                    : '<span class="badge bg-secondary">Finalizada</span>' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-building display-4 text-primary"></i>
                </div>
                <h5 class="fw-bold"><?= e($cliente['razon_social']) ?></h5>
                <p class="text-muted small mb-3"><?= e($cliente['cuit']) ?></p>
                <div class="row g-2 text-center">
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <div class="fw-bold fs-4"><?= $totalDocs ?></div>
                            <small class="text-muted">Documentos</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <div class="fw-bold fs-4"><?= $totalClaves ?></div>
                            <small class="text-muted">Claves</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-grid gap-2">
            <a href="<?= tenant_url("clientes/{$cliente['id']}/claves") ?>" class="btn btn-outline-danger">
                <i class="bi bi-key"></i> Claves Fiscales
            </a>
            <a href="<?= tenant_url("clientes/{$cliente['id']}/documentos") ?>" class="btn btn-outline-success">
                <i class="bi bi-folder"></i> Documentos
            </a>
            <a href="<?= tenant_url('clientes') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver al listado
            </a>
        </div>

        <div class="card mt-3">
            <div class="card-body small text-muted">
                <p class="mb-1">Creado: <?= format_datetime($cliente['created_at']) ?></p>
                <p class="mb-0">Actualizado: <?= format_datetime($cliente['updated_at']) ?></p>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
