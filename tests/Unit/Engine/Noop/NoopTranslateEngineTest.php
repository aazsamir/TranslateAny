<?php

declare(strict_types=1);

namespace Tests\Unit\Engine\Noop;

use App\Engine\Noop\NoopTranslateEngine;
use Tests\TestCase;
use Tests\Unit\Utils\TranslatePayloadFixture;

class NoopTranslateEngineTest extends TestCase
{
    private NoopTranslateEngine $engine;

    protected function setUp(): void
    {
        $this->engine = NoopTranslateEngine::new();
    }

    public function testTranslate(): void
    {
        $payload = TranslatePayloadFixture::get();
        $result = $this->engine->translate($payload);

        $this->assertSame(
            'Hello world!',
            $result->text,
        );
    }

    public function testLanguages(): void
    {
        $result = $this->engine->languages();

        $this->assertSame(
            [],
            $result,
        );
    }
}
