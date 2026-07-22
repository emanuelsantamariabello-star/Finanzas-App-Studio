<?php declare(strict_types=1); ?>
<aside class="app-sidebar" data-sidebar>
    <div class="sidebar-brand">
        <div class="brand-mark">FA</div>
        <div class="brand-text">
            <strong>Finanzas App</strong>
            <span>Studio</span>
        </div>
        <button
            class="sidebar-collapse"
            type="button"
            data-sidebar-collapse
            aria-label="Contraer menu"
            aria-expanded="true"
        >
            <span data-sidebar-collapse-icon aria-hidden="true">‹</span>
        </button>
    </div>

    <nav class="sidebar-nav">
        <a class="<?= current_path() === '/' ? 'active' : '' ?>" href="<?= e(url('/')) ?>" title="Dashboard">
            <span class="nav-mark" aria-hidden="true">D</span>
            <span class="nav-label">Dashboard</span>
        </a>
        <a class="<?= str_starts_with(current_path(), '/posts') ? 'active' : '' ?>" href="<?= e(url('/posts')) ?>" title="Publicaciones">
            <span class="nav-mark" aria-hidden="true">P</span>
            <span class="nav-label">Publicaciones</span>
        </a>
        <a class="<?= current_path() === '/templates' ? 'active' : '' ?>" href="<?= e(url('/templates')) ?>" title="Plantillas">
            <span class="nav-mark" aria-hidden="true">T</span>
            <span class="nav-label">Plantillas</span>
        </a>
        <a class="<?= current_path() === '/exports' ? 'active' : '' ?>" href="<?= e(url('/exports')) ?>" title="Exportaciones">
            <span class="nav-mark" aria-hidden="true">E</span>
            <span class="nav-label">Exportaciones</span>
        </a>
    </nav>
</aside>
