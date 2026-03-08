<?php
$pageTitle = 'Claves Fiscales';
ob_start();
?>

<style>
    .client-search-box {
        background: #fff;
        border: 2px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.25rem 1.5rem;
        transition: border-color .2s;
    }
    .client-search-box:focus-within {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37,99,235,.1);
    }
    .search-results-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: .75rem;
        box-shadow: 0 8px 24px rgba(0,0,0,.12);
        z-index: 100;
        max-height: 320px;
        overflow-y: auto;
        display: none;
    }
    .search-results-dropdown.show { display: block; }
    .search-result-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .75rem 1rem;
        cursor: pointer;
        border-bottom: 1px solid #f1f5f9;
        transition: background .15s;
    }
    .search-result-item:hover { background: #f8fafc; }
    .search-result-item:last-child { border-bottom: none; }
    .search-result-item .client-name { font-weight: 600; color: #1e293b; }
    .search-result-item .client-cuit { font-size: .8rem; color: #64748b; }
    .search-result-item .keys-count {
        background: #eff6ff;
        color: #2563eb;
        font-size: .75rem;
        font-weight: 600;
        padding: .2rem .6rem;
        border-radius: 1rem;
    }
    .active-client-banner {
        background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%);
        border: 1px solid #bfdbfe;
        border-radius: .75rem;
        padding: 1rem 1.25rem;
    }
    .clave-row {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: .75rem;
        padding: 1rem 1.25rem;
        transition: all .2s;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .clave-row:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,.06);
        border-color: #cbd5e1;
    }
    .clave-row .cat-icon {
        width: 40px;
        height: 40px;
        border-radius: .5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .clave-row .clave-info { flex: 1; min-width: 0; }
    .clave-row .clave-actions { display: flex; gap: .5rem; align-items: center; flex-shrink: 0; }
    .cred-inline {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: .5rem;
        padding: .5rem .75rem;
        margin-top: .5rem;
        font-family: 'Consolas', 'Monaco', monospace;
        font-size: .85rem;
        display: flex;
        gap: 1.5rem;
        align-items: center;
        flex-wrap: wrap;
    }
    .cred-inline .cred-item { display: flex; align-items: center; gap: .5rem; }
    .cred-inline .cred-label { color: #64748b; font-size: .75rem; font-weight: 600; text-transform: uppercase; }
    .cred-inline .cred-value { color: #1e293b; font-weight: 600; user-select: all; }
    .btn-copy-sm {
        border: none;
        background: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 2px 4px;
        border-radius: 3px;
        font-size: .85rem;
        transition: all .15s;
    }
    .btn-copy-sm:hover { background: #e2e8f0; color: #1e293b; }
    .btn-copy-sm.copied { color: #10b981; }
    .auto-hide-progress {
        height: 2px;
        background: #ef4444;
        border-radius: 1px;
        margin-top: .35rem;
    }
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #94a3b8;
    }
    .empty-state i { font-size: 3rem; }
    .quick-clients .quick-chip {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .35rem .75rem;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 2rem;
        font-size: .8rem;
        color: #475569;
        cursor: pointer;
        transition: all .15s;
        text-decoration: none;
    }
    .quick-chip:hover { border-color: #2563eb; color: #2563eb; background: #eff6ff; }
    .quick-chip.active { border-color: #2563eb; color: #fff; background: #2563eb; }
</style>

<!-- Search Section -->
<div class="mb-4">
    <div class="client-search-box">
        <div class="d-flex align-items-center gap-3">
            <i class="bi bi-search fs-5 text-muted"></i>
            <div class="flex-grow-1 position-relative">
                <input type="text" id="clientSearch" class="form-control form-control-lg border-0 shadow-none p-0" 
                       placeholder="Buscar cliente por nombre o CUIT..." autocomplete="off"
                       value="<?= $clienteActivo ? e($clienteActivo['razon_social'] . ' - ' . $clienteActivo['cuit']) : '' ?>">
                <div class="search-results-dropdown" id="searchResults"></div>
            </div>
            <?php if ($clienteActivo): ?>
            <a href="<?= tenant_url('claves-fiscales') ?>" class="btn btn-sm btn-outline-secondary" title="Limpiar">
                <i class="bi bi-x-lg"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Quick Client Chips -->
<?php if (!$clienteActivo && !empty($clientes)): ?>
<div class="quick-clients mb-4">
    <small class="text-muted d-block mb-2"><i class="bi bi-lightning"></i> Acceso rápido:</small>
    <div class="d-flex flex-wrap gap-2">
        <?php foreach (array_slice($clientes, 0, 12) as $cli): ?>
        <a href="<?= tenant_url('claves-fiscales') ?>?cliente_id=<?= $cli['id'] ?>" class="quick-chip">
            <i class="bi bi-person"></i>
            <?= e($cli['razon_social']) ?>
            <span class="text-muted">(<?= e($cli['cuit']) ?>)</span>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Active Client Banner + Keys -->
<?php if ($clienteActivo): ?>
<div class="active-client-banner mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-building text-primary"></i> <?= e($clienteActivo['razon_social']) ?>
            </h5>
            <small class="text-muted">CUIT: <?= e($clienteActivo['cuit']) ?> &mdash; <?= count($claves) ?> clave(s)</small>
        </div>
        <div class="d-flex gap-2">
            <?php if (is_admin() || is_empleado()): ?>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bi bi-plus-lg"></i> Nueva Clave
            </button>
            <?php endif; ?>
            <a href="<?= tenant_url("clientes/{$clienteActivo['id']}/ver") ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-person-vcard"></i> Ver Cliente
            </a>
        </div>
    </div>
</div>

<!-- Keys List -->
<?php if (empty($claves)): ?>
    <div class="empty-state">
        <i class="bi bi-key"></i>
        <p class="mt-2 mb-0">Este cliente no tiene claves fiscales registradas.</p>
        <?php if (is_admin() || is_empleado()): ?>
        <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-lg"></i> Crear primera clave
        </button>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="d-flex flex-column gap-2" id="clavesList">
        <?php foreach ($claves as $clave):
            $catKey = $clave['categoria'] ?? 'otros';
            $catInfo = $categorias[$catKey] ?? $categorias['otros'];
        ?>
        <div class="clave-row" data-id="<?= $clave['id'] ?>">
            <div class="cat-icon bg-<?= $catInfo['color'] ?> bg-opacity-10 text-<?= $catInfo['color'] ?>">
                <i class="bi bi-<?= $catInfo['icon'] ?>"></i>
            </div>
            <div class="clave-info">
                <div class="d-flex align-items-center gap-2">
                    <strong><?= e($clave['referencia']) ?></strong>
                    <span class="badge bg-<?= $catInfo['color'] ?> bg-opacity-10 text-<?= $catInfo['color'] ?>" style="font-size:.7rem"><?= $catInfo['label'] ?></span>
                    <?php if ($clave['url_sitio']): ?>
                    <a href="<?= e($clave['url_sitio']) ?>" target="_blank" class="text-muted small"><i class="bi bi-box-arrow-up-right"></i></a>
                    <?php endif; ?>
                </div>
                <?php if ($clave['observaciones']): ?>
                <small class="text-muted"><?= e($clave['observaciones']) ?></small>
                <?php endif; ?>
                <!-- Credential display area -->
                <div id="cred-<?= $clave['id'] ?>" class="cred-area"></div>
            </div>
            <div class="clave-actions">
                <?php if (!empty($clave['ultimo_acceso'])): ?>
                <small class="text-muted d-none d-md-block" title="Último acceso" style="font-size:.7rem">
                    <i class="bi bi-clock"></i> <?= format_datetime($clave['ultimo_acceso']) ?>
                </small>
                <?php endif; ?>
                <button class="btn btn-sm btn-outline-primary btn-decrypt-quick" data-id="<?= $clave['id'] ?>" title="Ver credenciales">
                    <i class="bi bi-eye"></i>
                </button>
                <?php if (is_admin() || is_empleado()): ?>
                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $clave['id'] ?>" title="Editar">
                    <i class="bi bi-pencil"></i>
                </button>
                <?php endif; ?>
                <?php if (is_admin()): ?>
                <form method="POST" action="<?= tenant_url("claves/{$clave['id']}/delete") ?>" class="d-inline" onsubmit="return confirm('¿Eliminar esta clave?')">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Edit Modal -->
        <?php if (is_admin() || is_empleado()): ?>
        <div class="modal fade" id="editModal<?= $clave['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="<?= tenant_url("claves/{$clave['id']}/update") ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="redirect_to" value="<?= e(tenant_url('claves-fiscales') . '?cliente_id=' . $clienteActivo['id']) ?>">
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
                                    <input type="text" name="usuario" class="form-control" placeholder="Dejar vacío para mantener" autocomplete="off">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Nueva Contraseña</label>
                                    <input type="password" name="password_clave" class="form-control" placeholder="Dejar vacío para mantener" autocomplete="new-password">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">URL del Sitio</label>
                                    <input type="url" name="url_sitio" class="form-control" value="<?= e($clave['url_sitio'] ?? '') ?>">
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
    </div>
<?php endif; ?>

<!-- Create Modal -->
<?php if (is_admin() || is_empleado()): ?>
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="<?= tenant_url("clientes/{$clienteActivo['id']}/claves/store") ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="redirect_to" value="<?= e(tenant_url('claves-fiscales') . '?cliente_id=' . $clienteActivo['id']) ?>">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-key"></i> Nueva Clave - <?= e($clienteActivo['razon_social']) ?></h5>
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
                                <input type="password" name="password_clave" id="newPassword" class="form-control" required autocomplete="new-password">
                                <button type="button" class="btn btn-outline-secondary btn-toggle-pw" title="Ver/ocultar">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="btnGenerate" title="Generar contraseña segura">
                                    <i class="bi bi-dice-5"></i>
                                </button>
                            </div>
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

<?php else: ?>
<!-- No client selected - show instructions -->
<div class="empty-state">
    <i class="bi bi-search"></i>
    <h5 class="mt-3 text-muted">Buscá un cliente para ver sus claves</h5>
    <p class="text-muted">Escribí el nombre o CUIT del cliente en el buscador de arriba.</p>
</div>
<?php endif; ?>

<?php ob_start(); ?>
<script>
$(function() {
    const AUTO_HIDE_SECONDS = 30;
    let autoHideTimers = {};
    let searchTimeout = null;

    // ==========================================
    // CLIENT SEARCH (live search)
    // ==========================================
    const $search = $('#clientSearch');
    const $results = $('#searchResults');

    $search.on('input', function() {
        let q = $(this).val().trim();
        clearTimeout(searchTimeout);
        
        if (q.length < 2) {
            $results.removeClass('show').html('');
            return;
        }

        searchTimeout = setTimeout(function() {
            $.get('<?= tenant_url("api/claves/search") ?>', { q: q })
                .done(function(data) {
                    if (!data.results || data.results.length === 0) {
                        $results.html('<div class="p-3 text-center text-muted small">No se encontraron clientes</div>').addClass('show');
                        return;
                    }
                    let html = '';
                    data.results.forEach(function(c) {
                        html += `<div class="search-result-item" data-url="<?= tenant_url('claves-fiscales') ?>?cliente_id=${c.id}">
                            <div>
                                <div class="client-name">${escapeHtml(c.razon_social)}</div>
                                <div class="client-cuit">CUIT: ${escapeHtml(c.cuit)}</div>
                            </div>
                            <span class="keys-count"><i class="bi bi-key"></i> ${c.total_claves} clave(s)</span>
                        </div>`;
                    });
                    $results.html(html).addClass('show');
                });
        }, 250);
    });

    $search.on('focus', function() {
        if ($results.children().length > 0) $results.addClass('show');
    });

    $(document).on('click', '.search-result-item', function() {
        window.location.href = $(this).data('url');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.client-search-box').length) {
            $results.removeClass('show');
        }
    });

    // ==========================================
    // QUICK DECRYPT (single click, no confirmation in this view)
    // ==========================================
    $(document).on('click', '.btn-decrypt-quick', function() {
        let id = $(this).data('id');
        let $btn = $(this);
        let $area = $('#cred-' + id);

        // Toggle: if already showing, hide
        if ($area.children().length > 0) {
            $area.html('');
            $btn.html('<i class="bi bi-eye"></i>').removeClass('btn-secondary').addClass('btn-outline-primary');
            if (autoHideTimers[id]) clearInterval(autoHideTimers[id]);
            return;
        }

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.get('<?= tenant_url("claves") ?>/' + id + '/decrypt')
            .done(function(data) {
                $btn.prop('disabled', false).html('<i class="bi bi-eye-slash"></i>').removeClass('btn-outline-primary').addClass('btn-secondary');
                let html = `
                    <div class="cred-inline">
                        <div class="cred-item">
                            <span class="cred-label">Usuario:</span>
                            <span class="cred-value">${escapeHtml(data.usuario)}</span>
                            <button class="btn-copy-sm" data-copy="${escapeAttr(data.usuario)}" title="Copiar">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                        <div class="cred-item">
                            <span class="cred-label">Clave:</span>
                            <span class="cred-value">${escapeHtml(data.password)}</span>
                            <button class="btn-copy-sm" data-copy="${escapeAttr(data.password)}" title="Copiar">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </div>
                    <div class="auto-hide-progress" id="bar-${id}" style="width:100%"></div>`;
                $area.html(html);
                startAutoHide(id, $btn);
            })
            .fail(function(xhr) {
                $btn.prop('disabled', false).html('<i class="bi bi-eye"></i>');
                alert(xhr.responseJSON?.error || 'Error al descifrar');
            });
    });

    // ==========================================
    // AUTO-HIDE
    // ==========================================
    function startAutoHide(id, $btn) {
        let elapsed = 0;
        let $bar = $('#bar-' + id);
        if (autoHideTimers[id]) clearInterval(autoHideTimers[id]);
        
        autoHideTimers[id] = setInterval(function() {
            elapsed += 0.1;
            let pct = Math.max(0, 100 - (elapsed / AUTO_HIDE_SECONDS * 100));
            $bar.css('width', pct + '%');
            if (elapsed >= AUTO_HIDE_SECONDS) {
                clearInterval(autoHideTimers[id]);
                $('#cred-' + id).html('');
                $btn.html('<i class="bi bi-eye"></i>').removeClass('btn-secondary').addClass('btn-outline-primary');
            }
        }, 100);
    }

    // ==========================================
    // COPY
    // ==========================================
    $(document).on('click', '.btn-copy-sm', function(e) {
        e.stopPropagation();
        let val = $(this).data('copy');
        let $b = $(this);
        navigator.clipboard.writeText(val).then(function() {
            $b.addClass('copied').html('<i class="bi bi-check-lg"></i>');
            setTimeout(() => $b.removeClass('copied').html('<i class="bi bi-clipboard"></i>'), 1500);
        });
    });

    // ==========================================
    // TOGGLE PASSWORD & GENERATOR
    // ==========================================
    $(document).on('click', '.btn-toggle-pw', function() {
        let $input = $(this).closest('.input-group').find('input[name="password_clave"]');
        if ($input.attr('type') === 'password') {
            $input.attr('type', 'text');
            $(this).html('<i class="bi bi-eye-slash"></i>');
        } else {
            $input.attr('type', 'password');
            $(this).html('<i class="bi bi-eye"></i>');
        }
    });

    $('#btnGenerate').on('click', function() {
        let chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%&*_-+=';
        let pw = '';
        let arr = new Uint32Array(16);
        crypto.getRandomValues(arr);
        for (let i = 0; i < 16; i++) pw += chars[arr[i] % chars.length];
        let $input = $('#newPassword');
        $input.val(pw).attr('type', 'text');
        $input.closest('.input-group').find('.btn-toggle-pw').html('<i class="bi bi-eye-slash"></i>');
    });

    // ==========================================
    // HELPERS
    // ==========================================
    function escapeHtml(str) {
        let d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }
    function escapeAttr(str) {
        return str.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }
});
</script>
<?php $extraJs = ob_get_clean(); ?>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
