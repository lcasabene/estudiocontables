<?php
$isEdit = $cliente !== null;
$pageTitle = $isEdit ? 'Editar Cliente' : 'Nuevo Cliente';
ob_start();
?>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-<?= $isEdit ? 'pencil' : 'plus-lg' ?>"></i> <?= $pageTitle ?>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= $isEdit ? tenant_url("clientes/{$cliente['id']}/update") : tenant_url('clientes/store') ?>">
                    <?= csrf_field() ?>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Razón Social *</label>
                            <input type="text" name="razon_social" class="form-control" required
                                   value="<?= e($cliente['razon_social'] ?? old('razon_social')) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">CUIT *</label>
                            <input type="text" name="cuit" class="form-control" required placeholder="XX-XXXXXXXX-X"
                                   value="<?= e($cliente['cuit'] ?? old('cuit')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="<?= e($cliente['email'] ?? old('email')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono</label>
                            <input type="text" name="telefono" class="form-control"
                                   value="<?= e($cliente['telefono'] ?? old('telefono')) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Dirección</label>
                            <input type="text" name="direccion" class="form-control"
                                   value="<?= e($cliente['direccion'] ?? old('direccion')) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">URL Carpeta Drive</label>
                            <input type="url" name="url_carpeta_drive" class="form-control" placeholder="https://drive.google.com/..."
                                   value="<?= e($cliente['url_carpeta_drive'] ?? old('url_carpeta_drive')) ?>">
                        </div>

                        <?php if (!$isEdit && !empty($condiciones)): ?>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Condición Fiscal</label>
                            <select name="condicion_fiscal_id" class="form-select">
                                <option value="">-- Seleccionar --</option>
                                <?php foreach ($condiciones as $cf): ?>
                                    <option value="<?= $cf['id'] ?>"><?= e($cf['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fecha Desde CF</label>
                            <input type="date" name="fecha_desde_cf" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> <?= $isEdit ? 'Actualizar' : 'Guardar' ?>
                        </button>
                        <a href="<?= tenant_url('clientes') ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($isEdit && !empty($condiciones)): ?>
        <!-- Assign Fiscal Condition -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-file-earmark-ruled"></i> Asignar Condición Fiscal
            </div>
            <div class="card-body">
                <form method="POST" action="<?= tenant_url("clientes/{$cliente['id']}/condicion-fiscal") ?>">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <select name="condicion_fiscal_id" class="form-select" required>
                                <option value="">-- Seleccionar --</option>
                                <?php foreach ($condiciones as $cf): ?>
                                    <option value="<?= $cf['id'] ?>"><?= e($cf['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="fecha_desde" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="observaciones" class="form-control" placeholder="Observaciones">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Asignar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Fiscal Condition History -->
        <?php if (!empty($historialCF)): ?>
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clock-history"></i> Historial Condición Fiscal
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Condición</th>
                            <th>Desde</th>
                            <th>Hasta</th>
                            <th>Observaciones</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historialCF as $hcf): ?>
                        <tr>
                            <td><?= e($hcf['condicion_nombre']) ?></td>
                            <td><?= format_date($hcf['fecha_desde']) ?></td>
                            <td><?= $hcf['fecha_hasta'] ? format_date($hcf['fecha_hasta']) : '-' ?></td>
                            <td class="small"><?= e($hcf['observaciones'] ?? '') ?></td>
                            <td>
                                <?php if ($hcf['activo'] && !$hcf['fecha_hasta']): ?>
                                    <span class="badge bg-success">Vigente</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Finalizada</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-info-circle"></i> Información</div>
            <div class="card-body">
                <?php if ($isEdit): ?>
                    <p class="small text-muted mb-1">Creado: <?= format_datetime($cliente['created_at']) ?></p>
                    <p class="small text-muted mb-3">Actualizado: <?= format_datetime($cliente['updated_at']) ?></p>
                    <div class="d-grid gap-2">
                        <a href="<?= tenant_url("clientes/{$cliente['id']}/claves") ?>" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-key"></i> Claves Fiscales
                        </a>
                        <a href="<?= tenant_url("clientes/{$cliente['id']}/documentos") ?>" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-folder"></i> Documentos
                        </a>
                        <?php if ($cliente['url_carpeta_drive']): ?>
                        <a href="<?= e($cliente['url_carpeta_drive']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-google"></i> Carpeta Drive
                        </a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <p class="small text-muted">Complete los datos del nuevo cliente. Los campos marcados con * son obligatorios.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
