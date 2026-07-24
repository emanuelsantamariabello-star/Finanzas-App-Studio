<?php declare(strict_types=1); ?>
<section class="dashboard-header mb-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <div>
            <p class="section-kicker mb-2">Recursos</p>
            <h2 class="mb-2">Biblioteca</h2>
            <p class="text-muted mb-0">Administra imagenes cargadas localmente para reutilizarlas en plantillas.</p>
        </div>
        <form class="library-upload" method="post" action="<?= e(url('/library/store')) ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <label class="btn btn-primary mb-0" for="library_image">Agregar imagen</label>
            <input class="d-none" type="file" id="library_image" name="image" accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp" data-auto-submit-file>
        </form>
    </div>
</section>

<?php if ($message = flash('success')): ?>
    <div class="alert alert-success"><?= e($message) ?></div>
<?php endif; ?>
<?php if ($message = flash('error')): ?>
    <div class="alert alert-danger"><?= e($message) ?></div>
<?php endif; ?>

<?php if (!$databaseAvailable): ?>
    <div class="alert alert-warning">La base de datos no esta disponible. La eliminacion queda bloqueada hasta validar el uso de las imagenes.</div>
<?php endif; ?>

<section class="panel">
    <div class="panel-header">
        <h3>Imagenes locales</h3>
    </div>

    <form class="row g-3 align-items-end mb-4" method="get" action="<?= e(url('/library')) ?>">
        <div class="col-12 col-lg-5">
            <label class="form-label" for="q">Buscar</label>
            <input class="form-control" id="q" name="q" value="<?= e($selectedSearch) ?>" placeholder="Nombre o etiqueta">
        </div>
        <div class="col-12 col-lg-4">
            <label class="form-label" for="tag">Etiqueta</label>
            <select class="form-select" id="tag" name="tag">
                <option value="">Todas</option>
                <?php foreach ($tags as $tag): ?>
                    <option value="<?= e((string) $tag) ?>" <?= $selectedTag === $tag ? 'selected' : '' ?>><?= e((string) $tag) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12 col-lg-auto">
            <button class="btn btn-outline-primary" type="submit">Filtrar</button>
            <a class="btn btn-outline-secondary" href="<?= e(url('/library')) ?>">Limpiar</a>
        </div>
    </form>

    <?php if ($files === []): ?>
        <div class="empty-state">
            <h3>No hay imagenes para mostrar</h3>
            <p>Agrega imagenes o limpia los filtros activos.</p>
        </div>
    <?php else: ?>
        <div class="media-library-grid media-library-page-grid">
            <?php foreach ($files as $file): ?>
                <?php $useCount = $file['use_count']; ?>
                <article class="media-library-card">
                    <a href="<?= e((string) $file['url']) ?>" target="_blank" rel="noopener">
                        <img src="<?= e((string) $file['url']) ?>" alt="<?= e((string) $file['name']) ?>">
                    </a>
                    <div class="media-library-card-body">
                        <strong title="<?= e((string) $file['name']) ?>"><?= e((string) $file['name']) ?></strong>
                        <span><?= e((string) $file['size_label']) ?> · <?= e((string) $file['updated_at']) ?></span>
                        <?php if ($file['tags'] !== []): ?>
                            <div class="media-tag-list">
                                <?php foreach ($file['tags'] as $tag): ?>
                                    <a class="badge text-bg-light border" href="<?= e(url('/library?tag=' . urlencode((string) $tag))) ?>"><?= e((string) $tag) ?></a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($useCount === null): ?>
                            <span class="badge text-bg-warning">Uso no validado</span>
                        <?php elseif ((int) $useCount > 0): ?>
                            <span class="badge text-bg-light border"><?= (int) $useCount ?> usos</span>
                        <?php else: ?>
                            <span class="badge app-badge">Disponible</span>
                        <?php endif; ?>
                    </div>
                    <form method="post" action="<?= e(url('/library/tags')) ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="path" value="<?= e((string) $file['path']) ?>">
                        <label class="form-label small" for="tags_<?= e(md5((string) $file['path'])) ?>">Etiquetas</label>
                        <div class="input-group input-group-sm">
                            <input class="form-control" id="tags_<?= e(md5((string) $file['path'])) ?>" name="tags" value="<?= e(implode(', ', $file['tags'])) ?>" placeholder="logo, captura">
                            <button class="btn btn-outline-primary" type="submit">Guardar</button>
                        </div>
                    </form>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-sm btn-outline-primary" href="<?= e((string) $file['url']) ?>" download>Descargar</a>
                        <form method="post" action="<?= e(url('/library/delete')) ?>" data-confirm-title="Eliminar imagen" data-confirm="Eliminar esta imagen de la biblioteca? Esta accion no se puede deshacer.">
                            <?= csrf_field() ?>
                            <input type="hidden" name="path" value="<?= e((string) $file['path']) ?>">
                            <button class="btn btn-sm btn-outline-danger" type="submit" <?= $useCount === null || (int) $useCount > 0 ? 'disabled' : '' ?>>Eliminar</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
