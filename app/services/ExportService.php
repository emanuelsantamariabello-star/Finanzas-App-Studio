<?php

declare(strict_types=1);

final class ExportService
{
    private Export $exports;

    public function __construct(private PDO $pdo)
    {
        $this->exports = new Export($pdo);
    }

    public function count(): int
    {
        return $this->exports->count();
    }

    public function register(int $postId, string $format): string
    {
        $filePath = 'public/exports/finanzas-app-export-' . $postId . '-' . date('Y-m-d-His') . '.png';
        $this->exports->create($postId, $format, $filePath);

        return $filePath;
    }
}
