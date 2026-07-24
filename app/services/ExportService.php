<?php

declare(strict_types=1);

final class ExportService
{
    private const MAX_PNG_SIZE = 20971520;

    private Export $exports;

    public function __construct(private PDO $pdo)
    {
        $this->exports = new Export($pdo);
    }

    public function count(): int
    {
        return $this->exports->count();
    }

    public function list(?int $templateId = null, ?string $format = null): array
    {
        return $this->exports->all($templateId, $format);
    }

    public function find(int $id): ?array
    {
        return $this->exports->find($id);
    }

    public function register(int $postId, string $format): string
    {
        $filePath = 'public/exports/finanzas-app-export-' . $postId . '-' . date('Y-m-d-His') . '.png';
        $this->exports->create($postId, $format, $filePath);

        return $filePath;
    }

    public function storeUploadedPng(array $post, ?array $file, ?string $format = null): array
    {
        if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('PNG no recibido.');
        }

        if ((int) ($file['size'] ?? 0) <= 0 || (int) $file['size'] > self::MAX_PNG_SIZE) {
            throw new RuntimeException('Tamaño de PNG no valido.');
        }

        $tmpName = (string) ($file['tmp_name'] ?? '');
        $contents = file_get_contents($tmpName);

        if ($contents === false || !str_starts_with($contents, "\x89PNG\r\n\x1a\n")) {
            throw new RuntimeException('Archivo PNG no valido.');
        }

        $imageSize = getimagesizefromstring($contents);

        if ($imageSize === false || ($imageSize['mime'] ?? '') !== 'image/png') {
            throw new RuntimeException('Imagen PNG no valida.');
        }

        $exportFormat = $format ?? (string) $post['format'];
        $expected = PostService::FORMATS[$exportFormat] ?? null;

        if ($expected === null || (int) $imageSize[0] !== $expected['width'] || (int) $imageSize[1] !== $expected['height']) {
            throw new RuntimeException('Dimensiones de exportacion no validas.');
        }

        $fileName = $this->buildFileName($post, $exportFormat);
        $relativePath = 'public/exports/' . $fileName;
        $destination = APP_BASE_PATH . '/' . $relativePath;

        if (file_put_contents($destination, $contents, LOCK_EX) === false) {
            throw new RuntimeException('No fue posible guardar el PNG.');
        }

        $this->exports->create((int) $post['id'], $exportFormat, $relativePath);

        return [
            'file_path' => $relativePath,
            'width' => (int) $imageSize[0],
            'height' => (int) $imageSize[1],
        ];
    }

    private function buildFileName(array $post, string $format): string
    {
        $title = strtolower((string) ($post['title'] ?? 'publicacion'));
        $title = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $title) ?: $title;
        $slug = preg_replace('/[^a-z0-9]+/', '-', $title) ?: 'publicacion';
        $slug = trim($slug, '-');
        $slug = substr($slug, 0, 80) ?: 'publicacion';

        return 'finanzas-app-' . $slug . '-' . $format . '-' . date('Y-m-d-His') . '-' . bin2hex(random_bytes(4)) . '.png';
    }
}
