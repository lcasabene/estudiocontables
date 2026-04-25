<?php
use Core\Session;
use Core\Tenant;
use Core\CSRF;
$currentUser = Core\Auth::user();
$slug = Tenant::slug();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Dashboard') ?> - <?= e(Tenant::name() ?? 'Luis Ariel Casabene') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #2563eb;
            --sidebar-bg: #1e293b;
            --sidebar-text: #94a3b8;
            --sidebar-active: #ffffff;
        }
        body { background: #f1f5f9; font-family: 'Inter', system-ui, sans-serif; }
        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--sidebar-width); background: var(--sidebar-bg);
            z-index: 1000; transition: transform .3s;
            overflow-y: auto;
        }
        .sidebar-brand {
            padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,.1);
            color: #fff; font-weight: 700; font-size: 1.1rem;
        }
        .sidebar-brand small { display: block; font-size: .75rem; color: var(--sidebar-text); font-weight: 400; }
        .sidebar-nav { padding: 1rem 0; }
        .sidebar-nav .nav-section {
            padding: .5rem 1.5rem; font-size: .7rem; text-transform: uppercase;
            letter-spacing: .05em; color: rgba(148,163,184,.6); margin-top: .5rem;
        }
        .sidebar-nav a {
            display: flex; align-items: center; gap: .75rem;
            padding: .6rem 1.5rem; color: var(--sidebar-text);
            text-decoration: none; font-size: .875rem; transition: all .2s;
            border-left: 3px solid transparent;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            color: var(--sidebar-active); background: rgba(255,255,255,.05);
            border-left-color: var(--primary-color);
        }
        .sidebar-nav a i { font-size: 1.1rem; width: 1.5rem; text-align: center; }
        .main-content { margin-left: var(--sidebar-width); min-height: 100vh; }
        .top-bar {
            background: #fff; border-bottom: 1px solid #e2e8f0;
            padding: .75rem 1.5rem; display: flex; align-items: center; justify-content: space-between;
        }
        .content-area { padding: 1.5rem; }
        .card { border: none; box-shadow: 0 1px 3px rgba(0,0,0,.08); border-radius: .75rem; }
        .card-header { background: #fff; border-bottom: 1px solid #e2e8f0; font-weight: 600; }
        .stat-card { border-left: 4px solid var(--primary-color); }
        .stat-card .stat-value { font-size: 2rem; font-weight: 700; color: #1e293b; }
        .stat-card .stat-label { color: #64748b; font-size: .85rem; }
        .btn-primary { background: var(--primary-color); border-color: var(--primary-color); }
        .table th { font-weight: 600; font-size: .85rem; color: #475569; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
    <?php if (isset($extraCss)) echo $extraCss; ?>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-building"></i> <?= e(Tenant::name() ?? 'Estudio') ?>
            <small>Sistema de Gestión Contable</small>
        </div>
        <div class="sidebar-nav">
            <div class="nav-section">Principal</div>
            <a href="<?= tenant_url('dashboard') ?>" class="<?= str_contains($_SERVER['REQUEST_URI'], '/dashboard') ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <?php if (is_admin() || is_empleado()): ?>
            <div class="nav-section">Gestión</div>
            <a href="<?= tenant_url('clientes') ?>" class="<?= str_contains($_SERVER['REQUEST_URI'], '/clientes') && !str_contains($_SERVER['REQUEST_URI'], '/claves') ? 'active' : '' ?>">
                <i class="bi bi-people"></i> Clientes
            </a>
            <a href="<?= tenant_url('claves-fiscales/todas') ?>" class="<?= str_contains($_SERVER['REQUEST_URI'], '/claves-fiscales') ? 'active' : '' ?>">
                <i class="bi bi-key"></i> Claves Fiscales
            </a>
            <a href="<?= tenant_url('exenciones/vencimientos') ?>" class="<?= str_contains($_SERVER['REQUEST_URI'], '/exenciones/vencimientos') ? 'active' : '' ?>">
                <i class="bi bi-shield-exclamation"></i> Exenciones
            </a>
            <?php endif; ?>

            <?php if (is_admin()): ?>
            <div class="nav-section">Configuración</div>
            <a href="<?= tenant_url('configuracion') ?>" class="<?= str_contains($_SERVER['REQUEST_URI'], '/configuracion') ? 'active' : '' ?>">
                <i class="bi bi-gear"></i> Configuración
            </a>
            <a href="<?= tenant_url('condiciones-fiscales') ?>" class="<?= str_contains($_SERVER['REQUEST_URI'], '/condiciones-fiscales') ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-ruled"></i> Condiciones Fiscales
            </a>
            <div class="nav-section">Administración</div>
            <a href="<?= tenant_url('usuarios') ?>" class="<?= str_contains($_SERVER['REQUEST_URI'], '/usuarios') ? 'active' : '' ?>">
                <i class="bi bi-person-gear"></i> Usuarios
            </a>
            <a href="<?= tenant_url('auditoria') ?>" class="<?= str_contains($_SERVER['REQUEST_URI'], '/auditoria') ? 'active' : '' ?>">
                <i class="bi bi-shield-check"></i> Auditoría
            </a>
            <div class="nav-section">Blog</div>
            <a href="<?= tenant_url('blog') ?>" class="<?= str_contains($_SERVER['REQUEST_URI'], '/blog') ? 'active' : '' ?>">
                <i class="bi bi-journal-text"></i> Artículos
            </a>
            <?php endif; ?>

            <?php if (is_cliente()): ?>
            <div class="nav-section">Mi Portal</div>
            <a href="<?= tenant_url('portal') ?>" class="<?= str_contains($_SERVER['REQUEST_URI'], '/portal') ? 'active' : '' ?>">
                <i class="bi bi-folder2-open"></i> Mis Empresas
            </a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-outline-secondary d-md-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="mb-0"><?= e($pageTitle ?? 'Dashboard') ?></h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small">
                    <i class="bi bi-person-circle"></i> <?= e($currentUser['name'] ?? '') ?>
                    <span class="badge bg-secondary ms-1"><?= e($currentUser['role'] ?? '') ?></span>
                </span>
                <a href="<?= tenant_url('logout') ?>" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </a>
            </div>
        </div>

        <div class="content-area">
            <?php if ($msg = get_flash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle"></i> <?= e($msg) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($msg = get_flash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle"></i> <?= e($msg) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <?php if (isset($extraJs)) echo $extraJs; ?>
</body>
</html>
