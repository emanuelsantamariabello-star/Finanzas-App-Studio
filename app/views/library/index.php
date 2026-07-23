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

    <?php if ($files === []): ?>
        <div class="empty-state">
            <h3>No hay imagenes cargadas</h3>
            <p>Agrega imagenes desde este modulo o desde el editor de publicaciones.</p>
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
                        <?php if ($useCount === null): ?>
                            <span class="badge text-bg-warning">Uso no validado</span>
                        <?php elseif ((int) $useCount > 0): ?>
                            <span class="badge text-bg-light border"><?= (int) $useCount ?> usos</span>
                        <?php else: ?>
                            <span class="badge app-badge">Disponible</span>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-sm btn-outline-primary" href="<?= e((string) $file['url']) ?>" download>Descargar</a>
                        <form method="post" action="<?= e(url('/library/delete')) ?>" data-confirm="Eliminar esta imagen de la biblioteca?">
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
