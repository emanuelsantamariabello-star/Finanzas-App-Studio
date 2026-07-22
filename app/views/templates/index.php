<?php declare(strict_types=1); ?>
<section class="dashboard-header mb-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <div>
            <p class="section-kicker mb-2">Catalogo</p>
            <h2 class="mb-2">Plantillas</h2>
            <p class="text-muted mb-0">Plantillas bloqueadas disponibles para generar publicaciones consistentes.</p>
        </div>
        <a class="btn btn-primary" href="<?= e(url('/posts/create')) ?>">Usar plantilla</a>
    </div>
</section>

<?php if (!$databaseAvailable): ?>
    <div class="alert alert-warning">La base de datos no esta disponible. No se pueden listar plantillas.</div>
<?php endif; ?>

<section class="row g-4">
    <?php foreach ($templates as $template): ?>
        <?php $meta = $templateMeta[$template['slug']] ?? ['goal' => $template['description'], 'fields' => []]; ?>
        <div class="col-12 col-xl-4">
            <article class="template-card">
                <div class="template-preview template-preview-<?= e($template['slug']) ?>">
                    <span><?= e($template['name']) ?></span>
                </div>
                <div class="template-card-body">
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <div>
                            <h3><?= e($template['name']) ?></h3>
                            <p><?= e($meta['goal']) ?></p>
                        </div>
                        <span class="badge text-bg-light border"><?= (int) $template['posts_count'] ?> posts</span>
                    </div>
                    <div class="template-fields">
                        <?php foreach ($meta['fields'] as $field): ?>
                            <span><?= e($field) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <a class="btn btn-sm btn-outline-primary mt-3" href="<?= e(url('/posts/create')) ?>">Crear publicacion</a>
                </div>
            </article>
        </div>
    <?php endforeach; ?>
</section>

<section class="panel mt-4">
    <div class="panel-header">
        <h3>Formatos disponibles</h3>
    </div>
    <div class="format-grid">
        <?php foreach ($formats as $format): ?>
            <div>
                <strong><?= e($format['label']) ?></strong>
                <span><?= (int) $format['width'] ?> x <?= (int) $format['height'] ?> px</span>
            </div>
        <?php endforeach; ?>
    </div>
</section>
