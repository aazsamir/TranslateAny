<?php

declare(strict_types=1);

namespace Tests\Unit\Engine\Noop;

use App\Engine\Noop\NoopDetectEngine;
use Tests\TestCase;

class NoopDetectEngineTest extends TestCase
{
    private NoopDetectEngine $engine;

    protected function setUp(): void
    {
        $this->engine = NoopDetectEngine::new();
    }

    public function test(): void
    {
        $result = $this->engine->detect('123');

        $this->assertEmpty($result);
    }
}
