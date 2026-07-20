document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('[data-post-editor]');

    if (!form) {
        return;
    }

    const button = form.querySelector('[data-export-button]');
    const canvasNode = form.querySelector('[data-post-canvas]');
    const registerUrl = form.querySelector('[data-export-register-url]')?.value || '';

    const waitForImages = async () => {
        const images = Array.from(canvasNode.querySelectorAll('img')).filter((image) => image.src);
        await Promise.all(images.map((image) => {
            if (image.complete) {
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
        if (!window.html2canvas || !registerUrl) {
            return;
        }

        button.disabled = true;
        button.textContent = 'Exportando...';

        try {
            await document.fonts.ready;
            await waitForImages();
            const originalTransform = canvasNode.style.transform;
            const originalMargin = canvasNode.style.marginBottom;
            canvasNode.style.transform = 'none';
            canvasNode.style.marginBottom = '0';

            const output = await window.html2canvas(canvasNode, {
                backgroundColor: null,
                scale: 1,
                useCORS: true,
                logging: false,
            });

            canvasNode.style.transform = originalTransform;
            canvasNode.style.marginBottom = originalMargin;

            const title = form.querySelector('[name="title"]')?.value || 'publicacion';
            const date = new Date().toISOString().slice(0, 10);
            const link = document.createElement('a');
            link.download = `finanzas-app-${slugify(title)}-${date}.png`;
            link.href = output.toDataURL('image/png');
            link.click();
            window.location.href = registerUrl;
        } catch (error) {
            button.disabled = false;
            button.textContent = 'Exportar PNG';
            window.alert('No fue posible exportar el PNG.');
        }
    });
});
