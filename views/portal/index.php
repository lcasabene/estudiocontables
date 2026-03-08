<?php $pageTitle = 'Mi Portal'; ob_start(); ?>

<div class="row g-4">
    <?php if (empty($clientes)): ?>
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox display-3 text-muted"></i>
                    <p class="text-muted mt-3">No tiene empresas asignadas.</p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($clientes as $cliente): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="bi bi-building text-primary fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0"><?= e($cliente['razon_social']) ?></h6>
                            <small class="text-muted">CUIT: <?= e($cliente['cuit']) ?></small>
                        </div>
                    </div>
                    <?php if ($cliente['email']): ?>
                        <p class="small mb-1"><i class="bi bi-envelope"></i> <?= e($cliente['email']) ?></p>
                    <?php endif; ?>
                    <?php if ($cliente['telefono']): ?>
                        <p class="small mb-1"><i class="bi bi-telephone"></i> <?= e($cliente['telefono']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="<?= tenant_url("portal/cliente/{$cliente['id']}") ?>" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-eye"></i> Ver Detalle
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
