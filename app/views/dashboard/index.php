<?php declare(strict_types=1); ?>
<section class="dashboard-header mb-4">
    <div>
        <p class="section-kicker mb-2">Contenido promocional</p>
        <h2 class="mb-2">Base lista para construir publicaciones editables</h2>
        <p class="text-muted mb-0">Arquitectura inicial preparada para plantillas, capturas, textos y exportaciones futuras.</p>
    </div>
</section>

<section class="row g-3 mb-4">
    <?php foreach ($stats as $stat): ?>
        <div class="col-12 col-md-4">
            <article class="metric-card">
                <span class="metric-label"><?= e($stat['label']) ?></span>
                <strong class="metric-value text-<?= e($stat['tone']) ?>"><?= e($stat['value']) ?></strong>
            </article>
        </div>
    <?php endforeach; ?>
</section>

<section class="row g-4">
    <div class="col-12 col-xl-7">
        <article class="panel">
            <div class="panel-header">
                <h3>Estado de la Fase 1</h3>
            </div>
            <div class="status-list">
                <div><span></span>Estructura modular creada</div>
                <div><span></span>Configuracion central preparada</div>
                <div><span></span>Rutas y layout inicial disponibles</div>
                <div><span></span>Base de datos definida para migracion manual</div>
            </div>
        </article>
    </div>

    <div class="col-12 col-xl-5">
        <article class="panel">
            <div class="panel-header">
                <h3>Proximas areas</h3>
            </div>
            <div class="next-list">
                <span>Editor visual</span>
                <span>Gestion de plantillas</span>
                <span>Exportacion de imagenes</span>
                <span>Biblioteca de capturas</span>
            </div>
        </article>
    </div>
</section>
