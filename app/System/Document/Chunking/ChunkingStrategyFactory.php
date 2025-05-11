<?php

declare(strict_types=1);

namespace App\System\Document\Chunking;

class ChunkingStrategyFactory
{
    public function create(): ChunkingStrategy
    {
        return new FullPageStrategy();
    }
}
