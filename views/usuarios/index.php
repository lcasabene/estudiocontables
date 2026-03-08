<?php $pageTitle = 'Usuarios'; ob_start(); ?>

<div class="card mb-4">
    <div class="card-header"><i class="bi bi-plus-lg"></i> Nuevo Usuario</div>
    <div class="card-body">
        <form method="POST" action="<?= tenant_url('usuarios/store') ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Nombre Completo *</label>
                    <input type="text" name="nombre_completo" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Email *</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Contraseña *</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Rol *</label>
                    <select name="rol" class="form-select" required>
                        <option value="empleado">Empleado</option>
                        <option value="admin">Admin</option>
                        <option value="cliente">Cliente</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check-lg"></i> Crear</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><i class="bi bi-person-gear"></i> Usuarios del Estudio</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Creado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td class="fw-semibold"><?= e($u['nombre_completo']) ?></td>
                        <td><?= e($u['email']) ?></td>
                        <td>
                            <?php 
                            $rolColors = ['admin' => 'danger', 'empleado' => 'primary', 'cliente' => 'success'];
                            $color = $rolColors[$u['rol']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $color ?>"><?= e($u['rol']) ?></span>
                        </td>
                        <td class="small text-muted"><?= format_datetime($u['created_at']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editUser<?= $u['id'] ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <?php if ($u['id'] !== Core\Session::userId()): ?>
                            <form method="POST" action="<?= tenant_url("usuarios/{$u['id']}/delete") ?>" class="d-inline" onsubmit="return confirm('¿Eliminar este usuario?')">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            <?php endif; ?>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editUser<?= $u['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="<?= tenant_url("usuarios/{$u['id']}/update") ?>">
                                            <?= csrf_field() ?>
                                            <div class="modal-header">
                                                <h5 class="modal-title">Editar: <?= e($u['nombre_completo']) ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Nombre Completo</label>
                                                    <input type="text" name="nombre_completo" class="form-control" value="<?= e($u['nombre_completo']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" name="email" class="form-control" value="<?= e($u['email']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Nueva Contraseña</label>
                                                    <input type="password" name="password" class="form-control" placeholder="Dejar vacío para no cambiar">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Rol</label>
                                                    <select name="rol" class="form-select">
                                                        <option value="admin" <?= $u['rol'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                                        <option value="empleado" <?= $u['rol'] === 'empleado' ? 'selected' : '' ?>>Empleado</option>
                                                        <option value="cliente" <?= $u['rol'] === 'cliente' ? 'selected' : '' ?>>Cliente</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Actualizar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
