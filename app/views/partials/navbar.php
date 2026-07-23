<?php declare(strict_types=1); ?>
<nav class="navbar navbar-expand-lg app-navbar">
    <div class="container-fluid px-3 px-lg-4">
        <button class="btn btn-outline-secondary d-lg-none" type="button" data-sidebar-toggle aria-label="Abrir menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div>
            <p class="text-muted small mb-0">Herramienta interna</p>
            <h1 class="h5 mb-0"><?= e($title ?? 'Dashboard') ?></h1>
        </div>

        <div class="ms-auto d-flex align-items-center gap-2">
            <span class="badge app-badge">Local</span>
        </div>
    </div>
</nav>
