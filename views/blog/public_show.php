<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($post['titulo']) ?> | Blog - Estudio Contable Casabene</title>
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

        .blog-nav {
            background: var(--dark);
            padding: 1rem 0;
            border-bottom: 3px solid var(--accent);
        }
        .blog-nav .brand { color: #fff; font-weight: 700; font-size: 1.1rem; }
        .blog-nav .brand span { color: var(--accent); }
        .blog-nav a.nav-link-custom { color: rgba(255,255,255,0.7); font-size: 0.9rem; transition: color 0.3s; }
        .blog-nav a.nav-link-custom:hover { color: var(--accent); }

        .post-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--dark) 100%);
            color: #fff;
            padding: 3rem 0;
        }
        .post-header h1 { font-weight: 800; font-size: 2.2rem; line-height: 1.3; margin-bottom: 1rem; }
        .post-header .meta { opacity: 0.7; font-size: 0.9rem; }
        .post-header .meta i { color: var(--accent); }
        .breadcrumb-item a { color: var(--accent); }

        .post-content-wrapper {
            background: #fff;
            border-radius: 1rem;
            padding: 3rem;
            margin-top: -2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            position: relative;
            z-index: 2;
        }
        .post-content {
            font-size: 1.05rem;
            line-height: 1.9;
            color: #334155;
        }
        .post-content h2, .post-content h3 {
            color: var(--primary);
            font-weight: 700;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
        .post-content p { margin-bottom: 1.2rem; }
        .post-content ul, .post-content ol { margin-bottom: 1.2rem; padding-left: 1.5rem; }
        .post-content li { margin-bottom: 0.5rem; }
        .post-content blockquote {
            border-left: 4px solid var(--accent);
            padding: 1rem 1.5rem;
            background: #fffbeb;
            border-radius: 0 8px 8px 0;
            margin: 1.5rem 0;
            font-style: italic;
            color: #92400e;
        }

        /* Comments */
        .comments-section { margin-top: 3rem; }
        .comments-section h3 { font-weight: 700; color: var(--dark); margin-bottom: 1.5rem; }
        .comment-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            border-left: 3px solid var(--accent);
        }
        .comment-card .comment-author {
            font-weight: 700;
            color: var(--dark);
            font-size: 0.95rem;
        }
        .comment-card .comment-date {
            font-size: 0.8rem;
            color: var(--gray);
        }
        .comment-card .comment-text {
            margin-top: 0.5rem;
            font-size: 0.95rem;
            line-height: 1.7;
            color: #475569;
        }
        .comment-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
            min-width: 40px;
        }

        .comment-form-card {
            background: #fff;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }
        .comment-form-card h4 { font-weight: 700; color: var(--dark); margin-bottom: 1.5rem; }
        .form-control:focus { border-color: var(--primary-light); box-shadow: 0 0 0 0.2rem rgba(37,99,235,0.15); }
        .btn-comment {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 0.7rem 2rem;
            border-radius: 8px;
            font-weight: 600;
        }
        .btn-comment:hover { background: var(--primary-light); color: #fff; }

        .sidebar-card {
            background: #fff;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            margin-bottom: 1.5rem;
        }
        .sidebar-card h5 { font-weight: 700; font-size: 1rem; color: var(--dark); margin-bottom: 1rem; }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-light);
            font-weight: 600;
            font-size: 0.9rem;
        }
        .btn-back:hover { color: var(--accent); }

        .share-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--light);
            color: var(--primary);
            font-size: 1.1rem;
            transition: all 0.3s;
            border: 1px solid #e2e8f0;
        }
        .share-btn:hover { background: var(--primary); color: #fff; }

        .blog-footer {
            background: var(--dark);
            color: rgba(255,255,255,0.5);
            padding: 2rem 0;
            text-align: center;
            font-size: 0.9rem;
            margin-top: 4rem;
        }
        .blog-footer a { color: var(--accent); }

        @media (max-width: 768px) {
            .post-content-wrapper { padding: 1.5rem; }
            .post-header h1 { font-size: 1.6rem; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="blog-nav">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="<?= base_url('/') ?>" class="brand">
                <i class="bi bi-building"></i> Estudio <span>Casabene</span>
            </a>
            <div class="d-flex gap-3 align-items-center">
                <a href="<?= base_url('/') ?>" class="nav-link-custom">Inicio</a>
                <a href="<?= base_url('blog') ?>" class="nav-link-custom" style="color: var(--accent);">Blog</a>
                <a href="<?= base_url('estudio/login') ?>" class="nav-link-custom">Ingresar</a>
            </div>
        </div>
    </nav>

    <!-- Post Header -->
    <section class="post-header">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-3" style="font-size:0.85rem;">
                    <li class="breadcrumb-item"><a href="<?= base_url('blog') ?>">Blog</a></li>
                    <li class="breadcrumb-item active text-white-50"><?= e($post['titulo']) ?></li>
                </ol>
            </nav>
            <h1><?= e($post['titulo']) ?></h1>
            <div class="meta">
                <i class="bi bi-calendar3 me-1"></i> <?= date('d/m/Y', strtotime($post['created_at'])) ?>
                &nbsp;&bull;&nbsp;
                <i class="bi bi-chat-dots me-1"></i> <?= $commentCount ?> comentario<?= $commentCount !== 1 ? 's' : '' ?>
            </div>
        </div>
    </section>

    <!-- Content -->
    <section style="padding-bottom: 3rem;">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="post-content-wrapper">
                        <div class="post-content">
                            <?= $post['contenido'] ?>
                        </div>
                    </div>

                    <!-- Comments -->
                    <div class="comments-section" id="comentarios">
                        <h3><i class="bi bi-chat-dots me-2"></i>Comentarios (<?= $commentCount ?>)</h3>

                        <?php if ($msg = get_flash('blog_success')): ?>
                            <div class="alert alert-success py-2"><i class="bi bi-check-circle me-1"></i> <?= e($msg) ?></div>
                        <?php endif; ?>
                        <?php if ($msg = get_flash('blog_error')): ?>
                            <div class="alert alert-danger py-2"><i class="bi bi-exclamation-triangle me-1"></i> <?= e($msg) ?></div>
                        <?php endif; ?>

                        <?php if (!empty($comments)): ?>
                            <?php foreach ($comments as $comment): ?>
                            <div class="comment-card">
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="comment-avatar">
                                        <?= mb_strtoupper(mb_substr($comment['nombre'], 0, 1)) ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="comment-author"><?= e($comment['nombre']) ?></span>
                                            <span class="comment-date"><?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?></span>
                                        </div>
                                        <div class="comment-text"><?= nl2br(e($comment['comentario'])) ?></div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Se el primero en dejar un comentario.</p>
                        <?php endif; ?>

                        <!-- Comment Form -->
                        <div class="comment-form-card mt-4">
                            <h4><i class="bi bi-pencil-square me-2"></i>Dejar un comentario</h4>
                            <form method="POST" action="<?= base_url('blog/' . e($post['slug']) . '/comentar') ?>">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nombre *</label>
                                        <input type="text" name="nombre" class="form-control" required placeholder="Tu nombre">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Email <small class="text-muted">(opcional, no se publica)</small></label>
                                        <input type="email" name="email" class="form-control" placeholder="tu@email.com">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Comentario *</label>
                                        <textarea name="comentario" class="form-control" rows="4" required placeholder="Escribi tu comentario o consulta..."></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn-comment">
                                            <i class="bi bi-send me-1"></i> Publicar Comentario
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="sidebar-card">
                        <h5><i class="bi bi-arrow-left-circle me-1"></i> Navegacion</h5>
                        <a href="<?= base_url('blog') ?>" class="btn-back">
                            <i class="bi bi-arrow-left"></i> Volver al Blog
                        </a>
                    </div>
                    <div class="sidebar-card">
                        <h5><i class="bi bi-share me-1"></i> Compartir</h5>
                        <div class="d-flex gap-2">
                            <a href="https://wa.me/?text=<?= urlencode($post['titulo'] . ' - ' . base_url('blog/' . $post['slug'])) ?>" target="_blank" class="share-btn" title="WhatsApp">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(base_url('blog/' . $post['slug'])) ?>" target="_blank" class="share-btn" title="Facebook">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?text=<?= urlencode($post['titulo']) ?>&url=<?= urlencode(base_url('blog/' . $post['slug'])) ?>" target="_blank" class="share-btn" title="Twitter">
                                <i class="bi bi-twitter-x"></i>
                            </a>
                        </div>
                    </div>
                    <div class="sidebar-card" style="background: linear-gradient(135deg, var(--primary), var(--dark)); color: #fff;">
                        <h5 style="color: var(--accent);"><i class="bi bi-telephone me-1"></i> Consultas</h5>
                        <p style="font-size:0.9rem; opacity:0.8;">Necesita asesoramiento para su entidad religiosa?</p>
                        <a href="https://wa.me/5492995743759" target="_blank" class="btn btn-sm w-100" style="background: var(--accent); color: var(--dark); font-weight: 600;">
                            <i class="bi bi-whatsapp me-1"></i> Consultar por WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="blog-footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> Estudio Contable Casabene. <a href="<?= base_url('/') ?>">Volver al inicio</a></p>
        </div>
    </footer>

</body>
</html>
