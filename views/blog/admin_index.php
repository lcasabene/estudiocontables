<?php $pageTitle = 'Blog - Artículos'; ob_start(); ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-journal-text"></i> Artículos del Blog</span>
        <a href="<?= tenant_url('blog/crear') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Nuevo Artículo
        </a>
    </div>
    <div class="card-body">
        <?php if (empty($posts)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-journal-text display-4 d-block mb-3" style="opacity:0.3"></i>
                <p>No hay artículos creados todavía.</p>
                <a href="<?= tenant_url('blog/crear') ?>" class="btn btn-primary btn-sm mt-2">
                    <i class="bi bi-plus-lg"></i> Crear primer artículo
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Título</th>
                            <th>Estado</th>
                            <th>Comentarios</th>
                            <th>Votos</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                        <tr>
                            <td>
                                <strong><?= e($post['titulo']) ?></strong>
                                <br><small class="text-muted">/blog/<?= e($post['slug']) ?></small>
                            </td>
                            <td>
                                <?php if ($post['publicado']): ?>
                                    <span class="badge bg-success">Publicado</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Borrador</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-info"><?= $post['comment_count'] ?></span>
                            </td>
                            <td>
                                <span class="text-success fw-semibold"><i class="bi bi-hand-thumbs-up-fill"></i> <?= $post['likes'] ?></span>
                                <span class="text-danger fw-semibold ms-2"><i class="bi bi-hand-thumbs-down-fill"></i> <?= $post['dislikes'] ?></span>
                            </td>
                            <td class="small"><?= date('d/m/Y H:i', strtotime($post['created_at'])) ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <?php if ($post['publicado']): ?>
                                    <a href="<?= base_url('blog/' . e($post['slug'])) ?>" target="_blank" class="btn btn-outline-info" title="Ver en blog">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php endif; ?>
                                    <a href="<?= tenant_url("blog/{$post['id']}/editar") ?>" class="btn btn-outline-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="<?= tenant_url("blog/{$post['id']}/delete") ?>" 
                                          style="display:inline" onsubmit="return confirm('¿Eliminar este artículo y todos sus comentarios?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
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

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
