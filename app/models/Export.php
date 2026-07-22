<?php

declare(strict_types=1);

final class Export
{
    public function __construct(private PDO $pdo)
    {
    }

    public function create(int $postId, string $format, string $filePath): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO exports (post_id, format, file_path, exported_at)
             VALUES (:post_id, :format, :file_path, NOW())'
        );
        $statement->execute([
            'post_id' => $postId,
            'format' => $format,
            'file_path' => $filePath,
        ]);
    }

    public function count(): int
    {
        $statement = $this->pdo->query('SELECT COUNT(*) FROM exports');

        return (int) $statement->fetchColumn();
    }

    public function all(?int $templateId = null, ?string $format = null): array
    {
        $sql = 'SELECT exports.*, posts.title AS post_title, posts.template_id, templates.name AS template_name
                FROM exports
                INNER JOIN posts ON posts.id = exports.post_id
                LEFT JOIN templates ON templates.id = posts.template_id';
        $conditions = [];
        $params = [];

        if ($templateId !== null) {
            $conditions[] = 'posts.template_id = :template_id';
            $params['template_id'] = $templateId;
        }

        if ($format !== null && $format !== '') {
            $conditions[] = 'exports.format = :format';
            $params['format'] = $format;
        }

        if ($conditions !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY exports.exported_at DESC';

        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT exports.*, posts.title AS post_title
             FROM exports
             INNER JOIN posts ON posts.id = exports.post_id
             WHERE exports.id = :id'
        );
        $statement->execute(['id' => $id]);
        $export = $statement->fetch();

        return $export ?: null;
    }
}
