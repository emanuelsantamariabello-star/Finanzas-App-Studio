document.addEventListener('DOMContentLoaded', () => {
    const shell = document.querySelector('[data-app-shell]');
    const sidebar = document.querySelector('[data-sidebar]');
    const toggle = document.querySelector('[data-sidebar-toggle]');
    const collapse = document.querySelector('[data-sidebar-collapse]');
    const collapseIcon = document.querySelector('[data-sidebar-collapse-icon]');
    const collapsedStorageKey = 'finanzasApp.sidebarCollapsed';

    const readSidebarState = () => {
        try {
            return window.localStorage.getItem(collapsedStorageKey) === 'true';
        } catch (error) {
            return false;
        }
    };

    const writeSidebarState = (isCollapsed) => {
        try {
            window.localStorage.setItem(collapsedStorageKey, String(isCollapsed));
        } catch (error) {
            return;
        }
    };

    if (sidebar && toggle) {
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('is-open');
        });
    }

    if (shell && collapse) {
        const setCollapsedState = (isCollapsed) => {
            shell.classList.toggle('is-sidebar-collapsed', isCollapsed);
            collapse.setAttribute('aria-expanded', String(!isCollapsed));
            collapse.setAttribute('aria-label', isCollapsed ? 'Expandir menu' : 'Contraer menu');

            if (collapseIcon) {
                collapseIcon.textContent = isCollapsed ? '>' : '<';
            }

            writeSidebarState(isCollapsed);
        };

        setCollapsedState(readSidebarState());

        collapse.addEventListener('click', () => {
            setCollapsedState(!shell.classList.contains('is-sidebar-collapsed'));
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
