<?php ob_start(); ?>

<div class="row mb-4">
    <div class="col">
        <div class="card">
            <div class="card-body py-2">
                <form method="GET" class="d-flex align-items-center gap-3 flex-wrap">
                    <span class="fw-semibold text-muted small">Mostrar vencimientos en los próximos:</span>
                    <?php foreach ([30 => '30 días', 60 => '60 días', 90 => '90 días', 180 => '180 días', 0 => 'Todas'] as $val => $label): ?>
                        <a href="?dias=<?= $val ?>" class="btn btn-sm <?= $dias === $val ? 'btn-primary' : 'btn-outline-secondary' ?>">
                            <?= $label ?>
                        </a>
                    <?php endforeach; ?>
                    <span class="ms-auto text-muted small"><?= count($exenciones) ?> resultado(s)</span>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if (empty($exenciones)): ?>
<div class="alert alert-success">
    <i class="bi bi-check-circle"></i>
    <?= $dias === 0 ? 'No hay exenciones cargadas.' : "No hay exenciones que venzan en los próximos {$dias} días." ?>
</div>
<?php else: ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-shield-exclamation"></i> Exenciones <?= $dias === 0 ? '— Todas' : "próximas a vencer ({$dias} días)" ?></span>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0" id="tablaVencimientos">
            <thead class="table-light">
                <tr>
                    <th>Cliente</th>
                    <th>CUIT</th>
                    <th>Impuesto</th>
                    <th>Desde</th>
                    <th>Vence</th>
                    <th>Días restantes</th>
                    <th>Archivo</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($exenciones as $ex):
                    $dr = (int)($ex['dias_restantes'] ?? 9999);
                    if ($dr < 0) {
                        $badge = 'bg-danger';
                        $label = 'Vencida';
                    } elseif ($dr <= 15) {
                        $badge = 'bg-danger';
                        $label = "Vence en {$dr} días";
                    } elseif ($dr <= 30) {
                        $badge = 'bg-warning text-dark';
                        $label = "Vence en {$dr} días";
                    } else {
                        $badge = 'bg-success';
                        $label = "Vence en {$dr} días";
                    }
                    $clienteId = $ex['cliente_id'] ?? $ex['cliente_id_val'] ?? null;
                ?>
                <tr>
                    <td>
                        <?php if ($clienteId): ?>
                            <a href="<?= tenant_url("clientes/{$clienteId}/ver") ?>" class="text-decoration-none fw-semibold">
                                <?= e($ex['razon_social']) ?>
                            </a>
                        <?php else: ?>
                            <?= e($ex['razon_social']) ?>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted small"><?= e($ex['cuit']) ?></td>
                    <td><?= e($ex['impuesto_nombre']) ?></td>
                    <td><?= $ex['fecha_desde'] ? format_date($ex['fecha_desde']) : '-' ?></td>
                    <td>
                        <?php if ($ex['fecha_hasta']): ?>
                            <span class="fw-semibold"><?= format_date($ex['fecha_hasta']) ?></span>
                        <?php else: ?>
                            <span class="text-muted">Indeterminada</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($ex['fecha_hasta']): ?>
                            <span class="badge <?= $badge ?>"><?= $label ?></span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Sin fecha</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($ex['archivo'] && $clienteId): ?>
                            <a href="<?= tenant_url("clientes/{$clienteId}/exenciones/{$ex['id']}/descargar") ?>"
                               class="btn btn-outline-secondary btn-sm" target="_blank" title="Ver archivo">
                                <i class="bi bi-paperclip"></i>
                            </a>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($clienteId): ?>
                            <a href="<?= tenant_url("clientes/{$clienteId}/editar") ?>#exenciones"
                               class="btn btn-outline-primary btn-sm" title="Editar exención">
                                <i class="bi bi-pencil"></i>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#tablaVencimientos').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        order: [[4, 'asc']],
        pageLength: 25,
    });
});
</script>
<?php endif; ?>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
