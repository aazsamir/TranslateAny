<?php

declare(strict_types=1);

namespace Tests\Unit\Engine\Noop;

use App\Engine\Noop\NoopDetectionEngine;
use Tests\TestCase;

class NoopDetectionEngineTest extends TestCase
{
    private NoopDetectionEngine $engine;

    protected function setUp(): void
    {
        $this->engine = NoopDetectionEngine::new();
    }

    public function test(): void
    {
        $result = $this->engine->detect('123');

        $this->assertEmpty($result);
    }
}
