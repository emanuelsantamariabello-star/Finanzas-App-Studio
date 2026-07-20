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
            'SELECT id, name, slug, description, canvas_width, canvas_height
             FROM templates
             WHERE is_active = 1
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
