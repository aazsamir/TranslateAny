<?php

declare(strict_types=1);

namespace Tests\Mock;

use App\System\Document\Chunking\ChunkingStrategy;
use App\System\Document\Chunking\ChunkingStrategyFactory;
use App\System\Document\Chunking\NoopChunkingStrategy;

class ChunkingStrategyFactoryMock extends ChunkingStrategyFactory
{
    public function create(): ChunkingStrategy
    {
        return new NoopChunkingStrategy();
    }
}
