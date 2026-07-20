<?php

declare(strict_types=1);

final class Post
{
    public function __construct(
        public readonly int $id,
        public readonly ?int $templateId,
        public readonly string $title,
        public readonly string $status,
    ) {
    }
}
