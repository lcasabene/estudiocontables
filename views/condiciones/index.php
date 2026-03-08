<?php $pageTitle = 'Condiciones Fiscales'; ob_start(); ?>

<?php if (is_admin()): ?>
<div class="card mb-4">
    <div class="card-header"><i class="bi bi-plus-lg"></i> Nueva Condición Fiscal</div>
    <div class="card-body">
        <form method="POST" action="<?= tenant_url('condiciones-fiscales/store') ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-9">
                    <input type="text" name="nombre" class="form-control" placeholder="Nombre de la condición fiscal" required>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check-lg"></i> Crear</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header"><i class="bi bi-file-earmark-ruled"></i> Condiciones Fiscales</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Creada</th>
                        <?php if (is_admin()): ?><th>Acciones</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($condiciones)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">No hay condiciones fiscales</td></tr>
                    <?php else: ?>
                        <?php foreach ($condiciones as $cf): ?>
                        <tr>
                            <td><?= $cf['id'] ?></td>
                            <td class="fw-semibold"><?= e($cf['nombre']) ?></td>
                            <td class="small text-muted"><?= format_datetime($cf['created_at']) ?></td>
                            <?php if (is_admin()): ?>
                            <td>
                                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editCF<?= $cf['id'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" action="<?= tenant_url("condiciones-fiscales/{$cf['id']}/delete") ?>" class="d-inline" onsubmit="return confirm('¿Eliminar esta condición?')">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editCF<?= $cf['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <form method="POST" action="<?= tenant_url("condiciones-fiscales/{$cf['id']}/update") ?>">
                                                <?= csrf_field() ?>
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Editar Condición</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="text" name="nombre" class="form-control" value="<?= e($cf['nombre']) ?>" required>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
