<?php declare(strict_types=1); ?>
<section class="dashboard-header mb-4">
    <div>
        <p class="section-kicker mb-2">Historial</p>
        <h2 class="mb-2">Exportaciones</h2>
        <p class="text-muted mb-0">Consulta, filtra y descarga los PNG generados desde publicaciones.</p>
    </div>
</section>

<?php if (!$databaseAvailable): ?>
    <div class="alert alert-warning">La base de datos no esta disponible. No se pueden listar exportaciones.</div>
<?php endif; ?>

<section class="panel">
    <form class="row g-3 align-items-end mb-3" method="get" action="<?= e(url('/exports')) ?>">
        <div class="col-12 col-md-4">
            <label class="form-label" for="template_id">Plantilla</label>
            <select class="form-select" id="template_id" name="template_id">
                <option value="">Todas</option>
                <?php foreach ($templates as $template): ?>
                    <option value="<?= (int) $template['id'] ?>" <?= $selectedTemplateId === (int) $template['id'] ? 'selected' : '' ?>>
                        <?= e($template['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12 col-md-4">
            <label class="form-label" for="format">Formato</label>
            <select class="form-select" id="format" name="format">
                <option value="">Todos</option>
                <?php foreach ($formats as $key => $format): ?>
                    <option value="<?= e($key) ?>" <?= $selectedFormat === $key ? 'selected' : '' ?>>
                        <?= e($format['label']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12 col-md-auto">
            <button class="btn btn-outline-primary" type="submit">Filtrar</button>
        </div>
    </form>

    <?php if ($exports === []): ?>
        <div class="empty-state">
            <h3>No hay exportaciones</h3>
            <p>Cuando exportes una publicacion, el PNG guardado aparecera aqui.</p>
            <a class="btn btn-primary" href="<?= e(url('/posts')) ?>">Ver publicaciones</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Vista</th>
                        <th>Publicacion</th>
                        <th>Plantilla</th>
                        <th>Formato</th>
                        <th>Archivo</th>
                        <th>Fecha</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exports as $export): ?>
                        <?php $fileExists = is_file(APP_BASE_PATH . '/' . $export['file_path']); ?>
                        <tr>
                            <td>
                                <div class="list-preview-thumb" aria-label="Miniatura de exportacion">
                                    <?php if ($fileExists): ?>
                                        <img src="<?= e(url((string) $export['file_path'])) ?>" alt="<?= e($export['post_title']) ?>">
                                    <?php else: ?>
                                        <span>No disponible</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?= e($export['post_title']) ?></td>
                            <td><?= e($export['template_name'] ?? 'Sin plantilla') ?></td>
                            <td><?= e($formats[$export['format']]['label'] ?? $export['format']) ?></td>
                            <td><code><?= e(basename((string) $export['file_path'])) ?></code></td>
                            <td><?= e($export['exported_at']) ?></td>
                            <td class="text-end">
                                <?php if ($fileExists): ?>
                                    <a class="btn btn-sm btn-outline-primary" href="<?= e(url((string) $export['file_path'])) ?>" download>Descargar</a>
                                <?php else: ?>
                                    <span class="badge text-bg-warning">Archivo faltante</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
