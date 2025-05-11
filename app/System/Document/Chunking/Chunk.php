<?php

declare(strict_types=1);

namespace App\System\Document\Chunking;

readonly class Chunk
{
    public function __construct(
        public int $page,
        public string $text,
    ) {
    }
}
