<?php

declare(strict_types=1);

define('APP_BASE_PATH', __DIR__);

require APP_BASE_PATH . '/app/config/bootstrap.php';

$routes = require APP_BASE_PATH . '/routes/web.php';
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$basePath = parse_url(config('app.base_url', ''), PHP_URL_PATH) ?: '';

if ($basePath !== '' && str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath)) ?: '/';
}

$route = $routes[$uri] ?? $routes['/404'];

[$controller, $method] = $route;

(new $controller())->$method();
