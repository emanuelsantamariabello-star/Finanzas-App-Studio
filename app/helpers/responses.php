<?php

declare(strict_types=1);

function view(string $view, array $data = [], string $layout = 'layouts/app'): void
{
    extract($data, EXTR_SKIP);

    ob_start();
    require APP_BASE_PATH . '/app/views/' . $view . '.php';
    $content = ob_get_clean();

    require APP_BASE_PATH . '/app/views/' . $layout . '.php';
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function abort_not_found(): void
{
    http_response_code(404);
    view('errors/404', ['title' => 'Pagina no encontrada']);
}

function request_method(): string
{
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
}
