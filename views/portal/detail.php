<?php $pageTitle = e($cliente['razon_social']); ob_start(); ?>

<div class="mb-3">
    <a href="<?= tenant_url('portal') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Volver a Mis Empresas
    </a>
</div>

<div class="row g-4">
    <!-- Client Info -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-body text-center">
                <i class="bi bi-building display-4 text-primary"></i>
                <h5 class="fw-bold mt-2"><?= e($cliente['razon_social']) ?></h5>
                <p class="text-muted"><?= e($cliente['cuit']) ?></p>

                <?php if ($condicionActual): ?>
                    <span class="badge bg-primary"><?= e($condicionActual['condicion_nombre']) ?></span>
                <?php endif; ?>
            </div>
            <ul class="list-group list-group-flush">
                <?php if ($cliente['email']): ?>
                    <li class="list-group-item small"><i class="bi bi-envelope"></i> <?= e($cliente['email']) ?></li>
                <?php endif; ?>
                <?php if ($cliente['telefono']): ?>
                    <li class="list-group-item small"><i class="bi bi-telephone"></i> <?= e($cliente['telefono']) ?></li>
                <?php endif; ?>
                <?php if ($cliente['direccion']): ?>
                    <li class="list-group-item small"><i class="bi bi-geo-alt"></i> <?= e($cliente['direccion']) ?></li>
                <?php endif; ?>
                <?php if ($cliente['url_carpeta_drive']): ?>
                    <li class="list-group-item small">
                        <a href="<?= e($cliente['url_carpeta_drive']) ?>" target="_blank">
                            <i class="bi bi-google"></i> Carpeta Drive
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Documents -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-folder"></i> Documentos (<?= count($documentos) ?>)
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Título</th>
                                <th>Tipo</th>
                                <th>Tamaño</th>
                                <th>Fecha</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($documentos)): ?>
                                <tr><td colspan="5" class="text-center text-muted py-3">Sin documentos</td></tr>
                            <?php else: ?>
                                <?php foreach ($documentos as $doc): ?>
                                <tr>
                                    <td><i class="bi bi-file-earmark"></i> <?= e($doc['titulo']) ?></td>
                                    <td><?= $doc['tipo'] ? '<span class="badge bg-info">' . e($doc['tipo']) . '</span>' : '-' ?></td>
                                    <td class="small"><?= $doc['tamano'] ? number_format($doc['tamano'] / 1024, 1) . ' KB' : '-' ?></td>
                                    <td class="small"><?= format_date($doc['created_at']) ?></td>
                                    <td>
                                        <a href="<?= tenant_url("documentos/{$doc['id']}/download") ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Fiscal Keys -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-key"></i> Claves Fiscales (<?= count($claves) ?>)
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Referencia</th>
                                <th>Sitio</th>
                                <th>Credenciales</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($claves)): ?>
                                <tr><td colspan="3" class="text-center text-muted py-3">Sin claves registradas</td></tr>
                            <?php else: ?>
                                <?php foreach ($claves as $clave): ?>
                                <tr>
                                    <td class="fw-semibold"><?= e($clave['referencia']) ?></td>
                                    <td>
                                        <?php if ($clave['url_sitio']): ?>
                                            <a href="<?= e($clave['url_sitio']) ?>" target="_blank" class="small">
                                                <i class="bi bi-box-arrow-up-right"></i> Ir
                                            </a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info btn-decrypt" data-id="<?= $clave['id'] ?>">
                                            <i class="bi bi-eye"></i> Ver
                                        </button>
                                        <div class="decrypt-result d-none mt-2 small" id="decrypt-<?= $clave['id'] ?>">
                                            <div><strong>Usuario:</strong> <code class="user-val"></code></div>
                                            <div><strong>Clave:</strong> <code class="pass-val"></code></div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<script>
$(function() {
    $('.btn-decrypt').on('click', function() {
        let id = $(this).data('id');
        let $result = $('#decrypt-' + id);
        
        if ($result.is(':visible')) {
            $result.addClass('d-none');
            $(this).html('<i class="bi bi-eye"></i> Ver');
            return;
        }

        $.get('<?= tenant_url("claves") ?>/' + id + '/decrypt', function(data) {
            $result.find('.user-val').text(data.usuario);
            $result.find('.pass-val').text(data.password);
            $result.removeClass('d-none');
        }).fail(function(xhr) {
            alert(xhr.responseJSON?.error || 'Error al descifrar');
        });

        $(this).html('<i class="bi bi-eye-slash"></i> Ocultar');
    });
});
</script>
<?php $extraJs = ob_get_clean(); ?>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
