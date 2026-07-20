<?php declare(strict_types=1); ?>
<aside class="app-sidebar" data-sidebar>
    <div class="sidebar-brand">
        <div class="brand-mark">FA</div>
        <div>
            <strong>Finanzas App</strong>
            <span>Studio</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a class="<?= current_path() === '/' ? 'active' : '' ?>" href="<?= e(url('/')) ?>">
            <span>Dashboard</span>
        </a>
        <a class="<?= str_starts_with(current_path(), '/posts') ? 'active' : '' ?>" href="<?= e(url('/posts')) ?>">
            <span>Publicaciones</span>
        </a>
        <a class="disabled" href="#" aria-disabled="true">
            <span>Plantillas</span>
        </a>
        <a class="disabled" href="#" aria-disabled="true">
            <span>Exportaciones</span>
        </a>
    </nav>
</aside>
