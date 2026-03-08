<?php
$pageTitle = 'Claves Fiscales - ' . e($cliente['razon_social']);
ob_start();
?>

<style>
    .btn-copy {
        border: none; background: none; color: #64748b; cursor: pointer;
        padding: 2px 6px; border-radius: 4px; transition: all .15s;
    }
    .btn-copy:hover { background: #e2e8f0; color: #1e293b; }
    .btn-copy.copied { color: #10b981; }
    .auto-hide-bar {
        height: 3px; background: #ef4444; border-radius: 2px; margin-top: .35rem;
    }
    .password-wrapper { position: relative; }
    .password-wrapper .toggle-pw {
        position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
        border: none; background: none; color: #64748b; cursor: pointer;
    }
    .filter-pills .btn { border-radius: 2rem; font-size: .8rem; }
    .filter-pills .btn.active { box-shadow: 0 2px 8px rgba(37,99,235,.25); }
    .clave-table { margin-bottom: 0; }
    .clave-table th { font-size: .75rem; text-transform: uppercase; color: #64748b; font-weight: 600; letter-spacing: .03em; }
    .clave-table td { vertical-align: middle; }
    .cat-badge {
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .2rem .6rem; border-radius: 2rem; font-size: .75rem; font-weight: 600;
    }
    .cred-inline {
        background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: .4rem;
        padding: .4rem .75rem; font-family: 'Consolas','Monaco',monospace; font-size: .82rem;
        display: inline-flex; gap: 1.25rem; align-items: center; flex-wrap: wrap;
    }
    .cred-inline .cred-label { color: #64748b; font-size: .7rem; font-weight: 600; }
    .cred-inline .cred-val { color: #166534; font-weight: 600; user-select: all; }
    .ref-name { font-weight: 600; color: #1e293b; }
    .ref-obs { font-size: .78rem; color: #94a3b8; display: block; }
</style>

<!-- Header -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div class="d-flex align-items-center gap-2">
        <a href="<?= tenant_url("clientes/{$cliente['id']}/ver") ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h5 class="mb-0 fw-bold"><?= e($cliente['razon_social']) ?></h5>
            <small class="text-muted">CUIT: <?= e($cliente['cuit']) ?> &mdash; <?= count($claves) ?> clave(s) registrada(s)</small>
        </div>
    </div>
    <div class="d-flex gap-2">
        <?php if (is_admin() || is_empleado()): ?>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-lg"></i> Nueva Clave
        </button>
        <?php endif; ?>
    </div>
</div>

<!-- Filters & Search -->
<?php if (!empty($claves)): ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div class="filter-pills d-flex flex-wrap gap-1">
        <button class="btn btn-sm btn-outline-primary active" data-filter="all">
            <i class="bi bi-grid"></i> Todas (<?= count($claves) ?>)
        </button>
        <?php
        $countByCategory = [];
        foreach ($claves as $c) {
            $cat = $c['categoria'] ?? 'otros';
            $countByCategory[$cat] = ($countByCategory[$cat] ?? 0) + 1;
        }
        foreach ($countByCategory as $catKey => $catCount):
            $catInfo = $categorias[$catKey] ?? $categorias['otros'];
        ?>
        <button class="btn btn-sm btn-outline-<?= $catInfo['color'] ?>" data-filter="<?= $catKey ?>">
            <i class="bi bi-<?= $catInfo['icon'] ?>"></i> <?= $catInfo['label'] ?> (<?= $catCount ?>)
        </button>
        <?php endforeach; ?>
    </div>
    <div style="max-width:300px">
        <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" id="searchClaves" class="form-control" placeholder="Buscar clave...">
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Table -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($claves)): ?>
        <div class="text-center py-5">
            <i class="bi bi-key display-3 text-muted"></i>
            <p class="text-muted mt-3 mb-0">No hay claves fiscales registradas para este cliente.</p>
            <?php if (is_admin() || is_empleado()): ?>
            <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bi bi-plus-lg"></i> Crear primera clave
            </button>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover clave-table">
                <thead class="table-light">
                    <tr>
                        <th style="width:130px">Categoría</th>
                        <th>Referencia</th>
                        <th style="width:120px">Sitio</th>
                        <th>Credenciales</th>
                        <th style="width:130px">Último acceso</th>
                        <th style="width:140px" class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="clavesBody">
                    <?php foreach ($claves as $clave):
                        $catKey = $clave['categoria'] ?? 'otros';
                        $catInfo = $categorias[$catKey] ?? $categorias['otros'];
                    ?>
                    <tr class="clave-item" data-category="<?= $catKey ?>" data-search="<?= e(strtolower($clave['referencia'] . ' ' . ($clave['observaciones'] ?? '') . ' ' . ($clave['url_sitio'] ?? ''))) ?>">
                        <td>
                            <span class="cat-badge bg-<?= $catInfo['color'] ?> bg-opacity-10 text-<?= $catInfo['color'] ?>">
                                <i class="bi bi-<?= $catInfo['icon'] ?>"></i> <?= $catInfo['label'] ?>
                            </span>
                        </td>
                        <td>
                            <span class="ref-name"><?= e($clave['referencia']) ?></span>
                            <?php if ($clave['observaciones']): ?>
                            <span class="ref-obs"><?= e($clave['observaciones']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($clave['url_sitio']): ?>
                            <a href="<?= e($clave['url_sitio']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary py-0 px-2">
                                <i class="bi bi-box-arrow-up-right"></i> Abrir
                            </a>
                            <?php else: ?>
                            <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div id="cred-area-<?= $clave['id'] ?>">
                                <button class="btn btn-sm btn-outline-info btn-show-cred" data-id="<?= $clave['id'] ?>">
                                    <i class="bi bi-eye"></i> Ver
                                </button>
                            </div>
                        </td>
                        <td>
                            <?php if (!empty($clave['ultimo_acceso'])): ?>
                            <small class="text-muted"><i class="bi bi-clock"></i> <?= format_datetime($clave['ultimo_acceso']) ?></small>
                            <?php else: ?>
                            <small class="text-muted"><i class="bi bi-eye-slash"></i> Nunca</small>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <?php if (is_admin() || is_empleado()): ?>
                                <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $clave['id'] ?>" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <?php endif; ?>
                                <?php if (is_admin()): ?>
                                <button class="btn btn-outline-secondary btn-access-log" data-id="<?= $clave['id'] ?>" title="Log de accesos">
                                    <i class="bi bi-clock-history"></i>
                                </button>
                                <form method="POST" action="<?= tenant_url("claves/{$clave['id']}/delete") ?>" class="d-inline" onsubmit="return confirm('¿Eliminar esta clave?')">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Edit Modals -->
<?php foreach ($claves as $clave): ?>
<?php if (is_admin() || is_empleado()): ?>
<div class="modal fade" id="editModal<?= $clave['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= tenant_url("claves/{$clave['id']}/update") ?>">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil"></i> Editar: <?= e($clave['referencia']) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Referencia *</label>
                            <input type="text" name="referencia" class="form-control" value="<?= e($clave['referencia']) ?>" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Categoría</label>
                            <select name="categoria" class="form-select">
                                <?php foreach ($categorias as $cKey => $cVal): ?>
                                <option value="<?= $cKey ?>" <?= ($clave['categoria'] ?? 'otros') === $cKey ? 'selected' : '' ?>><?= $cVal['label'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nuevo Usuario</label>
                            <input type="text" name="usuario" class="form-control" placeholder="Dejar vacío para mantener el actual" autocomplete="off">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nueva Contraseña</label>
                            <div class="password-wrapper">
                                <input type="password" name="password_clave" class="form-control pe-5" placeholder="Dejar vacío para mantener la actual" autocomplete="new-password">
                                <button type="button" class="toggle-pw" tabindex="-1"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">URL del Sitio</label>
                            <input type="url" name="url_sitio" class="form-control" value="<?= e($clave['url_sitio'] ?? '') ?>" placeholder="https://...">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Observaciones</label>
                            <textarea name="observaciones" class="form-control" rows="2"><?= e($clave['observaciones'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?php endforeach; ?>

<!-- Create Modal -->
<?php if (is_admin() || is_empleado()): ?>
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="<?= tenant_url("clientes/{$cliente['id']}/claves/store") ?>">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-key"></i> Nueva Clave Fiscal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Referencia *</label>
                            <input type="text" name="referencia" class="form-control" required placeholder="Ej: AFIP Clave Fiscal, ARBA...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Categoría *</label>
                            <select name="categoria" class="form-select" required>
                                <?php foreach ($categorias as $cKey => $cVal): ?>
                                <option value="<?= $cKey ?>"><?= $cVal['label'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">URL del Sitio</label>
                            <input type="url" name="url_sitio" class="form-control" placeholder="https://...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Usuario *</label>
                            <input type="text" name="usuario" class="form-control" required autocomplete="off" placeholder="Usuario o CUIT">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contraseña *</label>
                            <div class="input-group">
                                <div class="password-wrapper flex-grow-1">
                                    <input type="password" name="password_clave" id="newPassword" class="form-control pe-5" required autocomplete="new-password">
                                    <button type="button" class="toggle-pw" tabindex="-1"><i class="bi bi-eye"></i></button>
                                </div>
                                <button type="button" class="btn btn-outline-secondary" id="btnGenerate" title="Generar contraseña segura">
                                    <i class="bi bi-dice-5"></i>
                                </button>
                            </div>
                            <small class="text-muted" id="pwStrength"></small>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Observaciones</label>
                            <textarea name="observaciones" class="form-control" rows="2" placeholder="Notas adicionales..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-shield-plus"></i> Guardar Clave</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Confirm Decrypt Modal -->
<div class="modal fade" id="confirmDecrypt" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <i class="bi bi-shield-exclamation display-4 text-warning"></i>
                <h6 class="fw-bold mt-2">Confirmar acceso</h6>
                <p class="text-muted small mb-3">Esta acción quedará registrada en el log de auditoría.</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary btn-sm" id="confirmDecryptBtn"><i class="bi bi-unlock"></i> Ver Credenciales</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (is_admin()): ?>
<!-- Access Log Modal -->
<div class="modal fade" id="accessLogModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-clock-history"></i> Log de Accesos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="accessLogBody">
                <div class="text-center py-3"><div class="spinner-border text-primary"></div></div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php ob_start(); ?>
<script>
$(function() {
    const AUTO_HIDE_SECONDS = 30;
    let pendingDecryptId = null;
    let autoHideTimers = {};

    // ==========================================
    // FILTER BY CATEGORY
    // ==========================================
    $('.filter-pills .btn').on('click', function() {
        $('.filter-pills .btn').removeClass('active');
        $(this).addClass('active');
        let filter = $(this).data('filter');
        if (filter === 'all') {
            $('.clave-item').show();
        } else {
            $('.clave-item').hide();
            $(`.clave-item[data-category="${filter}"]`).show();
        }
    });

    // ==========================================
    // SEARCH
    // ==========================================
    $('#searchClaves').on('input', function() {
        let q = $(this).val().toLowerCase();
        $('.clave-item').each(function() {
            let text = $(this).data('search');
            $(this).toggle(text.includes(q));
        });
        if (q) {
            $('.filter-pills .btn').removeClass('active');
        } else {
            $('.filter-pills .btn[data-filter="all"]').addClass('active');
        }
    });

    // ==========================================
    // DECRYPT with confirmation
    // ==========================================
    $(document).on('click', '.btn-show-cred', function() {
        pendingDecryptId = $(this).data('id');
        new bootstrap.Modal('#confirmDecrypt').show();
    });

    $('#confirmDecryptBtn').on('click', function() {
        bootstrap.Modal.getInstance('#confirmDecrypt').hide();
        if (!pendingDecryptId) return;
        decryptKey(pendingDecryptId);
    });

    function decryptKey(id) {
        let $area = $('#cred-area-' + id);
        $area.html('<span class="spinner-border spinner-border-sm text-primary"></span>');

        $.get('<?= tenant_url("claves") ?>/' + id + '/decrypt')
            .done(function(data) {
                $area.html(`
                    <div class="cred-inline">
                        <span><span class="cred-label">USR:</span> <span class="cred-val">${escapeHtml(data.usuario)}</span>
                        <button class="btn-copy" data-value="${escapeAttr(data.usuario)}" title="Copiar"><i class="bi bi-clipboard"></i></button></span>
                        <span><span class="cred-label">PASS:</span> <span class="cred-val">${escapeHtml(data.password)}</span>
                        <button class="btn-copy" data-value="${escapeAttr(data.password)}" title="Copiar"><i class="bi bi-clipboard"></i></button></span>
                    </div>
                    <div class="auto-hide-bar" id="bar-${id}" style="width:100%"></div>
                    <button class="btn btn-sm btn-link text-muted p-0 mt-1 btn-hide-cred" data-id="${id}" style="font-size:.75rem">
                        <i class="bi bi-eye-slash"></i> Ocultar
                    </button>
                `);
                startAutoHide(id);
            })
            .fail(function(xhr) {
                $area.html(`
                    <span class="text-danger small"><i class="bi bi-exclamation-triangle"></i> ${xhr.responseJSON?.error || 'Error'}</span>
                    <button class="btn btn-sm btn-outline-info ms-2 btn-show-cred" data-id="${id}"><i class="bi bi-arrow-clockwise"></i></button>
                `);
            });
    }

    // ==========================================
    // AUTO-HIDE
    // ==========================================
    function startAutoHide(id) {
        let elapsed = 0;
        let $bar = $('#bar-' + id);
        if (autoHideTimers[id]) clearInterval(autoHideTimers[id]);
        autoHideTimers[id] = setInterval(function() {
            elapsed += 0.1;
            $bar.css('width', Math.max(0, 100 - (elapsed / AUTO_HIDE_SECONDS * 100)) + '%');
            if (elapsed >= AUTO_HIDE_SECONDS) {
                clearInterval(autoHideTimers[id]);
                hideCred(id);
            }
        }, 100);
    }

    function hideCred(id) {
        if (autoHideTimers[id]) clearInterval(autoHideTimers[id]);
        $('#cred-area-' + id).html(`
            <button class="btn btn-sm btn-outline-info btn-show-cred" data-id="${id}">
                <i class="bi bi-eye"></i> Ver
            </button>`);
    }

    $(document).on('click', '.btn-hide-cred', function() {
        hideCred($(this).data('id'));
    });

    // ==========================================
    // COPY TO CLIPBOARD
    // ==========================================
    $(document).on('click', '.btn-copy', function() {
        let val = $(this).data('value');
        let $btn = $(this);
        navigator.clipboard.writeText(val).then(function() {
            $btn.addClass('copied').html('<i class="bi bi-check-lg"></i>');
            setTimeout(() => $btn.removeClass('copied').html('<i class="bi bi-clipboard"></i>'), 1500);
        });
    });

    // ==========================================
    // TOGGLE PASSWORD VISIBILITY
    // ==========================================
    $(document).on('click', '.toggle-pw', function() {
        let $input = $(this).siblings('input');
        if ($input.attr('type') === 'password') {
            $input.attr('type', 'text');
            $(this).html('<i class="bi bi-eye-slash"></i>');
        } else {
            $input.attr('type', 'password');
            $(this).html('<i class="bi bi-eye"></i>');
        }
    });

    // ==========================================
    // PASSWORD GENERATOR
    // ==========================================
    $('#btnGenerate').on('click', function() {
        let chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%&*_-+=';
        let len = 16;
        let pw = '';
        let arr = new Uint32Array(len);
        crypto.getRandomValues(arr);
        for (let i = 0; i < len; i++) pw += chars[arr[i] % chars.length];
        let $input = $('#newPassword');
        $input.val(pw).attr('type', 'text');
        $input.siblings('.toggle-pw').html('<i class="bi bi-eye-slash"></i>');
        checkStrength(pw);
    });

    $('#newPassword').on('input', function() { checkStrength($(this).val()); });

    function checkStrength(pw) {
        let $el = $('#pwStrength');
        if (!pw) { $el.html(''); return; }
        let score = 0;
        if (pw.length >= 8) score++;
        if (pw.length >= 12) score++;
        if (/[a-z]/.test(pw) && /[A-Z]/.test(pw)) score++;
        if (/\d/.test(pw)) score++;
        if (/[^a-zA-Z0-9]/.test(pw)) score++;
        let labels = ['', 'Muy débil', 'Débil', 'Media', 'Fuerte', 'Muy fuerte'];
        let colors = ['', 'danger', 'warning', 'info', 'success', 'success'];
        $el.html(`<span class="text-${colors[score]}"><i class="bi bi-shield-fill-check"></i> ${labels[score]}</span>`);
    }

    <?php if (is_admin()): ?>
    // ==========================================
    // ACCESS LOG
    // ==========================================
    $(document).on('click', '.btn-access-log', function() {
        let id = $(this).data('id');
        let $body = $('#accessLogBody');
        $body.html('<div class="text-center py-3"><div class="spinner-border text-primary"></div></div>');
        new bootstrap.Modal('#accessLogModal').show();
        $.get('<?= tenant_url("claves") ?>/' + id + '/access-log')
            .done(function(data) {
                if (!data.logs || data.logs.length === 0) {
                    $body.html('<p class="text-muted text-center py-3">Sin accesos registrados.</p>');
                    return;
                }
                let html = '<table class="table table-sm"><thead><tr><th>Fecha</th><th>Usuario</th><th>IP</th></tr></thead><tbody>';
                data.logs.forEach(l => {
                    html += `<tr><td class="small">${l.created_at}</td><td>${escapeHtml(l.nombre_completo || 'Sistema')}</td><td class="small text-muted">${l.ip || ''}</td></tr>`;
                });
                html += '</tbody></table>';
                $body.html(html);
            });
    });
    <?php endif; ?>

    // ==========================================
    // HELPERS
    // ==========================================
    function escapeHtml(str) {
        let div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
    function escapeAttr(str) {
        return str.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }
});
</script>
<?php $extraJs = ob_get_clean(); ?>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
