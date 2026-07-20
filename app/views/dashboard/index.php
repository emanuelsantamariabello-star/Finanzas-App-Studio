<?php declare(strict_types=1); ?>
<section class="dashboard-header mb-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <div>
        <p class="section-kicker mb-2">Contenido promocional</p>
        <h2 class="mb-2">Generador de publicaciones</h2>
        <p class="text-muted mb-0">Crea borradores con plantillas bloqueadas, preview en tiempo real y exportacion PNG.</p>
        </div>
        <a class="btn btn-primary" href="<?= e(url('/posts/create')) ?>">Nueva publicacion</a>
    </div>
</section>

<?php if (!$databaseAvailable): ?>
    <div class="alert alert-warning">La base de datos no esta disponible. Revisa la configuracion local y aplica las migraciones.</div>
<?php endif; ?>

<section class="row g-3 mb-4">
    <?php foreach ($stats as $stat): ?>
        <div class="col-12 col-md-4">
            <article class="metric-card">
                <span class="metric-label"><?= e($stat['label']) ?></span>
                <strong class="metric-value text-<?= e($stat['tone']) ?>"><?= e($stat['value']) ?></strong>
            </article>
        </div>
    <?php endforeach; ?>
</section>

<section class="row g-4">
    <div class="col-12 col-xl-7">
        <article class="panel">
            <div class="panel-header">
                <h3>Publicaciones recientes</h3>
            </div>
            <?php if ($recentPosts === []): ?>
                <p class="text-muted mb-0">Todavia no hay publicaciones creadas.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Titulo</th>
                                <th>Plantilla</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentPosts as $post): ?>
                                <tr>
                                    <td><a href="<?= e(url('/posts/edit?id=' . (int) $post['id'])) ?>"><?= e($post['title']) ?></a></td>
                                    <td><?= e($post['template_name'] ?? 'Sin plantilla') ?></td>
                                    <td><span class="badge text-bg-light border"><?= e($post['status']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </article>
    </div>

    <div class="col-12 col-xl-5">
        <article class="panel">
            <div class="panel-header">
                <h3>Flujo MVP</h3>
            </div>
            <div class="next-list">
                <span>Elegir plantilla</span>
                <span>Completar contenido</span>
                <span>Guardar borrador</span>
                <span>Exportar PNG</span>
            </div>
        </article>
    </div>
</section>
