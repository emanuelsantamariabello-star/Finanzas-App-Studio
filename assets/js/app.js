document.addEventListener('DOMContentLoaded', () => {
    const shell = document.querySelector('[data-app-shell]');
    const sidebar = document.querySelector('[data-sidebar]');
    const toggle = document.querySelector('[data-sidebar-toggle]');
    const collapse = document.querySelector('[data-sidebar-collapse]');
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
            collapse.setAttribute('title', isCollapsed ? 'Expandir menu' : 'Contraer menu');

            writeSidebarState(isCollapsed);
        };

        setCollapsedState(readSidebarState());

        collapse.addEventListener('click', () => {
            setCollapsedState(!shell.classList.contains('is-sidebar-collapsed'));
        });
    }

    const confirmModal = document.querySelector('[data-confirm-modal]');
    const confirmTitle = confirmModal?.querySelector('[data-confirm-modal-title]');
    const confirmMessage = confirmModal?.querySelector('[data-confirm-modal-message]');
    const confirmSubmit = confirmModal?.querySelector('[data-confirm-modal-submit]');
    let pendingConfirmForm = null;

    document.querySelectorAll('[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.confirmed === 'true') {
                delete form.dataset.confirmed;
                return;
            }

            event.preventDefault();
            pendingConfirmForm = form;

            if (confirmTitle) {
                confirmTitle.textContent = form.getAttribute('data-confirm-title') || 'Eliminar registro';
            }

            if (confirmMessage) {
                confirmMessage.textContent = form.getAttribute('data-confirm') || 'Esta accion no se puede deshacer.';
            }

            if (confirmModal && window.bootstrap) {
                window.bootstrap.Modal.getOrCreateInstance(confirmModal).show();
            }
        });
    });

    confirmSubmit?.addEventListener('click', () => {
        if (!pendingConfirmForm) {
            return;
        }

        pendingConfirmForm.dataset.confirmed = 'true';
        pendingConfirmForm.requestSubmit();
        pendingConfirmForm = null;
    });

    document.querySelectorAll('[data-auto-submit-file]').forEach((input) => {
        input.addEventListener('change', () => {
            if (input.files && input.files.length > 0) {
                input.form?.submit();
            }
        });
    });

    document.querySelectorAll('[data-duplicate-post]').forEach((button) => {
        button.addEventListener('click', () => {
            const postId = document.querySelector('[data-duplicate-post-id]');
            const postTitle = document.querySelector('[data-duplicate-post-title]');
            const template = document.querySelector('[data-duplicate-template]');
            const format = document.querySelector('[data-duplicate-format]');

            if (postId) {
                postId.value = button.getAttribute('data-post-id') || '';
            }

            if (postTitle) {
                postTitle.textContent = button.getAttribute('data-post-title') || '';
            }

            if (template) {
                template.value = button.getAttribute('data-template-id') || '';
            }

            if (format) {
                format.value = button.getAttribute('data-format') || '';
            }
        });
    });
});
