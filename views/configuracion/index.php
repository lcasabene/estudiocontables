<?php $pageTitle = 'Configuración'; ob_start(); ?>

<div class="row g-4">
    <div class="col-md-4">
        <a href="<?= tenant_url('configuracion/impuestos') ?>" class="text-decoration-none">
            <div class="card h-100 card-hover">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="rounded-3 p-3 bg-primary bg-opacity-10">
                        <i class="bi bi-receipt fs-3 text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold">Impuestos</h6>
                        <p class="mb-0 small text-muted">Gestión del listado de impuestos utilizados en exenciones.</p>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Espacio reservado para futuras configuraciones -->
    <div class="col-md-4">
        <div class="card h-100 border-dashed opacity-50">
            <div class="card-body d-flex align-items-center gap-3 p-4">
                <div class="rounded-3 p-3 bg-secondary bg-opacity-10">
                    <i class="bi bi-plus-circle fs-3 text-secondary"></i>
                </div>
                <div>
                    <h6 class="mb-1 fw-bold text-muted">Próximamente</h6>
                    <p class="mb-0 small text-muted">Nuevas tablas de configuración.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card-hover { transition: box-shadow .2s, transform .2s; }
.card-hover:hover { box-shadow: 0 4px 16px rgba(0,0,0,.12); transform: translateY(-2px); }
.border-dashed { border: 2px dashed #dee2e6 !important; }
</style>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
