<?php

declare(strict_types=1);

final class Export
{
    public function __construct(
        public readonly int $id,
        public readonly int $postId,
        public readonly string $format,
        public readonly string $filePath,
    ) {
    }
}
