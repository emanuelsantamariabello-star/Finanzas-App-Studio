<?php

declare(strict_types=1);

function required(array $data, array $fields): array
{
    $errors = [];

    foreach ($fields as $field) {
        if (!isset($data[$field]) || trim((string) $data[$field]) === '') {
            $errors[$field] = 'Este campo es obligatorio.';
        }
    }

    return $errors;
}
