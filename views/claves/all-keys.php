<?php
$pageTitle = 'Todas las Claves Fiscales';
ob_start();
?>

<style>
    .stats-bar {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .stats-bar .stat {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: .75rem;
        padding: .75rem 1.25rem;
        display: flex;
        align-items: center;
        gap: .75rem;
    }
    .stats-bar .stat-icon {
        width: 42px; height: 42px;
        border-radius: .5rem;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
    }
    .stats-bar .stat-value { font-size: 1.5rem; font-weight: 700; color: #1e293b; line-height: 1; }
    .stats-bar .stat-label { font-size: .75rem; color: #64748b; }
    .client-group {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: .75rem;
        overflow: hidden;
        margin-bottom: 1rem;
    }
    .client-group-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: .75rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        transition: background .15s;
    }
    .client-group-header:hover { background: #f1f5f9; }
    .client-group-header .client-title { font-weight: 700; color: #1e293b; }
    .client-group-header .client-cuit { font-size: .8rem; color: #64748b; margin-left: .5rem; }
    .client-group-header .badge-count {
        background: #eff6ff; color: #2563eb;
        font-size: .75rem; font-weight: 600;
        padding: .2rem .6rem; border-radius: 1rem;
    }
    .client-group-body { padding: 0; }
    .clave-row-compact {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .6rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        transition: background .1s;
    }
    .clave-row-compact:last-child { border-bottom: none; }
    .clave-row-compact:hover { background: #fafbfd; }
    .clave-row-compact .cat-dot {
        width: 32px; height: 32px;
        border-radius: .4rem;
        display: flex; align-items: center; justify-content: center;
        font-size: .85rem;
        flex-shrink: 0;
    }
    .clave-row-compact .clave-info { flex: 1; min-width: 0; }
    .clave-row-compact .clave-ref { font-weight: 600; font-size: .9rem; color: #1e293b; }
    .clave-row-compact .clave-obs { font-size: .75rem; color: #94a3b8; }
    .clave-row-compact .clave-actions { flex-shrink: 0; display: flex; gap: .35rem; align-items: center; }
    .cred-inline-sm {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: .4rem;
        padding: .4rem .75rem;
        font-family: 'Consolas', 'Monaco', monospace;
        font-size: .8rem;
        display: inline-flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
        margin-left: .5rem;
    }
    .cred-inline-sm .cred-label { color: #64748b; font-size: .7rem; font-weight: 600; }
    .cred-inline-sm .cred-val { color: #166534; font-weight: 600; user-select: all; }
    .btn-copy-xs {
        border: none; background: none; color: #94a3b8; cursor: pointer;
        padding: 1px 4px; border-radius: 3px; font-size: .8rem;
    }
    .btn-copy-xs:hover { background: #dcfce7; color: #166534; }
    .btn-copy-xs.copied { color: #10b981; }
    .auto-bar { height: 2px; background: #ef4444; border-radius: 1px; margin-top: .25rem; }
    .toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }
    .toolbar .search-input { max-width: 350px; }
    .filter-cats .btn { border-radius: 2rem; font-size: .75rem; padding: .25rem .6rem; }
</style>

<!-- Stats -->
<div class="stats-bar flex-wrap">
    <div class="stat">
        <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-people"></i></div>
        <div>
            <div class="stat-value"><?= $totalClientes ?></div>
            <div class="stat-label">Clientes</div>
        </div>
    </div>
    <div class="stat">
        <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-key"></i></div>
        <div>
            <div class="stat-value"><?= $totalClaves ?></div>
            <div class="stat-label">Claves totales</div>
        </div>
    </div>
    <?php
    $catCounts = [];
    foreach ($grouped as $g) {
        foreach ($g['claves'] as $c) {
            $ck = $c['categoria'] ?? 'otros';
            $catCounts[$ck] = ($catCounts[$ck] ?? 0) + 1;
        }
    }
    arsort($catCounts);
    foreach (array_slice($catCounts, 0, 3, true) as $ck => $cnt):
        $ci = $categorias[$ck] ?? $categorias['otros'];
    ?>
    <div class="stat">
        <div class="stat-icon bg-<?= $ci['color'] ?> bg-opacity-10 text-<?= $ci['color'] ?>"><i class="bi bi-<?= $ci['icon'] ?>"></i></div>
        <div>
            <div class="stat-value"><?= $cnt ?></div>
            <div class="stat-label"><?= $ci['label'] ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Toolbar -->
<div class="toolbar">
    <div class="d-flex gap-2 align-items-center">
        <div class="input-group input-group-sm search-input">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" id="globalSearch" class="form-control" placeholder="Buscar cliente o clave...">
        </div>
        <div class="filter-cats d-none d-md-flex gap-1">
            <button class="btn btn-sm btn-outline-secondary active" data-cat="all">Todas</button>
            <?php foreach ($catCounts as $ck => $cnt):
                $ci = $categorias[$ck] ?? $categorias['otros'];
            ?>
            <button class="btn btn-sm btn-outline-<?= $ci['color'] ?>" data-cat="<?= $ck ?>">
                <i class="bi bi-<?= $ci['icon'] ?>"></i> <?= $ci['label'] ?>
            </button>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= tenant_url('claves-fiscales') ?>" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-search"></i> Buscar por cliente
        </a>
        <button class="btn btn-sm btn-outline-secondary" id="btnExpandAll" title="Expandir/Colapsar todo">
            <i class="bi bi-arrows-expand"></i>
        </button>
    </div>
</div>

<!-- Client Groups -->
<?php if (empty($grouped)): ?>
<div class="text-center py-5">
    <i class="bi bi-key display-3 text-muted"></i>
    <p class="text-muted mt-3">No hay claves fiscales registradas en el sistema.</p>
</div>
<?php else: ?>
<div id="clientGroups">
    <?php foreach ($grouped as $group): ?>
    <div class="client-group" data-client="<?= e(strtolower($group['razon_social'] . ' ' . $group['cuit'])) ?>">
        <div class="client-group-header" data-bs-toggle="collapse" data-bs-target="#group-<?= $group['cliente_id'] ?>">
            <div class="d-flex align-items-center">
                <i class="bi bi-chevron-down me-2 text-muted collapse-icon"></i>
                <span class="client-title"><?= e($group['razon_social']) ?></span>
                <span class="client-cuit"><?= e($group['cuit']) ?></span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge-count"><i class="bi bi-key"></i> <?= count($group['claves']) ?></span>
                <a href="<?= tenant_url("claves-fiscales?cliente_id={$group['cliente_id']}") ?>" class="btn btn-sm btn-outline-primary py-0 px-2" onclick="event.stopPropagation()" title="Gestionar">
                    <i class="bi bi-gear"></i>
                </a>
            </div>
        </div>
        <div class="collapse show client-group-body" id="group-<?= $group['cliente_id'] ?>">
            <?php foreach ($group['claves'] as $clave):
                $catKey = $clave['categoria'] ?? 'otros';
                $catInfo = $categorias[$catKey] ?? $categorias['otros'];
            ?>
            <div class="clave-row-compact" data-cat="<?= $catKey ?>" data-search="<?= e(strtolower($clave['referencia'] . ' ' . ($clave['observaciones'] ?? ''))) ?>">
                <div class="cat-dot bg-<?= $catInfo['color'] ?> bg-opacity-10 text-<?= $catInfo['color'] ?>">
                    <i class="bi bi-<?= $catInfo['icon'] ?>"></i>
                </div>
                <div class="clave-info">
                    <span class="clave-ref"><?= e($clave['referencia']) ?></span>
                    <?php if ($clave['observaciones']): ?>
                    <span class="clave-obs d-none d-md-inline ms-2"><?= e($clave['observaciones']) ?></span>
                    <?php endif; ?>
                    <span id="cred-<?= $clave['id'] ?>"></span>
                </div>
                <div class="clave-actions">
                    <?php if ($clave['url_sitio']): ?>
                    <a href="<?= e($clave['url_sitio']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary py-0 px-1" title="Ir al sitio">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                    <?php endif; ?>
                    <button class="btn btn-sm btn-outline-primary py-0 px-1 btn-decrypt-inline" data-id="<?= $clave['id'] ?>" title="Ver credenciales">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php ob_start(); ?>
<script>
$(function() {
    const AUTO_HIDE = 30;
    let timers = {};

    // ==========================================
    // GLOBAL SEARCH
    // ==========================================
    $('#globalSearch').on('input', function() {
        let q = $(this).val().toLowerCase();
        if (!q) {
            $('.client-group').show();
            $('.clave-row-compact').show();
            return;
        }
        $('.client-group').each(function() {
            let clientMatch = $(this).data('client').includes(q);
            let hasVisibleKey = false;
            $(this).find('.clave-row-compact').each(function() {
                let keyMatch = $(this).data('search').includes(q);
                $(this).toggle(clientMatch || keyMatch);
                if (clientMatch || keyMatch) hasVisibleKey = true;
            });
            $(this).toggle(clientMatch || hasVisibleKey);
        });
    });

    // ==========================================
    // CATEGORY FILTER
    // ==========================================
    $('.filter-cats .btn').on('click', function() {
        $('.filter-cats .btn').removeClass('active');
        $(this).addClass('active');
        let cat = $(this).data('cat');
        if (cat === 'all') {
            $('.clave-row-compact').show();
            $('.client-group').show();
        } else {
            $('.clave-row-compact').hide().filter(`[data-cat="${cat}"]`).show();
            // Hide groups with no visible keys
            $('.client-group').each(function() {
                $(this).toggle($(this).find('.clave-row-compact:visible').length > 0);
            });
        }
    });

    // ==========================================
    // EXPAND / COLLAPSE ALL
    // ==========================================
    let allExpanded = true;
    $('#btnExpandAll').on('click', function() {
        allExpanded = !allExpanded;
        if (allExpanded) {
            $('.client-group-body').collapse('show');
            $(this).html('<i class="bi bi-arrows-expand"></i>');
        } else {
            $('.client-group-body').collapse('hide');
            $(this).html('<i class="bi bi-arrows-collapse"></i>');
        }
    });

    // ==========================================
    // INLINE DECRYPT
    // ==========================================
    $(document).on('click', '.btn-decrypt-inline', function() {
        let id = $(this).data('id');
        let $btn = $(this);
        let $cred = $('#cred-' + id);

        if ($cred.children().length > 0) {
            $cred.html('');
            $btn.html('<i class="bi bi-eye"></i>').removeClass('btn-secondary').addClass('btn-outline-primary');
            if (timers[id]) clearInterval(timers[id]);
            return;
        }

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" style="width:.7rem;height:.7rem"></span>');

        $.get('<?= tenant_url("claves") ?>/' + id + '/decrypt')
            .done(function(data) {
                $btn.prop('disabled', false).html('<i class="bi bi-eye-slash"></i>').removeClass('btn-outline-primary').addClass('btn-secondary');
                $cred.html(`
                    <span class="cred-inline-sm">
                        <span><span class="cred-label">USR:</span> <span class="cred-val">${esc(data.usuario)}</span>
                        <button class="btn-copy-xs" data-cp="${escA(data.usuario)}"><i class="bi bi-clipboard"></i></button></span>
                        <span><span class="cred-label">PASS:</span> <span class="cred-val">${esc(data.password)}</span>
                        <button class="btn-copy-xs" data-cp="${escA(data.password)}"><i class="bi bi-clipboard"></i></button></span>
                    </span>
                    <div class="auto-bar" id="bar-${id}" style="width:100%"></div>
                `);
                startHide(id, $btn);
            })
            .fail(function(xhr) {
                $btn.prop('disabled', false).html('<i class="bi bi-eye"></i>');
                alert(xhr.responseJSON?.error || 'Error al descifrar');
            });
    });

    function startHide(id, $btn) {
        let t = 0;
        if (timers[id]) clearInterval(timers[id]);
        timers[id] = setInterval(function() {
            t += 0.1;
            $('#bar-' + id).css('width', Math.max(0, 100 - t / AUTO_HIDE * 100) + '%');
            if (t >= AUTO_HIDE) {
                clearInterval(timers[id]);
                $('#cred-' + id).html('');
                $btn.html('<i class="bi bi-eye"></i>').removeClass('btn-secondary').addClass('btn-outline-primary');
            }
        }, 100);
    }

    // ==========================================
    // COPY
    // ==========================================
    $(document).on('click', '.btn-copy-xs', function(e) {
        e.stopPropagation();
        let val = $(this).data('cp');
        let $b = $(this);
        navigator.clipboard.writeText(val).then(function() {
            $b.addClass('copied').html('<i class="bi bi-check-lg"></i>');
            setTimeout(() => $b.removeClass('copied').html('<i class="bi bi-clipboard"></i>'), 1500);
        });
    });

    // ==========================================
    // HELPERS
    // ==========================================
    function esc(s) { let d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
    function escA(s) { return s.replace(/"/g, '&quot;').replace(/'/g, '&#39;'); }
});
</script>
<?php $extraJs = ob_get_clean(); ?>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
