<?php

declare(strict_types=1);

final class Post
{
    public function __construct(private PDO $pdo)
    {
    }

    public function all(?int $templateId = null): array
    {
        $sql = 'SELECT posts.*, templates.name AS template_name, templates.slug AS template_slug
                FROM posts
                LEFT JOIN templates ON templates.id = posts.template_id';
        $params = [];

        if ($templateId !== null) {
            $sql .= ' WHERE posts.template_id = :template_id';
            $params['template_id'] = $templateId;
        }

        $sql .= ' ORDER BY COALESCE(posts.updated_at, posts.created_at) DESC';

        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll();
    }

    public function recent(int $limit = 5): array
    {
        $statement = $this->pdo->prepare(
            'SELECT posts.*, templates.name AS template_name
             FROM posts
             LEFT JOIN templates ON templates.id = posts.template_id
             ORDER BY COALESCE(posts.updated_at, posts.created_at) DESC
             LIMIT :limit'
        );
        $statement->bindValue('limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT posts.*, templates.name AS template_name, templates.slug AS template_slug
             FROM posts
             LEFT JOIN templates ON templates.id = posts.template_id
             WHERE posts.id = :id'
        );
        $statement->execute(['id' => $id]);
        $post = $statement->fetch();

        return $post ?: null;
    }

    public function create(array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO posts (
                template_id, title, subtitle, description, cta_text, version_label,
                format, image_path, status, content_json, created_at, updated_at
            ) VALUES (
                :template_id, :title, :subtitle, :description, :cta_text, :version_label,
                :format, :image_path, :status, :content_json, NOW(), NOW()
            )'
        );
        $statement->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $statement = $this->pdo->prepare(
            'UPDATE posts
             SET template_id = :template_id,
                 title = :title,
                 subtitle = :subtitle,
                 description = :description,
                 cta_text = :cta_text,
                 version_label = :version_label,
                 format = :format,
                 image_path = :image_path,
                 status = :status,
                 content_json = :content_json,
                 updated_at = NOW()
             WHERE id = :id'
        );
        $statement->execute($data);
    }

    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare('DELETE FROM posts WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    public function count(?string $status = null): int
    {
        if ($status === null) {
            $statement = $this->pdo->query('SELECT COUNT(*) FROM posts');
            return (int) $statement->fetchColumn();
        }

        $statement = $this->pdo->prepare('SELECT COUNT(*) FROM posts WHERE status = :status');
        $statement->execute(['status' => $status]);

        return (int) $statement->fetchColumn();
    }

    public function markExported(int $id): void
    {
        $statement = $this->pdo->prepare('UPDATE posts SET status = :status, updated_at = NOW() WHERE id = :id');
        $statement->execute(['id' => $id, 'status' => 'exported']);
    }

    public function imageUseCount(string $imagePath, ?int $excludeId = null): int
    {
        $sql = 'SELECT COUNT(*) FROM posts WHERE image_path = :image_path';
        $params = ['image_path' => $imagePath];

        if ($excludeId !== null) {
            $sql .= ' AND id <> :exclude_id';
            $params['exclude_id'] = $excludeId;
        }

        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return (int) $statement->fetchColumn();
    }
}
