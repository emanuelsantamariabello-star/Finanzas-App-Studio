<?php

declare(strict_types=1);

function asset(string $path): string
{
    return rtrim((string) config('app.base_url'), '/') . '/assets/' . ltrim($path, '/');
}
