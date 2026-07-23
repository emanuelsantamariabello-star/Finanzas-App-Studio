<?php

declare(strict_types=1);

final class UploadService
{
    private const MAX_SIZE = 8388608;
    private const LIBRARY_LIMIT = 18;
    private const METADATA_PATH = '/storage/media-library.json';
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

    public function libraryFiles(?int $limit = null, string $search = '', string $tag = ''): array
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
        $metadata = $this->readMetadata();

        if ($search !== '' || $tag !== '') {
            $files = array_filter($files, static function (string $file) use ($metadata, $search, $tag): bool {
                $relativePath = 'public/uploads/' . basename($file);
                $tags = $metadata[$relativePath]['tags'] ?? [];
                $tags = is_array($tags) ? $tags : [];
                $name = basename($file);
                $matchesSearch = $search === '' || str_contains(strtolower($name), strtolower($search));

                foreach ($tags as $item) {
                    if (str_contains(strtolower((string) $item), strtolower($search))) {
                        $matchesSearch = true;
                        break;
                    }
                }

                $matchesTag = $tag === '' || in_array($tag, $tags, true);

                return $matchesSearch && $matchesTag;
            });
        }

        if ($limit !== null) {
            $files = array_slice($files, 0, $limit);
        }

        return array_map(
            static function (string $file) use ($metadata): array {
                $relativePath = 'public/uploads/' . basename($file);
                $size = filesize($file) ?: 0;

                return [
                    'name' => basename($file),
                    'path' => $relativePath,
                    'url' => url($relativePath),
                    'size' => $size,
                    'size_label' => self::formatBytes($size),
                    'tags' => is_array($metadata[$relativePath]['tags'] ?? null) ? $metadata[$relativePath]['tags'] : [],
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

        if ($fullPath === false || !is_file($fullPath) || !unlink($fullPath)) {
            return false;
        }

        $this->forgetMetadata($resolvedPath);

        return true;
    }

    public function syncTags(string $path, string $rawTags): bool
    {
        $resolvedPath = $this->resolveLibraryPath($path);

        if ($resolvedPath === null) {
            return false;
        }

        $metadata = $this->readMetadata();
        $metadata[$resolvedPath]['tags'] = $this->normalizeTags($rawTags);

        return $this->writeMetadata($metadata);
    }

    public function allTags(): array
    {
        $tags = [];

        foreach ($this->readMetadata() as $item) {
            foreach (($item['tags'] ?? []) as $tag) {
                $tags[$tag] = $tag;
            }
        }

        sort($tags);

        return array_values($tags);
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
            $this->forgetMetadata($path);
        }
    }

    private static function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        }

        return number_format(max($bytes, 1) / 1024, 1) . ' KB';
    }

    private function normalizeTags(string $rawTags): array
    {
        $tags = array_map(
            static fn (string $tag): string => trim(strtolower($tag)),
            preg_split('/[,#]/', $rawTags) ?: []
        );

        $tags = array_filter($tags, static fn (string $tag): bool => $tag !== '');

        return array_values(array_unique(array_slice($tags, 0, 8)));
    }

    private function readMetadata(): array
    {
        $path = APP_BASE_PATH . self::METADATA_PATH;

        if (!is_file($path)) {
            return [];
        }

        $contents = file_get_contents($path);
        $metadata = $contents === false ? [] : json_decode($contents, true);

        return is_array($metadata) ? $metadata : [];
    }

    private function writeMetadata(array $metadata): bool
    {
        return file_put_contents(
            APP_BASE_PATH . self::METADATA_PATH,
            json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)
        ) !== false;
    }

    private function forgetMetadata(string $path): void
    {
        $metadata = $this->readMetadata();

        if (!isset($metadata[$path])) {
            return;
        }

        unset($metadata[$path]);
        $this->writeMetadata($metadata);
    }
}
