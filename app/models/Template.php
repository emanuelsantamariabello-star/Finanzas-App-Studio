<?php

declare(strict_types=1);

final class Template
{
    public function __construct(private PDO $pdo)
    {
    }

    public function active(): array
    {
        $statement = $this->pdo->query(
            'SELECT templates.id,
                    templates.name,
                    templates.slug,
                    templates.description,
                    templates.canvas_width,
                    templates.canvas_height,
                    COUNT(posts.id) AS posts_count
             FROM templates
             LEFT JOIN posts ON posts.template_id = templates.id
             WHERE is_active = 1
             GROUP BY templates.id, templates.name, templates.slug, templates.description, templates.canvas_width, templates.canvas_height
             ORDER BY id ASC'
        );

        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, name, slug, description, canvas_width, canvas_height
             FROM templates
             WHERE id = :id AND is_active = 1'
        );
        $statement->execute(['id' => $id]);
        $template = $statement->fetch();

        return $template ?: null;
    }

    public function countActive(): int
    {
        $statement = $this->pdo->query('SELECT COUNT(*) FROM templates WHERE is_active = 1');

        return (int) $statement->fetchColumn();
    }
}
