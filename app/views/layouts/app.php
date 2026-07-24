<?php declare(strict_types=1); ?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Inicio') ?> | <?= e(config('app.name')) ?></title>
    <link rel="icon" href="<?= e(asset('images/branding/favicon-finanzas-app.png')) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= e(asset('css/app.css') . '?v=' . filemtime(APP_BASE_PATH . '/assets/css/app.css')) ?>" rel="stylesheet">
</head>
<body>
    <div class="app-shell" data-app-shell>
        <?php require APP_BASE_PATH . '/app/views/partials/sidebar.php'; ?>

        <div class="app-main">
            <?php require APP_BASE_PATH . '/app/views/partials/navbar.php'; ?>

            <main class="container-fluid px-3 px-lg-4 py-4">
                <?= $content ?>
            </main>

            <?php require APP_BASE_PATH . '/app/views/partials/footer.php'; ?>
        </div>
    </div>

    <?php require APP_BASE_PATH . '/app/views/partials/confirm_modal.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= e(asset('js/app.js') . '?v=' . filemtime(APP_BASE_PATH . '/assets/js/app.js')) ?>"></script>
</body>
</html>
