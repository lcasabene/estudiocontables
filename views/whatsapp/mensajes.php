<?php ob_start(); ?>

<div class="row g-4">
    <!-- Panel envío manual -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-send"></i> Enviar mensaje</div>
            <div class="card-body">
                <form method="POST" action="<?= tenant_url('whatsapp/enviar') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Número <span class="text-muted small">(con código país)</span></label>
                        <input type="text" name="numero" class="form-control" placeholder="Ej: 5492991234567" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mensaje</label>
                        <textarea name="mensaje" class="form-control" rows="3" required placeholder="Escribí el mensaje..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-whatsapp"></i> Enviar texto
                    </button>
                </form>
                <hr>
                <p class="small text-muted mb-0">También podés reenviar el menú desde cada fila de la tabla.</p>
            </div>
        </div>
    </div>

    <!-- Tabla de mensajes -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-chat-dots"></i> Mensajes recibidos</span>
                <span class="badge bg-secondary"><?= count($mensajes) ?></span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($mensajes)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1"></i>
                        <p class="mt-2">No hay mensajes registrados aún.</p>
                    </div>
                <?php else: ?>
                <table class="table table-hover mb-0 table-sm" id="tablaMensajes">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Número</th>
                            <th>Contacto</th>
                            <th>Tipo</th>
                            <th>Mensaje / Opción</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mensajes as $m): ?>
                        <tr>
                            <td class="text-muted small text-nowrap">
                                <?= date('d/m/y H:i', strtotime($m['created_at'])) ?>
                            </td>
                            <td class="small fw-semibold"><?= e($m['from_number']) ?></td>
                            <td class="small"><?= e($m['contact_name'] ?? '-') ?></td>
                            <td>
                                <?php if ($m['tipo'] === 'interactive'): ?>
                                    <span class="badge bg-primary">Menú</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Texto</span>
                                <?php endif; ?>
                            </td>
                            <td class="small">
                                <?php if ($m['opcion_id']): ?>
                                    <code><?= e($m['opcion_id']) ?></code>
                                    <span class="text-muted"> — <?= e($m['body'] ?? '') ?></span>
                                <?php else: ?>
                                    <?= e(mb_strimwidth($m['body'] ?? '', 0, 80, '…')) ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-nowrap">
                                <form method="POST" action="<?= tenant_url("whatsapp/mensajes/{$m['id']}/reenviar-menu") ?>">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-success btn-sm" title="Reenviar menú">
                                        <i class="bi bi-arrow-repeat"></i> Menú
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#tablaMensajes').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        order: [[0, 'desc']],
        pageLength: 25,
    });
});
</script>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
