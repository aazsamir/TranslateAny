<?php

declare(strict_types=1);

namespace App\System\Document\Chunking;

interface ChunkingStrategy
{
    /**
     * @param iterable<int, string> $pages
     *
     * @return iterable<Chunk>
     */
    public function chunk(iterable $pages): iterable;
}
