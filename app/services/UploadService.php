<?php

declare(strict_types=1);

final class UploadService
{
    private const MAX_SIZE = 8388608;
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
}
