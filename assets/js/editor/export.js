document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('[data-post-editor]');

    if (!form) {
        return;
    }

    const button = form.querySelector('[data-export-button]');
    const packButton = document.querySelector('[data-export-pack-button]');
    const packStatus = document.querySelector('[data-export-pack-status]');
    const canvasNode = form.querySelector('[data-post-canvas]');
    const scaleBox = form.querySelector('[data-preview-scale-box]');
    const formatSelect = form.querySelector('[data-preview-field="format"]');
    const exportUrl = form.querySelector('[data-export-url]')?.value || '';
    const exportPackUrl = form.querySelector('[data-export-pack-url]')?.value || '';
    const postId = form.querySelector('[data-post-id]')?.value || '';

    const waitForImages = async () => {
        const images = Array.from(canvasNode.querySelectorAll('img')).filter((image) => image.src);
        await Promise.all(images.map((image) => {
            if (image.complete && image.naturalWidth > 0 && image.naturalHeight > 0) {
                return Promise.resolve();
            }

            return new Promise((resolve) => {
                image.addEventListener('load', resolve, { once: true });
                image.addEventListener('error', resolve, { once: true });
            });
        }));
    };

    const waitForPreviewUpdate = () => new Promise((resolve) => {
        window.requestAnimationFrame(() => window.requestAnimationFrame(resolve));
    });

    const slugify = (value) => value
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-|-$/g, '')
        .slice(0, 80);

    const renderCanvas = async () => {
        await document.fonts.ready;
        await waitForImages();

        const originalTransform = canvasNode.style.transform;
        const originalMargin = canvasNode.style.marginBottom;
        const originalBoxWidth = scaleBox?.style.width || '';
        const originalBoxHeight = scaleBox?.style.height || '';

        try {
            canvasNode.style.transform = 'none';
            canvasNode.style.marginBottom = '0';
            if (scaleBox) {
                scaleBox.style.width = `${canvasNode.offsetWidth}px`;
                scaleBox.style.height = `${canvasNode.offsetHeight}px`;
            }

            return await window.html2canvas(canvasNode, {
                backgroundColor: null,
                scale: 1,
                useCORS: true,
                logging: false,
            });
        } finally {
            canvasNode.style.transform = originalTransform;
            canvasNode.style.marginBottom = originalMargin;
            if (scaleBox) {
                scaleBox.style.width = originalBoxWidth;
                scaleBox.style.height = originalBoxHeight;
            }
        }
    };

    const uploadCanvas = async (url, blob, format = '') => {
        const title = form.querySelector('[name="title"]')?.value || 'publicacion';
        const date = new Date().toISOString().slice(0, 10);
        const formData = new FormData();

        formData.append('csrf_token', form.querySelector('[name="csrf_token"]')?.value || '');
        formData.append('id', postId);
        formData.append('png', blob, `${slugify(title)}-${format || 'export'}-${date}.png`);

        if (format) {
            formData.append('format', format);
        }

        const response = await window.fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
        const result = await response.json();

        if (!response.ok || !result.ok) {
            throw new Error(result.message || 'No fue posible guardar la exportacion.');
        }

        return result;
    };

    const exportCurrentCanvas = async (url, format = '') => {
        const output = await renderCanvas();
        const blob = await new Promise((resolve) => output.toBlob(resolve, 'image/png'));

        if (!blob) {
            throw new Error('No se pudo generar el PNG.');
        }

        return uploadCanvas(url, blob, format);
    };

    button?.addEventListener('click', async () => {
        if (!window.html2canvas || !exportUrl || !postId) {
            return;
        }

        button.disabled = true;
        button.textContent = 'Exportando...';

        try {
            const result = await exportCurrentCanvas(exportUrl);
            const link = document.createElement('a');

            link.download = result.file_path.split('/').pop();
            link.href = result.download_url;
            link.click();
            window.location.reload();
        } catch (error) {
            button.disabled = false;
            button.textContent = 'Exportar PNG';
            window.alert('No fue posible exportar el PNG.');
        }
    });

    packButton?.addEventListener('click', async () => {
        if (!window.html2canvas || !exportPackUrl || !postId || !formatSelect) {
            return;
        }

        const selectedFormats = Array.from(document.querySelectorAll('[data-export-pack-format]:checked'))
            .map((input) => input.value);

        if (selectedFormats.length === 0) {
            if (packStatus) {
                packStatus.hidden = false;
                packStatus.textContent = 'Selecciona al menos un formato.';
            }
            return;
        }

        const originalFormat = formatSelect.value;
        packButton.disabled = true;
        packButton.textContent = 'Generando...';

        if (packStatus) {
            packStatus.hidden = false;
        }

        try {
            for (let index = 0; index < selectedFormats.length; index += 1) {
                const format = selectedFormats[index];
                formatSelect.value = format;
                formatSelect.dispatchEvent(new Event('change', { bubbles: true }));
                await waitForPreviewUpdate();

                if (packStatus) {
                    packStatus.textContent = `Generando ${index + 1} de ${selectedFormats.length}...`;
                }

                await exportCurrentCanvas(exportPackUrl, format);
            }

            formatSelect.value = originalFormat;
            formatSelect.dispatchEvent(new Event('change', { bubbles: true }));

            if (packStatus) {
                packStatus.textContent = 'Pack generado. Los archivos quedaron en Exportaciones.';
            }

            window.setTimeout(() => window.location.reload(), 900);
        } catch (error) {
            formatSelect.value = originalFormat;
            formatSelect.dispatchEvent(new Event('change', { bubbles: true }));
            if (packStatus) {
                packStatus.textContent = 'No fue posible generar el pack completo.';
            }
            packButton.disabled = false;
            packButton.textContent = 'Generar pack';
        }
    });
});
