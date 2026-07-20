<?php declare(strict_types=1); ?>
<section class="dashboard-header mb-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <div>
            <p class="section-kicker mb-2">Gestion</p>
            <h2 class="mb-2">Publicaciones</h2>
            <p class="text-muted mb-0">Borradores y exportaciones creadas desde plantillas bloqueadas.</p>
        </div>
        <a class="btn btn-primary" href="<?= e(url('/posts/create')) ?>">Nueva publicacion</a>
    </div>
</section>

<?php if ($message = flash('success')): ?>
    <div class="alert alert-success"><?= e($message) ?></div>
<?php endif; ?>
<?php if ($message = flash('error')): ?>
    <div class="alert alert-danger"><?= e($message) ?></div>
<?php endif; ?>
<?php if (!$databaseAvailable): ?>
    <div class="alert alert-warning">La base de datos no esta disponible. No se pueden listar publicaciones.</div>
<?php endif; ?>

<section class="panel">
    <form class="row g-3 align-items-end mb-3" method="get" action="<?= e(url('/posts')) ?>">
        <div class="col-12 col-md-5">
            <label class="form-label" for="template_filter">Filtrar por plantilla</label>
            <select class="form-select" id="template_filter" name="template_id">
                <option value="">Todas</option>
                <?php foreach ($templates as $template): ?>
                    <option value="<?= (int) $template['id'] ?>" <?= $selectedTemplateId === (int) $template['id'] ? 'selected' : '' ?>>
                        <?= e($template['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12 col-md-auto">
            <button class="btn btn-outline-primary" type="submit">Filtrar</button>
        </div>
    </form>

    <?php if ($posts === []): ?>
        <div class="empty-state">
            <h3>No hay publicaciones</h3>
            <p>Crea el primer borrador desde una plantilla prediseñada.</p>
            <a class="btn btn-primary" href="<?= e(url('/posts/create')) ?>">Nueva publicacion</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Titulo</th>
                        <th>Plantilla</th>
                        <th>Formato</th>
                        <th>Estado</th>
                        <th>Actualizado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td><?= e($post['title']) ?></td>
                            <td><?= e($post['template_name'] ?? 'Sin plantilla') ?></td>
                            <td><?= e(PostService::FORMATS[$post['format']]['label'] ?? $post['format']) ?></td>
                            <td><span class="badge <?= $post['status'] === 'exported' ? 'text-bg-success' : 'text-bg-light border' ?>"><?= e($post['status']) ?></span></td>
                            <td><?= e($post['updated_at'] ?? $post['created_at']) ?></td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a class="btn btn-sm btn-outline-primary" href="<?= e(url('/posts/edit?id=' . (int) $post['id'])) ?>">Editar</a>
                                    <form method="post" action="<?= e(url('/posts/duplicate')) ?>">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">
                                        <button class="btn btn-sm btn-outline-secondary" type="submit">Duplicar</button>
                                    </form>
                                    <form method="post" action="<?= e(url('/posts/delete')) ?>" data-confirm="Eliminar esta publicacion?">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
