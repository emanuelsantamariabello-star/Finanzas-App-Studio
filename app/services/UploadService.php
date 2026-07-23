<?php

declare(strict_types=1);

final class UploadService
{
    private const MAX_SIZE = 8388608;
    private const LIBRARY_LIMIT = 18;
    private const ALLOWED_MIME = [
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
        'image/webp' => 'webp',
    ];

    public function handle(?array $file): array
    {
        if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return ['path' => null, 'error' => null];
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return ['path' => null, 'error' => 'No fue posible cargar la imagen.'];
        }

        if ((int) ($file['size'] ?? 0) > self::MAX_SIZE) {
            return ['path' => null, 'error' => 'La imagen no puede superar 8 MB.'];
        }

        $tmpName = (string) ($file['tmp_name'] ?? '');
        $extension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmpName);

        if (!is_string($mime) || !isset(self::ALLOWED_MIME[$mime])) {
            return ['path' => null, 'error' => 'La imagen debe ser PNG, JPG, JPEG o WEBP.'];
        }

        if (!in_array($extension, ['png', 'jpg', 'jpeg', 'webp'], true)) {
            return ['path' => null, 'error' => 'La extension del archivo no es valida.'];
        }

        $safeName = bin2hex(random_bytes(16)) . '.' . self::ALLOWED_MIME[$mime];
        $relativePath = 'public/uploads/' . $safeName;
        $destination = APP_BASE_PATH . '/' . $relativePath;

        if (!move_uploaded_file($tmpName, $destination)) {
            return ['path' => null, 'error' => 'No fue posible guardar la imagen.'];
        }

        return ['path' => $relativePath, 'error' => null];
    }

    public function recentLibrary(): array
    {
        return $this->libraryFiles(self::LIBRARY_LIMIT);
    }

    public function libraryFiles(?int $limit = null): array
    {
        $uploadRoot = APP_BASE_PATH . '/public/uploads';

        if (!is_dir($uploadRoot)) {
            return [];
        }

        $files = glob($uploadRoot . '/*.{png,jpg,jpeg,webp}', GLOB_BRACE);

        if ($files === false) {
            return [];
        }

        usort($files, static fn (string $left, string $right): int => filemtime($right) <=> filemtime($left));

        if ($limit !== null) {
            $files = array_slice($files, 0, $limit);
        }

        return array_map(
            static function (string $file): array {
                $relativePath = 'public/uploads/' . basename($file);
                $size = filesize($file) ?: 0;

                return [
                    'name' => basename($file),
                    'path' => $relativePath,
                    'url' => url($relativePath),
                    'size' => $size,
                    'size_label' => self::formatBytes($size),
                    'updated_at' => date('Y-m-d H:i', filemtime($file) ?: time()),
                ];
            },
            $files
        );
    }

    public function resolveLibraryPath(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (!str_starts_with($path, 'public/uploads/')) {
            return null;
        }

        $fullPath = realpath(APP_BASE_PATH . '/' . $path);
        $uploadRoot = realpath(APP_BASE_PATH . '/public/uploads');

        if ($fullPath === false || $uploadRoot === false || !str_starts_with($fullPath, $uploadRoot)) {
            return null;
        }

        if (!is_file($fullPath)) {
            return null;
        }

        return $path;
    }

    public function deleteLibraryFile(string $path): bool
    {
        $resolvedPath = $this->resolveLibraryPath($path);

        if ($resolvedPath === null) {
            return false;
        }

        $fullPath = realpath(APP_BASE_PATH . '/' . $resolvedPath);

        return $fullPath !== false && is_file($fullPath) && unlink($fullPath);
    }

    public function deleteIfUnused(?string $path, PostService $posts, ?int $excludeId = null): void
    {
        if ($path === null || $path === '') {
            return;
        }

        if ($posts->imageUseCount($path, $excludeId) > 0) {
            return;
        }

        $fullPath = realpath(APP_BASE_PATH . '/' . $path);
        $uploadRoot = realpath(APP_BASE_PATH . '/public/uploads');

        if ($fullPath === false || $uploadRoot === false || !str_starts_with($fullPath, $uploadRoot)) {
            return;
        }

        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }

    private static function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        }

        return number_format(max($bytes, 1) / 1024, 1) . ' KB';
    }
}
