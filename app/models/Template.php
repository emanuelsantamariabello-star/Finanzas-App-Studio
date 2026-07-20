<?php

declare(strict_types=1);

final class Template
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $description = null,
    ) {
    }
}
