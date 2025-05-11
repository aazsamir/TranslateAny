<?php

declare(strict_types=1);

namespace App\System\Document\Chunking;

class NoopChunkingStrategy implements ChunkingStrategy
{
    public function chunk(iterable $pages): iterable
    {
        foreach ($pages as $i => $page) {
            yield new Chunk(
                page: $i,
                text: $page,
            );
        }
    }
}
