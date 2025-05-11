<?php

declare(strict_types=1);

namespace Tests\Unit\System\Document\Chunking;

use App\System\Document\Chunking\Chunk;
use App\System\Document\Chunking\FullPageStrategy;
use Tests\TestCase;

class FullPageStrategyTest extends TestCase
{
    private FullPageStrategy $strategy;

    protected function setUp(): void
    {
        $this->strategy = new FullPageStrategy();
    }

    public function testChunk(): void
    {
        $pages = [
            "Line 1. \n Line 2. \n Line 3",
            "Line 1. \n Line 2. \n Line 3",
        ];

        $chunks = $this->strategy->chunk($pages);
        /** @var Chunk[] */
        $chunks = \iterator_to_array($chunks);

        $this->assertCount(2, $chunks);
        $first = $chunks[0];
        $this->assertEquals(1, $first->page);
        $this->assertEquals("Line 1. \n Line 2.", $first->text);

        $second = $chunks[1];
        $this->assertEquals(2, $second->page);
        $this->assertEquals("Line 3 Line 1. \n Line 2. \n Line 3", $second->text);
    }
}
