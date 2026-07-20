<?php

declare(strict_types=1);

final class TemplateService
{
    private Template $templates;

    public function __construct(private PDO $pdo)
    {
        $this->templates = new Template($pdo);
    }

    public function count(): int
    {
        return $this->templates->countActive();
    }

    public function all(): array
    {
        return $this->templates->active();
    }

    public function find(int $id): ?array
    {
        return $this->templates->find($id);
    }

    public function bySlug(string $slug): ?array
    {
        foreach ($this->all() as $template) {
            if ($template['slug'] === $slug) {
                return $template;
            }
        }

        return null;
    }
}
