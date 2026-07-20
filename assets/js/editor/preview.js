document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('[data-post-editor]');

    if (!form) {
        return;
    }

    const canvas = form.querySelector('[data-post-canvas]');
    const templateSelect = form.querySelector('[data-preview-field="template_id"]');
    const formatSelect = form.querySelector('[data-preview-field="format"]');
    const uploadInput = form.querySelector('[data-preview-image]');
    const uploadedImage = form.querySelector('[data-preview-upload]');
    const imageFrame = form.querySelector('.post-image-frame');
    const formatInfo = form.querySelector('[data-format-info]');
    const existingImageUrl = form.querySelector('[data-existing-image-url]')?.value || '';
    const fitButton = form.querySelector('[data-preview-fit]');

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
        const scale = Math.min((stage.clientWidth - 48) / width, (stage.clientHeight - 48) / height, 0.52);
        const safeScale = Math.max(scale, 0.18);
        canvas.style.transform = `scale(${safeScale})`;
        canvas.style.marginBottom = `${height * safeScale - height}px`;
    };

    const applyImage = (src) => {
        if (!src) {
            uploadedImage.removeAttribute('src');
            imageFrame.classList.remove('has-image');
            return;
        }

        uploadedImage.src = src;
        imageFrame.classList.add('has-image');
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
        fitPreview();
    };

    form.querySelectorAll('[data-preview-field]').forEach((input) => {
        input.addEventListener('input', update);
        input.addEventListener('change', update);
    });

    uploadInput.addEventListener('change', () => {
        const file = uploadInput.files?.[0];
        applyImage(file ? URL.createObjectURL(file) : existingImageUrl);
    });

    fitButton.addEventListener('click', fitPreview);
    window.addEventListener('resize', fitPreview);

    applyImage(existingImageUrl);
    update();
});
