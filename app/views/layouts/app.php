<?php declare(strict_types=1); ?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Inicio') ?> | <?= e(config('app.name')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= e(asset('css/app.css')) ?>" rel="stylesheet">
</head>
<body>
    <div class="app-shell">
        <?php require APP_BASE_PATH . '/app/views/partials/sidebar.php'; ?>

        <div class="app-main">
            <?php require APP_BASE_PATH . '/app/views/partials/navbar.php'; ?>

            <main class="container-fluid px-3 px-lg-4 py-4">
                <?= $content ?>
            </main>

            <?php require APP_BASE_PATH . '/app/views/partials/footer.php'; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
