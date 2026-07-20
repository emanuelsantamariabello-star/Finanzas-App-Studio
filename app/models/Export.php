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
}
