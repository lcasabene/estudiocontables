<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - Entidades Religiosas | Luis Ariel Casabene</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1e3a5f;
            --primary-light: #2563eb;
            --accent: #d4af37;
            --dark: #0f172a;
            --gray: #64748b;
            --light: #f8fafc;
        }
        body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; background: var(--light); color: #334155; }
        a { text-decoration: none; }

        /* Navbar */
        .blog-nav {
            background: var(--dark);
            padding: 1rem 0;
            border-bottom: 3px solid var(--accent);
        }
        .blog-nav .brand { color: #fff; font-weight: 700; font-size: 1.1rem; }
        .blog-nav .brand span { color: var(--accent); }
        .blog-nav .brand i { margin-right: 0.5rem; }
        .blog-nav a.nav-link-custom { color: rgba(255,255,255,0.7); font-size: 0.9rem; transition: color 0.3s; }
        .blog-nav a.nav-link-custom:hover { color: var(--accent); }

        /* Hero */
        .blog-hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--dark) 100%);
            color: #fff;
            padding: 4rem 0 3rem;
            text-align: center;
        }
        .blog-hero h1 { font-weight: 800; font-size: 2.5rem; margin-bottom: 0.5rem; }
        .blog-hero h1 span { color: var(--accent); }
        .blog-hero p { opacity: 0.7; font-size: 1.1rem; max-width: 600px; margin: 0 auto; }

        /* Cards */
        .post-card {
            background: #fff;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .post-card:hover { transform: translateY(-5px); box-shadow: 0 12px 30px rgba(0,0,0,0.12); }
        .post-card-img {
            height: 200px;
            background: linear-gradient(135deg, var(--primary), #1e3a5f);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent);
            font-size: 3rem;
        }
        .post-card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .post-card-body {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .post-card-body h3 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.75rem;
            line-height: 1.4;
        }
        .post-card-body h3 a { color: var(--dark); }
        .post-card-body h3 a:hover { color: var(--primary-light); }
        .post-card-body p {
            color: var(--gray);
            font-size: 0.9rem;
            line-height: 1.6;
            flex: 1;
        }
        .post-meta {
            font-size: 0.8rem;
            color: var(--gray);
            margin-top: auto;
            padding-top: 1rem;
            border-top: 1px solid #f1f5f9;
        }
        .post-meta i { color: var(--accent); }
        .btn-read {
            display: inline-block;
            color: var(--primary-light);
            font-weight: 600;
            font-size: 0.9rem;
        }
        .btn-read:hover { color: var(--accent); }
        .btn-read i { transition: transform 0.3s; }
        .btn-read:hover i { transform: translateX(4px); }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 4rem 0;
        }
        .empty-state i { font-size: 4rem; color: var(--gray); opacity: 0.3; }
        .empty-state h3 { color: var(--gray); margin-top: 1rem; }

        /* Pagination */
        .pagination .page-link { color: var(--primary); border-color: #e2e8f0; }
        .pagination .page-item.active .page-link { background: var(--primary); border-color: var(--primary); }

        /* Footer */
        .blog-footer {
            background: var(--dark);
            color: rgba(255,255,255,0.5);
            padding: 2rem 0;
            text-align: center;
            font-size: 0.9rem;
            margin-top: 4rem;
        }
        .blog-footer a { color: var(--accent); }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="blog-nav">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="<?= base_url('/') ?>" class="brand">
                <i class="bi bi-building"></i>Luis Ariel <span>Casabene</span>
            </a>
            <div class="d-flex gap-3 align-items-center">
                <a href="<?= base_url('/') ?>" class="nav-link-custom">Inicio</a>
                <a href="<?= base_url('blog') ?>" class="nav-link-custom" style="color: var(--accent);">Blog</a>
                <a href="<?= base_url('estudio/login') ?>" class="nav-link-custom">Ingresar</a>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="blog-hero">
        <div class="container">
            <h1><i class="bi bi-church me-2"></i>Blog <span>Entidades Religiosas</span></h1>
            <p>Informacion, novedades y guias sobre gestion contable e impositiva para iglesias y entidades evangelicas.</p>
        </div>
    </section>

    <!-- Posts -->
    <section style="padding: 3rem 0;">
        <div class="container">
            <?php if (empty($posts)): ?>
                <div class="empty-state">
                    <i class="bi bi-journal-text"></i>
                    <h3>Proximamente</h3>
                    <p class="text-muted">Estamos preparando contenido de valor para entidades religiosas. Vuelva pronto.</p>
                    <a href="<?= base_url('/') ?>" class="btn btn-outline-primary mt-3">Volver al inicio</a>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($posts as $post): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="post-card">
                            <div class="post-card-img">
                                <?php if ($post['imagen_url']): ?>
                                    <img src="<?= e($post['imagen_url']) ?>" alt="<?= e($post['titulo']) ?>">
                                <?php else: ?>
                                    <i class="bi bi-journal-bookmark-fill"></i>
                                <?php endif; ?>
                            </div>
                            <div class="post-card-body">
                                <h3><a href="<?= base_url('blog/' . e($post['slug'])) ?>"><?= e($post['titulo']) ?></a></h3>
                                <p><?= e($post['resumen'] ?? mb_substr(strip_tags($post['contenido']), 0, 150) . '...') ?></p>
                                <div class="post-meta d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y', strtotime($post['created_at'])) ?></span>
                                    <a href="<?= base_url('blog/' . e($post['slug'])) ?>" class="btn-read">
                                        Leer mas <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                <nav class="mt-5 d-flex justify-content-center">
                    <ul class="pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="<?= base_url('blog?page=' . $i) ?>"><?= $i ?></a>
                        </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="blog-footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> Luis Ariel Casabene. <a href="<?= base_url('/') ?>">Volver al inicio</a></p>
        </div>
    </footer>

</body>
</html>
