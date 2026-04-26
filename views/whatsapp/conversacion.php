<?php
ob_start(); // extraJs
?>
<script>
$(document).ready(function () {
    // Auto-scroll al final del hilo
    var hilo = document.getElementById('hiloMensajes');
    if (hilo) hilo.scrollTop = hilo.scrollHeight;

    // Seleccionar miembro del equipo → auto-completar número
    $('#selectEquipo').on('change', function () {
        var num = $(this).find(':selected').data('numero') || '';
        var nom = $(this).find(':selected').text();
        $('#inputDestNumero').val(num);
        $('#inputDestNombre').val(nom !== '-- número manual --' ? nom : '');
    });
});

// Modal reenvío
function abrirReenvio(id, body) {
    document.getElementById('reenvioMensajeId').value = id;
    document.getElementById('reenvioPreview').textContent = body;
    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalReenvio')).show();
}
</script>
<?php $extraJs = ob_get_clean(); ?>

<?php ob_start(); ?>

<!-- Modal reenviar al equipo -->
<div class="modal fade" id="modalReenvio" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="formReenvio">
                <?= csrf_field() ?>
                <input type="hidden" name="destinatario_nombre" id="inputDestNombre">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-share"></i> Reenviar al equipo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-secondary small py-2 mb-3">
                        <strong>Mensaje original:</strong>
                        <div id="reenvioPreview" class="mt-1 text-muted"></div>
                    </div>
                    <input type="hidden" id="reenvioMensajeId">

                    <?php if (!empty($equipo)): ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Miembro del equipo</label>
                        <select id="selectEquipo" class="form-select">
                            <option value="" data-numero="">-- número manual --</option>
                            <?php foreach ($equipo as $u): ?>
                                <option value="<?= $u['id'] ?>" data-numero="<?= e($u['whatsapp']) ?>">
                                    <?= e($u['nombre_completo']) ?> (<?= e($u['whatsapp']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Número destino *</label>
                        <input type="text" name="destinatario_numero" id="inputDestNumero" class="form-control"
                               placeholder="Ej: 5492991234567" required>
                        <div class="form-text">Con código de país, sin +</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nota adicional</label>
                        <textarea name="nota" class="form-control" rows="2"
                                  placeholder="Información extra para el destinatario..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Enviado por</label>
                        <input type="text" name="enviado_por" class="form-control"
                               value="<?= e($currentUser['name'] ?? '') ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-share"></i> Reenviar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Header contacto -->
    <div class="col-12">
        <div class="card">
            <div class="card-body py-2 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <a href="<?= tenant_url('whatsapp/mensajes') ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <div class="fw-bold"><?= e($contactName) ?></div>
                        <div class="text-muted small"><?= e($numero) ?></div>
                    </div>
                </div>
                <button class="btn btn-success btn-sm"
                        data-bs-toggle="modal" data-bs-target="#modalResponder"
                        data-numero="<?= e($numero) ?>" data-nombre="<?= e($contactName) ?>">
                    <i class="bi bi-reply"></i> Responder
                </button>
            </div>
        </div>
    </div>

    <!-- Hilo de mensajes -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-chat-dots"></i> Hilo de conversación</div>
            <div class="card-body p-3" id="hiloMensajes" style="max-height:520px;overflow-y:auto;">
                <?php if (empty($mensajes)): ?>
                    <p class="text-muted text-center">Sin mensajes.</p>
                <?php endif; ?>
                <?php foreach ($mensajes as $m): ?>
                <div class="mb-3">
                    <div class="d-flex align-items-start gap-2">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:32px;height:32px;font-size:.75rem;">
                            <?= mb_strtoupper(mb_substr($contactName, 0, 1)) ?>
                        </div>
                        <div class="flex-grow-1">
                            <div class="bg-light rounded p-2 small">
                                <?php if ($m['opcion_id']): ?>
                                    <span class="badge bg-primary me-1"><?= e($m['opcion_id']) ?></span>
                                <?php endif; ?>
                                <?= nl2br(e($m['body'] ?? '')) ?>
                            </div>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="text-muted" style="font-size:.7rem;">
                                    <?= date('d/m/y H:i', strtotime($m['created_at'])) ?>
                                </span>
                                <button class="btn btn-outline-warning btn-sm py-0 px-1"
                                        style="font-size:.7rem;"
                                        onclick="abrirReenvio(<?= $m['id'] ?>, <?= json_encode($m['body'] ?? $m['opcion_id'] ?? '') ?>)">
                                    <i class="bi bi-share"></i> Reenviar
                                </button>
                                <form method="POST" action="<?= tenant_url("whatsapp/mensajes/{$m['id']}/reenviar-menu") ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-secondary btn-sm py-0 px-1" style="font-size:.7rem;"
                                            title="Reenviar menú del bot">
                                        <i class="bi bi-robot"></i> Menú
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Panel lateral: reenvíos + responder -->
    <div class="col-lg-4">
        <!-- Respuesta rápida -->
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-reply"></i> Respuesta rápida</div>
            <div class="card-body">
                <form method="POST" action="<?= tenant_url('whatsapp/enviar') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="numero" value="<?= e($numero) ?>">
                    <input type="hidden" name="tipo" value="texto">
                    <textarea name="mensaje" class="form-control form-control-sm mb-2" rows="3"
                              placeholder="Escribí tu respuesta..." required></textarea>
                    <div class="d-grid gap-1">
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-send"></i> Enviar texto
                        </button>
                    </div>
                </form>
                <form method="POST" action="<?= tenant_url('whatsapp/enviar') ?>" class="mt-2">
                    <?= csrf_field() ?>
                    <input type="hidden" name="numero" value="<?= e($numero) ?>">
                    <input type="hidden" name="tipo" value="menu">
                    <button type="submit" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-list-ul"></i> Reenviar menú del bot
                    </button>
                </form>
            </div>
        </div>

        <!-- Historial de reenvíos -->
        <?php if (!empty($reenvios)): ?>
        <div class="card">
            <div class="card-header"><i class="bi bi-share"></i> Reenvíos al equipo</div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php foreach ($reenvios as $r): ?>
                    <li class="list-group-item small">
                        <div class="fw-semibold"><?= e($r['destinatario_nombre'] ?? $r['destinatario_numero']) ?></div>
                        <div class="text-muted"><?= e($r['destinatario_numero']) ?></div>
                        <?php if ($r['enviado_por']): ?>
                            <div class="text-muted">por <?= e($r['enviado_por']) ?></div>
                        <?php endif; ?>
                        <div class="text-muted" style="font-size:.7rem;"><?= date('d/m/y H:i', strtotime($r['created_at'])) ?></div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal responder (mismo que mensajes.php) -->
<div class="modal fade" id="modalResponder" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= tenant_url('whatsapp/enviar') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="numero" value="<?= e($numero) ?>">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-whatsapp text-success"></i> Responder a <?= e($contactName) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipo</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo" value="texto" id="cTipoTexto" checked>
                                <label class="form-check-label" for="cTipoTexto">Texto libre</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo" value="menu" id="cTipoMenu">
                                <label class="form-check-label" for="cTipoMenu">Menú bot</label>
                            </div>
                        </div>
                    </div>
                    <div id="cCampoMensaje">
                        <textarea name="mensaje" class="form-control" rows="3" placeholder="Mensaje..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-send"></i> Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('input[name="tipo"]').forEach(function (r) {
    r.addEventListener('change', function () {
        document.getElementById('cCampoMensaje').style.display = this.value === 'menu' ? 'none' : '';
    });
});
// Reenvío: actualizar action del form con el id del mensaje
document.getElementById('modalReenvio').addEventListener('show.bs.modal', function () {
    var id = document.getElementById('reenvioMensajeId').value;
    document.getElementById('formReenvio').action = '<?= tenant_url('whatsapp/mensajes/') ?>' + id + '/reenviar-equipo';
});
</script>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
