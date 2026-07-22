document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('[data-post-editor]');

    if (!form) {
        return;
    }

    const button = form.querySelector('[data-export-button]');
    const canvasNode = form.querySelector('[data-post-canvas]');
    const scaleBox = form.querySelector('[data-preview-scale-box]');
    const exportUrl = form.querySelector('[data-export-url]')?.value || '';
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

    const slugify = (value) => value
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-|-$/g, '')
        .slice(0, 80);

    button?.addEventListener('click', async () => {
        if (!window.html2canvas || !exportUrl || !postId) {
            return;
        }

        button.disabled = true;
        button.textContent = 'Exportando...';

        try {
            await document.fonts.ready;
            await waitForImages();
            const originalTransform = canvasNode.style.transform;
            const originalMargin = canvasNode.style.marginBottom;
            const originalBoxWidth = scaleBox?.style.width || '';
            const originalBoxHeight = scaleBox?.style.height || '';
            let output;

            try {
                canvasNode.style.transform = 'none';
                canvasNode.style.marginBottom = '0';
                if (scaleBox) {
                    scaleBox.style.width = `${canvasNode.offsetWidth}px`;
                    scaleBox.style.height = `${canvasNode.offsetHeight}px`;
                }

                output = await window.html2canvas(canvasNode, {
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

            const title = form.querySelector('[name="title"]')?.value || 'publicacion';
            const date = new Date().toISOString().slice(0, 10);
            const blob = await new Promise((resolve) => output.toBlob(resolve, 'image/png'));

            if (!blob) {
                throw new Error('No se pudo generar el PNG.');
            }

            const formData = new FormData();
            formData.append('csrf_token', form.querySelector('[name="csrf_token"]')?.value || '');
            formData.append('id', postId);
            formData.append('png', blob, `${slugify(title)}-${date}.png`);

            const response = await window.fetch(exportUrl, {
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
});
