<?php ob_start(); ?>

<!-- Modal responder -->
<div class="modal fade" id="modalResponder" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= tenant_url('whatsapp/enviar') ?>" id="formResponder">
                <?= csrf_field() ?>
                <input type="hidden" name="numero" id="responderNumero">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-whatsapp text-success"></i> Responder a <span id="responderNombre" class="fw-bold"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Número</label>
                        <input type="text" id="responderNumeroDisplay" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipo de envío</label>
                        <div class="d-flex gap-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo" value="texto" id="tipoTexto" checked>
                                <label class="form-check-label" for="tipoTexto">Texto libre</label>
                            </div>
                            <div class="form-check ms-3">
                                <input class="form-check-input" type="radio" name="tipo" value="menu" id="tipoMenu">
                                <label class="form-check-label" for="tipoMenu">Reenviar menú</label>
                            </div>
                        </div>
                    </div>
                    <div id="campoMensaje">
                        <label class="form-label fw-semibold">Mensaje</label>
                        <textarea name="mensaje" class="form-control" rows="3" placeholder="Escribí tu mensaje..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-send"></i> Enviar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tabla -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-chat-dots"></i> Mensajes recibidos</span>
        <div class="d-flex gap-2 align-items-center">
            <span class="badge bg-secondary"><?= count($mensajes) ?></span>
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalResponder"
                    data-numero="" data-nombre="nuevo número">
                <i class="bi bi-plus-lg"></i> Mensaje nuevo
            </button>
        </div>
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
                <tr style="cursor:pointer"
                    data-numero="<?= e($m['from_number']) ?>"
                    data-nombre="<?= e($m['contact_name'] ?? $m['from_number']) ?>"
                    onclick="abrirModal(this)">
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
                    <td class="text-nowrap" onclick="event.stopPropagation()">
                        <button class="btn btn-success btn-sm"
                                data-bs-toggle="modal" data-bs-target="#modalResponder"
                                data-numero="<?= e($m['from_number']) ?>"
                                data-nombre="<?= e($m['contact_name'] ?? $m['from_number']) ?>"
                                onclick="abrirModal(this); event.stopPropagation();"
                                title="Responder">
                            <i class="bi bi-reply"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<script>
function abrirModal(el) {
    var numero = el.dataset.numero;
    var nombre = el.dataset.nombre;
    document.getElementById('responderNumero').value        = numero;
    document.getElementById('responderNumeroDisplay').value = numero;
    document.getElementById('responderNombre').textContent  = nombre;
    var modal = new bootstrap.Modal(document.getElementById('modalResponder'));
    modal.show();
}

// Mostrar/ocultar campo mensaje según tipo
document.querySelectorAll('input[name="tipo"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        document.getElementById('campoMensaje').style.display = this.value === 'menu' ? 'none' : '';
    });
});

$(document).ready(function () {
    $('#tablaMensajes').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        order: [[0, 'desc']],
        pageLength: 25,
        columnDefs: [{ orderable: false, targets: 5 }]
    });
});
</script>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
