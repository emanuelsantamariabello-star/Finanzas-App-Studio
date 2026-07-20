<?php

declare(strict_types=1);

final class TemplateService
{
    public function __construct(private PDO $pdo)
    {
    }

    public function count(): int
    {
        $statement = $this->pdo->query('SELECT COUNT(*) FROM templates');

        return (int) $statement->fetchColumn();
    }
}
