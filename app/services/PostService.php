<?php

declare(strict_types=1);

final class PostService
{
    public const FORMATS = [
        'instagram_square' => ['label' => 'Instagram cuadrado', 'width' => 1080, 'height' => 1080],
        'instagram_vertical' => ['label' => 'Instagram vertical', 'width' => 1080, 'height' => 1350],
        'story' => ['label' => 'Historia / Estado', 'width' => 1080, 'height' => 1920],
    ];

    public const STATUSES = ['draft', 'exported'];

    private Post $posts;

    public function __construct(private PDO $pdo)
    {
        $this->posts = new Post($pdo);
    }

    public function list(?int $templateId = null): array
    {
        return $this->posts->all($templateId);
    }

    public function recent(int $limit = 5): array
    {
        return $this->posts->recent($limit);
    }

    public function find(int $id): ?array
    {
        return $this->posts->find($id);
    }

    public function count(?string $status = null): int
    {
        return $this->posts->count($status);
    }

    public function save(array $input, ?array $currentPost = null, ?string $imagePath = null): array
    {
        $data = $this->normalize($input, $currentPost, $imagePath);

        if ($currentPost === null) {
            $id = $this->posts->create($data);
        } else {
            $id = (int) $currentPost['id'];
            $this->posts->update($id, $data);
        }

        return $this->find($id) ?? [];
    }

    public function duplicate(int $id): ?int
    {
        $post = $this->find($id);

        if ($post === null) {
            return null;
        }

        $data = $this->normalize([
            'template_id' => $post['template_id'],
            'title' => $post['title'] . ' (copia)',
            'subtitle' => $post['subtitle'],
            'description' => $post['description'],
            'cta_text' => $post['cta_text'],
            'version_label' => $post['version_label'],
            'format' => $post['format'],
        ], null, $post['image_path']);

        return $this->posts->create($data);
    }

    public function delete(int $id): void
    {
        $this->posts->delete($id);
    }

    public function markExported(int $id): void
    {
        $this->posts->markExported($id);
    }

    public function imageUseCount(string $imagePath, ?int $excludeId = null): int
    {
        return $this->posts->imageUseCount($imagePath, $excludeId);
    }

    public function validate(array $input, TemplateService $templates): array
    {
        $errors = required($input, ['template_id', 'format', 'title']);
        $templateId = valid_id($input['template_id'] ?? null);
        $template = $templateId === null ? null : $templates->find($templateId);

        if ($template === null) {
            $errors['template_id'] = 'Selecciona una plantilla valida.';
        }

        if (!isset(self::FORMATS[(string) ($input['format'] ?? '')])) {
            $errors['format'] = 'Selecciona un formato valido.';
        }

        limit_text($errors, $input, 'title', 90);
        limit_text($errors, $input, 'subtitle', 130);
        limit_text($errors, $input, 'description', 320);
        limit_text($errors, $input, 'cta_text', 60);
        limit_text($errors, $input, 'version_label', 30);

        if ($template !== null && in_array($template['slug'], ['nueva-funcionalidad', 'consejo-financiero'], true)) {
            if (trim((string) ($input['description'] ?? '')) === '') {
                $errors['description'] = 'La descripcion es obligatoria para esta plantilla.';
            }
        }

        if ($template !== null && $template['slug'] === 'actualizacion-de-version') {
            if (trim((string) ($input['version_label'] ?? '')) === '') {
                $errors['version_label'] = 'La version es obligatoria para esta plantilla.';
            }
            if (trim((string) ($input['description'] ?? '')) === '') {
                $errors['description'] = 'La descripcion es obligatoria para esta plantilla.';
            }
        }

        return $errors;
    }

    private function normalize(array $input, ?array $currentPost, ?string $imagePath): array
    {
        $content = [
            'subtitle' => trim((string) ($input['subtitle'] ?? '')),
            'description' => trim((string) ($input['description'] ?? '')),
            'cta_text' => trim((string) ($input['cta_text'] ?? '')),
            'version_label' => trim((string) ($input['version_label'] ?? '')),
        ];

        return [
            'template_id' => (int) $input['template_id'],
            'title' => trim((string) $input['title']),
            'subtitle' => $content['subtitle'] !== '' ? $content['subtitle'] : null,
            'description' => $content['description'] !== '' ? $content['description'] : null,
            'cta_text' => $content['cta_text'] !== '' ? $content['cta_text'] : null,
            'version_label' => $content['version_label'] !== '' ? $content['version_label'] : null,
            'format' => (string) $input['format'],
            'image_path' => $imagePath ?? $currentPost['image_path'] ?? null,
            'status' => $currentPost['status'] ?? 'draft',
            'content_json' => json_encode($content, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
        ];
    }
}
