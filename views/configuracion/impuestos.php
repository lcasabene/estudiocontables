<?php ob_start(); ?>

<div class="row g-4">
    <!-- Formulario nuevo impuesto -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-plus-lg"></i> Nuevo Impuesto</div>
            <div class="card-body">
                <form method="POST" action="<?= tenant_url('configuracion/impuestos/store') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre *</label>
                        <input type="text" name="nombre" class="form-control" required placeholder="Ej: Ingresos Brutos">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-lg"></i> Agregar
                    </button>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <a href="<?= tenant_url('configuracion') ?>" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-arrow-left"></i> Volver a Configuración
                </a>
            </div>
        </div>
    </div>

    <!-- Listado -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-receipt"></i> Impuestos</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($impuestos)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">No hay impuestos cargados.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($impuestos as $imp): ?>
                        <tr class="<?= $imp['activo'] ? '' : 'table-secondary opacity-50' ?>">
                            <td class="text-muted small"><?= $imp['id'] ?></td>
                            <td>
                                <form method="POST" action="<?= tenant_url("configuracion/impuestos/{$imp['id']}/update") ?>"
                                      class="d-flex align-items-center gap-2" id="form-imp-<?= $imp['id'] ?>">
                                    <?= csrf_field() ?>
                                    <input type="text" name="nombre" value="<?= e($imp['nombre']) ?>"
                                           class="form-control form-control-sm" required>
                                    <input type="hidden" name="activo" value="0">
                                    <input type="checkbox" name="activo" value="1" class="form-check-input mt-0"
                                           title="Activo" <?= $imp['activo'] ? 'checked' : '' ?>>
                                    <button type="submit" class="btn btn-outline-primary btn-sm text-nowrap">
                                        <i class="bi bi-check"></i>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <?php if ($imp['activo']): ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="POST" action="<?= tenant_url("configuracion/impuestos/{$imp['id']}/delete") ?>"
                                      onsubmit="return confirm('¿Desactivar este impuesto?')">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-danger btn-sm" <?= !$imp['activo'] ? 'disabled' : '' ?>>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
