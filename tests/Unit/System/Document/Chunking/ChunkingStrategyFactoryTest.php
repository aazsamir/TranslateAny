<?php

declare(strict_types=1);

namespace Tests\Unit\System\Document\Chunking;

use App\System\Document\Chunking\ChunkingStrategyFactory;
use App\System\Document\Chunking\FullPageStrategy;
use Tests\TestCase;

class ChunkingStrategyFactoryTest extends TestCase
{
    private ChunkingStrategyFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new ChunkingStrategyFactory();
    }

    public function testCreate(): void
    {
        $strategy = $this->factory->create();

        $this->assertInstanceOf(FullPageStrategy::class, $strategy);
    }
}
