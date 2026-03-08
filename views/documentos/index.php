<?php $pageTitle = 'Documentos - ' . e($cliente['razon_social']); ob_start(); ?>

<div class="mb-3">
    <a href="<?= tenant_url("clientes/{$cliente['id']}/ver") ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Volver al cliente
    </a>
</div>

<?php if (is_admin() || is_empleado()): ?>
<div class="card mb-4">
    <div class="card-header"><i class="bi bi-cloud-upload"></i> Subir Documento</div>
    <div class="card-body">
        <form method="POST" action="<?= tenant_url("clientes/{$cliente['id']}/documentos/upload") ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Título</label>
                    <input type="text" name="titulo" class="form-control" placeholder="Opcional, se usa el nombre del archivo">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tipo</label>
                    <select name="tipo" class="form-select">
                        <option value="">-- Sin tipo --</option>
                        <option value="DDJJ">DDJJ</option>
                        <option value="Balance">Balance</option>
                        <option value="Contrato">Contrato</option>
                        <option value="Factura">Factura</option>
                        <option value="Impuesto">Impuesto</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Archivo *</label>
                    <input type="file" name="archivo" class="form-control" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-upload"></i> Subir</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-folder"></i> Documentos (<?= count($documentos) ?>)</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>MIME</th>
                        <th>Tamaño</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($documentos)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">No hay documentos</td></tr>
                    <?php else: ?>
                        <?php foreach ($documentos as $doc): ?>
                        <tr>
                            <td>
                                <i class="bi bi-file-earmark text-primary"></i>
                                <?= e($doc['titulo']) ?>
                            </td>
                            <td><?= $doc['tipo'] ? '<span class="badge bg-info">' . e($doc['tipo']) . '</span>' : '-' ?></td>
                            <td class="small text-muted"><?= e($doc['mime_type'] ?? '') ?></td>
                            <td class="small"><?= $doc['tamano'] ? number_format($doc['tamano'] / 1024, 1) . ' KB' : '-' ?></td>
                            <td class="small"><?= format_datetime($doc['created_at']) ?></td>
                            <td>
                                <a href="<?= tenant_url("documentos/{$doc['id']}/download") ?>" class="btn btn-sm btn-outline-primary" title="Descargar">
                                    <i class="bi bi-download"></i>
                                </a>
                                <?php if (is_admin()): ?>
                                <form method="POST" action="<?= tenant_url("documentos/{$doc['id']}/delete") ?>" class="d-inline" onsubmit="return confirm('¿Eliminar este documento?')">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
