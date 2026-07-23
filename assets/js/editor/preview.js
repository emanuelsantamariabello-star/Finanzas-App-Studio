document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('[data-post-editor]');

    if (!form) {
        return;
    }

    const canvas = form.querySelector('[data-post-canvas]');
    const scaleBox = form.querySelector('[data-preview-scale-box]');
    const templateSelect = form.querySelector('[data-preview-field="template_id"]');
    const formatSelect = form.querySelector('[data-preview-field="format"]');
    const uploadInput = form.querySelector('[data-preview-image]');
    const libraryImagePathInput = form.querySelector('[data-library-image-path]');
    const uploadedImage = form.querySelector('[data-preview-upload]');
    const imageFrame = form.querySelector('.post-image-frame');
    const formatInfo = form.querySelector('[data-format-info]');
    const existingImageUrl = form.querySelector('[data-existing-image-url]')?.value || '';
    const fitButton = form.querySelector('[data-preview-fit]');
    const clearImageButton = form.querySelector('[data-clear-image]');
    const localImageButton = document.querySelector('[data-local-image-button]');
    const selectedImageLabel = form.querySelector('[data-selected-image-label]');
    const imageAdjustControls = form.querySelector('[data-image-adjust-controls]');
    const imageWidthInput = form.querySelector('[data-image-width]');
    const imageHeightInput = form.querySelector('[data-image-height]');
    const imageWidthLabel = form.querySelector('[data-image-width-label]');
    const imageHeightLabel = form.querySelector('[data-image-height-label]');
    let selectedImageUrl = '';

    const nodes = {
        title: form.querySelector('[data-preview-title]'),
        subtitle: form.querySelector('[data-preview-subtitle]'),
        description: form.querySelector('[data-preview-description]'),
        cta: form.querySelector('[data-preview-cta]'),
        version: form.querySelector('[data-preview-version]'),
        label: form.querySelector('[data-preview-label]'),
    };

    const getTemplateSlug = () => templateSelect.selectedOptions[0]?.dataset.templateSlug || 'nueva-funcionalidad';

    const getValue = (field, fallback = '') => {
        const input = form.querySelector(`[data-preview-field="${field}"]`);
        const value = input?.value.trim() || '';
        return value || fallback;
    };

    const syncCounters = () => {
        form.querySelectorAll('[data-counter-for]').forEach((counter) => {
            const field = counter.getAttribute('data-counter-for');
            const input = form.querySelector(`[name="${field}"]`);
            const max = input?.getAttribute('maxlength') || '0';
            counter.textContent = `${input?.value.length || 0}/${max}`;
        });
    };

    const fitPreview = () => {
        const stage = form.querySelector('.preview-stage');
        const width = Number(formatSelect.selectedOptions[0]?.dataset.width || 1080);
        const height = Number(formatSelect.selectedOptions[0]?.dataset.height || 1080);
        const availableWidth = Math.max(stage.clientWidth - 48, 1);
        const availableHeight = Math.max(stage.clientHeight - 48, 1);
        const scale = Math.min(availableWidth / width, availableHeight / height, 1);
        const safeScale = Math.max(scale, 0.18);

        canvas.style.transform = `scale(${safeScale})`;
        scaleBox.style.width = `${width * safeScale}px`;
        scaleBox.style.height = `${height * safeScale}px`;
        scaleBox.dataset.previewScale = String(safeScale);
    };

    const hasPendingImage = () => (uploadInput.files && uploadInput.files.length > 0) || libraryImagePathInput.value !== '';

    const updateSelectedImageLabel = (label = '') => {
        if (!selectedImageLabel) {
            return;
        }

        if (label) {
            selectedImageLabel.textContent = label;
            return;
        }

        selectedImageLabel.textContent = existingImageUrl
            ? 'Imagen actual cargada.'
            : 'Selecciona una imagen desde la biblioteca o desde tu equipo.';
    };

    const updateImageControls = () => {
        const isAdviceTemplate = getTemplateSlug() === 'consejo-financiero';
        const hasImage = imageFrame.classList.contains('has-image');

        if (clearImageButton) {
            clearImageButton.hidden = !hasPendingImage();
        }

        if (imageAdjustControls) {
            imageAdjustControls.hidden = !(isAdviceTemplate && hasImage);
        }
    };

    const applyAdviceImageSize = () => {
        const width = Number(imageWidthInput?.value || 320);
        const height = Number(imageHeightInput?.value || 320);

        imageFrame.style.setProperty('--advice-image-width', `${width}px`);
        imageFrame.style.setProperty('--advice-image-height', `${height}px`);

        if (imageWidthLabel) {
            imageWidthLabel.textContent = `${width}px`;
        }

        if (imageHeightLabel) {
            imageHeightLabel.textContent = `${height}px`;
        }
    };

    const applyImage = (src) => {
        if (!src) {
            uploadedImage.removeAttribute('src');
            imageFrame.classList.remove('has-image');
            updateImageControls();
            return;
        }

        uploadedImage.src = src;
        imageFrame.classList.add('has-image');
        applyAdviceImageSize();
        updateImageControls();
    };

    const update = () => {
        const slug = getTemplateSlug();
        const template = window.FinanzasTemplates[slug] || window.FinanzasTemplates['nueva-funcionalidad'];
        const format = formatSelect.value;
        const width = Number(formatSelect.selectedOptions[0]?.dataset.width || 1080);
        const height = Number(formatSelect.selectedOptions[0]?.dataset.height || 1080);
        const cta = getValue('cta_text', template.defaultCta);
        const version = getValue('version_label');

        canvas.className = `post-canvas template-${slug}`;
        canvas.dataset.format = format;
        canvas.style.width = `${width}px`;
        canvas.style.height = `${height}px`;
        canvas.style.marginBottom = '0';
        nodes.label.textContent = template.label;
        nodes.title.textContent = getValue('title', 'Titulo de la publicacion');
        nodes.subtitle.textContent = getValue('subtitle');
        nodes.description.textContent = getValue('description', 'Describe el beneficio principal para el usuario.');
        nodes.cta.textContent = cta;
        nodes.cta.style.display = cta ? 'block' : 'none';
        nodes.version.textContent = version;
        nodes.version.style.display = version ? 'block' : 'none';
        formatInfo.textContent = `${width} x ${height}px`;

        form.querySelector('[data-field-row="subtitle"]').style.display = slug === 'consejo-financiero' ? 'none' : 'block';
        form.querySelector('[data-field-row="version_label"]').style.display = slug === 'actualizacion-de-version' || slug === 'nueva-funcionalidad' ? 'block' : 'none';

        syncCounters();
        applyAdviceImageSize();
        updateImageControls();
        fitPreview();
    };

    form.querySelectorAll('[data-preview-field]').forEach((input) => {
        input.addEventListener('input', update);
        input.addEventListener('change', update);
    });

    uploadInput.addEventListener('change', () => {
        const file = uploadInput.files?.[0];

        if (selectedImageUrl) {
            URL.revokeObjectURL(selectedImageUrl);
            selectedImageUrl = '';
        }

        libraryImagePathInput.value = '';

        if (file) {
            selectedImageUrl = URL.createObjectURL(file);
        }

        applyImage(selectedImageUrl || existingImageUrl);
        updateSelectedImageLabel(file ? `Archivo local: ${file.name}` : '');

        const modal = document.querySelector('#mediaLibraryModal');
        const modalInstance = modal && window.bootstrap ? window.bootstrap.Modal.getInstance(modal) : null;
        modalInstance?.hide();
    });

    clearImageButton?.addEventListener('click', () => {
        if (selectedImageUrl) {
            URL.revokeObjectURL(selectedImageUrl);
            selectedImageUrl = '';
        }

        uploadInput.value = '';
        libraryImagePathInput.value = '';
        applyImage(existingImageUrl);
        updateSelectedImageLabel();
    });

    localImageButton?.addEventListener('click', () => {
        uploadInput.click();
    });

    document.querySelectorAll('[data-media-path]').forEach((button) => {
        button.addEventListener('click', () => {
            if (selectedImageUrl) {
                URL.revokeObjectURL(selectedImageUrl);
                selectedImageUrl = '';
            }

            uploadInput.value = '';
            libraryImagePathInput.value = button.getAttribute('data-media-path') || '';
            applyImage(button.getAttribute('data-media-url') || existingImageUrl);
            updateSelectedImageLabel(`Biblioteca: ${button.getAttribute('data-media-name') || 'imagen seleccionada'}`);
        });
    });

    [imageWidthInput, imageHeightInput].forEach((input) => {
        input?.addEventListener('input', () => {
            applyAdviceImageSize();
            fitPreview();
        });
    });

    fitButton.addEventListener('click', fitPreview);
    window.addEventListener('resize', fitPreview);

    applyImage(existingImageUrl);
    update();
});
