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

function valid_id(mixed $value): ?int
{
    $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    return $id === false ? null : (int) $id;
}

function limit_text(array &$errors, array $data, string $field, int $max): void
{
    $value = trim((string) ($data[$field] ?? ''));

    if (mb_strlen($value) > $max) {
        $errors[$field] = "Maximo {$max} caracteres.";
    }
}
