<?php

declare(strict_types=1);

$localConfigPath = APP_BASE_PATH . '/.env.php';
$exampleConfigPath = APP_BASE_PATH . '/.env.example.php';

$GLOBALS['config'] = file_exists($localConfigPath)
    ? require $localConfigPath
    : require $exampleConfigPath;

function config(string $key, mixed $default = null): mixed
{
    $value = $GLOBALS['config'];

    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }

        $value = $value[$segment];
    }

    return $value;
}
