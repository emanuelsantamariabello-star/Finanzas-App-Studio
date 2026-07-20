document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('[data-sidebar]');
    const toggle = document.querySelector('[data-sidebar-toggle]');

    if (sidebar && toggle) {
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('is-open');
        });
    }

    document.querySelectorAll('[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.getAttribute('data-confirm') || 'Confirmar accion?')) {
                event.preventDefault();
            }
        });
    });
});
