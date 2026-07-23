<?php
declare(strict_types=1);

$value = static function (string $field, mixed $default = '') use ($old, $post): string {
    if (array_key_exists($field, $old)) {
        return (string) $old[$field];
    }

    return (string) ($post[$field] ?? $default);
};

$selectedTemplateId = (int) $value('template_id', $templates[0]['id'] ?? 0);
$selectedFormat = $value('format', 'instagram_square');
$imagePath = (string) ($post['image_path'] ?? '');
$action = $post === null ? '/posts/store' : '/posts/update';
$mediaLibrary = $mediaLibrary ?? [];
$content = json_decode((string) ($post['content_json'] ?? ''), true);
$content = is_array($content) ? $content : [];
$contentValue = static function (string $field, mixed $default = '') use ($old, $content): string {
    if (array_key_exists($field, $old)) {
        return (string) $old[$field];
    }

    return (string) ($content[$field] ?? $default);
};
?>

<?php if ($message = flash('success')): ?>
    <div class="alert alert-success"><?= e($message) ?></div>
<?php endif; ?>
<?php if ($message = flash('error')): ?>
    <div class="alert alert-danger"><?= e($message) ?></div>
<?php endif; ?>
<?php if (isset($errors['general'])): ?>
    <div class="alert alert-danger"><?= e($errors['general']) ?></div>
<?php endif; ?>

<form class="editor-grid" method="post" action="<?= e(url($action)) ?>" enctype="multipart/form-data" data-post-editor>
    <?= csrf_field() ?>
    <?php if ($post !== null): ?>
        <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">
    <?php endif; ?>
    <input type="hidden" data-logo-url value="<?= e(asset('images/branding/logo-finanzas-app.png')) ?>">
    <input type="hidden" data-existing-image-url value="<?= $imagePath !== '' ? e(url($imagePath)) : '' ?>">
    <input type="hidden" data-post-id value="<?= e((string) ($post['id'] ?? '')) ?>">
    <input type="hidden" data-export-url value="<?= $post !== null ? e(url('/posts/export')) : '' ?>">

    <section class="editor-panel">
        <div class="panel-header">
            <h3>Contenido</h3>
        </div>

        <div class="mb-3">
            <label class="form-label" for="template_id">Plantilla</label>
            <select class="form-select <?= isset($errors['template_id']) ? 'is-invalid' : '' ?>" id="template_id" name="template_id" data-preview-field="template_id" required>
                <?php foreach ($templates as $template): ?>
                    <option value="<?= (int) $template['id'] ?>" data-template-slug="<?= e($template['slug']) ?>" <?= $selectedTemplateId === (int) $template['id'] ? 'selected' : '' ?>>
                        <?= e($template['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['template_id'])): ?><div class="invalid-feedback"><?= e($errors['template_id']) ?></div><?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="format">Formato</label>
            <select class="form-select <?= isset($errors['format']) ? 'is-invalid' : '' ?>" id="format" name="format" data-preview-field="format" required>
                <?php foreach ($formats as $key => $format): ?>
                    <option value="<?= e($key) ?>" data-width="<?= (int) $format['width'] ?>" data-height="<?= (int) $format['height'] ?>" <?= $selectedFormat === $key ? 'selected' : '' ?>>
                        <?= e($format['label']) ?> (<?= (int) $format['width'] ?> x <?= (int) $format['height'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['format'])): ?><div class="invalid-feedback"><?= e($errors['format']) ?></div><?php endif; ?>
        </div>

        <?php
        $fields = [
            ['title', 'Titulo', 90, true],
            ['subtitle', 'Subtitulo', 130, false],
            ['description', 'Descripcion', 320, true],
            ['cta_text', 'Llamada a la accion', 60, false],
            ['version_label', 'Version o etiqueta', 30, false],
        ];
        ?>
        <?php foreach ($fields as [$field, $label, $max, $required]): ?>
            <div class="mb-3" data-field-row="<?= e($field) ?>">
                <label class="form-label" for="<?= e($field) ?>"><?= e($label) ?></label>
                <?php if ($field === 'description'): ?>
                    <textarea class="form-control <?= isset($errors[$field]) ? 'is-invalid' : '' ?>" id="<?= e($field) ?>" name="<?= e($field) ?>" maxlength="<?= (int) $max ?>" rows="4" data-preview-field="<?= e($field) ?>" <?= $required ? 'required' : '' ?>><?= e($value($field)) ?></textarea>
                <?php else: ?>
                    <input class="form-control <?= isset($errors[$field]) ? 'is-invalid' : '' ?>" id="<?= e($field) ?>" name="<?= e($field) ?>" maxlength="<?= (int) $max ?>" value="<?= e($value($field)) ?>" data-preview-field="<?= e($field) ?>" <?= $required ? 'required' : '' ?>>
                <?php endif; ?>
                <div class="d-flex justify-content-between mt-1">
                    <?php if (isset($errors[$field])): ?><div class="invalid-feedback d-block"><?= e($errors[$field]) ?></div><?php else: ?><span></span><?php endif; ?>
                    <small class="text-muted" data-counter-for="<?= e($field) ?>">0/<?= (int) $max ?></small>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="mb-4">
            <span class="form-label d-block">Captura o imagen</span>
            <input type="hidden" name="library_image_path" data-library-image-path>
            <input class="d-none <?= isset($errors['image']) ? 'is-invalid' : '' ?>" type="file" id="image" name="image" accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp" data-preview-image>
            <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#mediaLibraryModal">Elegir imagen</button>
                <button class="btn btn-outline-secondary btn-sm" type="button" data-clear-image hidden>Quitar seleccion</button>
            </div>
            <div class="form-text" data-selected-image-label><?= $imagePath !== '' ? 'Imagen actual: ' . e(basename($imagePath)) : 'Selecciona una imagen desde la biblioteca o desde tu equipo.' ?></div>
            <div class="image-adjust-controls mt-3" data-image-adjust-controls hidden>
                <p class="text-muted small mb-2">Ajuste manual para Consejo financiero</p>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label small" for="image_width">Ancho</label>
                        <input class="form-range" type="range" id="image_width" name="image_width" min="160" max="480" step="10" value="<?= e($contentValue('image_width', '320')) ?>" data-image-width>
                        <small class="text-muted" data-image-width-label><?= e($contentValue('image_width', '320')) ?>px</small>
                    </div>
                    <div class="col-6">
                        <label class="form-label small" for="image_height">Alto</label>
                        <input class="form-range" type="range" id="image_height" name="image_height" min="160" max="480" step="10" value="<?= e($contentValue('image_height', '320')) ?>" data-image-height>
                        <small class="text-muted" data-image-height-label><?= e($contentValue('image_height', '320')) ?>px</small>
                    </div>
                </div>
            </div>
            <?php if (isset($errors['image'])): ?><div class="invalid-feedback"><?= e($errors['image']) ?></div><?php endif; ?>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-primary" type="submit" data-save-button>Guardar borrador</button>
            <a class="btn btn-outline-secondary" href="<?= e(url('/posts')) ?>">Volver</a>
        </div>
    </section>

    <section class="preview-panel">
        <div class="preview-toolbar">
            <div>
                <strong>Vista previa</strong>
                <span class="text-muted" data-format-info></span>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary" type="button" data-preview-fit>Recentrar</button>
                <button class="btn btn-sm btn-success" type="button" data-export-button <?= $post === null ? 'disabled' : '' ?>>Exportar PNG</button>
            </div>
        </div>
        <div class="preview-stage">
            <div class="preview-scale-box" data-preview-scale-box>
                <article class="post-canvas" data-post-canvas>
                    <div class="post-bg-shape"></div>
                    <header class="post-header">
                        <div class="post-logo-frame">
                            <img data-preview-logo alt="Finanzas App" src="<?= e(asset('images/branding/logo-finanzas-app.png')) ?>">
                        </div>
                        <span data-preview-version></span>
                    </header>
                    <main class="post-content">
                        <span class="post-label" data-preview-label>NUEVA FUNCIONALIDAD</span>
                        <h2 data-preview-title><?= e($value('title', 'Titulo de la publicacion')) ?></h2>
                        <p class="post-subtitle" data-preview-subtitle></p>
                        <p class="post-description" data-preview-description></p>
                        <div class="post-visual">
                            <div class="post-image-frame">
                                <img data-preview-upload alt="">
                                <div class="post-image-placeholder">Vista del sistema</div>
                            </div>
                            <div class="post-css-icon"></div>
                        </div>
                        <strong class="post-cta" data-preview-cta></strong>
                    </main>
                    <footer class="post-footer">finanzasappsan.com</footer>
                </article>
            </div>
        </div>
    </section>
</form>

<div class="modal fade" id="mediaLibraryModal" tabindex="-1" aria-labelledby="mediaLibraryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content media-library-modal">
            <div class="modal-header">
                <div>
                    <p class="section-kicker mb-1">Biblioteca local</p>
                    <h2 class="modal-title h5" id="mediaLibraryModalLabel">Seleccionar imagen</h2>
                </div>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
                    <p class="text-muted mb-0">Elige una imagen cargada recientemente o sube una nueva desde tu equipo.</p>
                    <button class="btn btn-primary" type="button" data-local-image-button>Seleccionar del equipo</button>
                </div>

                <?php if ($mediaLibrary === []): ?>
                    <div class="empty-state py-4">
                        <h3 class="h6">Biblioteca vacia</h3>
                        <p class="text-muted mb-0">Cuando subas imagenes al sistema apareceran aqui para reutilizarlas.</p>
                    </div>
                <?php else: ?>
                    <div class="media-library-grid">
                        <?php foreach ($mediaLibrary as $media): ?>
                            <button
                                class="media-library-item"
                                type="button"
                                data-media-path="<?= e((string) $media['path']) ?>"
                                data-media-url="<?= e((string) $media['url']) ?>"
                                data-media-name="<?= e((string) $media['name']) ?>"
                                data-bs-dismiss="modal"
                            >
                                <img src="<?= e((string) $media['url']) ?>" alt="<?= e((string) $media['name']) ?>">
                                <span><?= e((string) $media['name']) ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="<?= e(asset('js/editor/templates.js')) ?>"></script>
<script src="<?= e(asset('js/editor/preview.js') . '?v=' . filemtime(APP_BASE_PATH . '/assets/js/editor/preview.js')) ?>"></script>
<script src="<?= e(asset('js/editor/export.js')) ?>"></script>
