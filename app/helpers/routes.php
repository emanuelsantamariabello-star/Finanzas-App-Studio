<?php

declare(strict_types=1);

function url(string $path = ''): string
{
    return rtrim((string) config('app.base_url'), '/') . '/' . ltrim($path, '/');
}

function current_path(): string
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $basePath = parse_url(config('app.base_url', ''), PHP_URL_PATH) ?: '';

    if ($basePath !== '' && str_starts_with($uri, $basePath)) {
        return substr($uri, strlen($basePath)) ?: '/';
    }

    return $uri;
}
