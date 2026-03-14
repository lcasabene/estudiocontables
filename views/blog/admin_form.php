<?php
$isEdit = $post !== null;
$pageTitle = $isEdit ? 'Editar Artículo' : 'Nuevo Artículo';
ob_start();
?>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-<?= $isEdit ? 'pencil' : 'plus-lg' ?>"></i> <?= $pageTitle ?>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= $isEdit ? tenant_url("blog/{$post['id']}/update") : tenant_url('blog/store') ?>">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Título *</label>
                        <input type="text" name="titulo" class="form-control" required
                               value="<?= e($post['titulo'] ?? '') ?>" id="inputTitulo">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Slug (URL)</label>
                        <div class="input-group">
                            <span class="input-group-text">/blog/</span>
                            <input type="text" name="slug" class="form-control" id="inputSlug"
                                   value="<?= e($post['slug'] ?? '') ?>" placeholder="se-genera-automaticamente">
                        </div>
                        <small class="text-muted">Dejar vacío para generar automáticamente desde el título.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Resumen</label>
                        <textarea name="resumen" class="form-control" rows="2" placeholder="Breve descripción del artículo (aparece en el listado)"><?= e($post['resumen'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Contenido *</label>
                        <textarea name="contenido" class="form-control" rows="15" required id="inputContenido"><?= e($post['contenido'] ?? '') ?></textarea>
                        <small class="text-muted">Puede usar HTML para dar formato: &lt;h2&gt;, &lt;h3&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;blockquote&gt;, &lt;strong&gt;, &lt;em&gt;</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">URL Imagen (opcional)</label>
                        <input type="url" name="imagen_url" class="form-control" placeholder="https://ejemplo.com/imagen.jpg"
                               value="<?= e($post['imagen_url'] ?? '') ?>">
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input type="checkbox" name="publicado" class="form-check-input" id="checkPublicado" 
                               <?= ($post['publicado'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="checkPublicado">Publicado</label>
                        <br><small class="text-muted">Si está desactivado, el artículo se guarda como borrador.</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> <?= $isEdit ? 'Actualizar' : 'Guardar' ?>
                        </button>
                        <a href="<?= tenant_url('blog') ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-info-circle"></i> Ayuda</div>
            <div class="card-body small text-muted">
                <?php if ($isEdit): ?>
                    <p class="mb-1">Creado: <?= format_datetime($post['created_at']) ?></p>
                    <p class="mb-3">Actualizado: <?= format_datetime($post['updated_at']) ?></p>
                    <?php if ($post['publicado']): ?>
                    <a href="<?= base_url('blog/' . e($post['slug'])) ?>" target="_blank" class="btn btn-outline-info btn-sm w-100 mb-2">
                        <i class="bi bi-eye"></i> Ver en el blog
                    </a>
                    <?php endif; ?>
                <?php endif; ?>
                <hr>
                <p class="fw-semibold text-dark">Formato HTML disponible:</p>
                <ul class="ps-3">
                    <li><code>&lt;h2&gt;Subtítulo&lt;/h2&gt;</code></li>
                    <li><code>&lt;h3&gt;Subtítulo menor&lt;/h3&gt;</code></li>
                    <li><code>&lt;p&gt;Párrafo&lt;/p&gt;</code></li>
                    <li><code>&lt;strong&gt;Negrita&lt;/strong&gt;</code></li>
                    <li><code>&lt;em&gt;Cursiva&lt;/em&gt;</code></li>
                    <li><code>&lt;ul&gt;&lt;li&gt;Lista&lt;/li&gt;&lt;/ul&gt;</code></li>
                    <li><code>&lt;blockquote&gt;Cita&lt;/blockquote&gt;</code></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<script>
document.getElementById('inputTitulo').addEventListener('input', function() {
    let slugInput = document.getElementById('inputSlug');
    if (slugInput.value === '' || slugInput.dataset.auto === '1') {
        let slug = this.value.toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/ñ/g, 'n')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-|-$/g, '');
        slugInput.value = slug;
        slugInput.dataset.auto = '1';
    }
});
document.getElementById('inputSlug').addEventListener('input', function() {
    this.dataset.auto = '0';
});
</script>
<?php $extraJs = ob_get_clean(); ?>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
