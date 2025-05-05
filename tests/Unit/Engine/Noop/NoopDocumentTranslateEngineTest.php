<?php

declare(strict_types=1);

namespace Tests\Unit\Engine\Noop;

use App\Engine\DocumentTranslation;
use App\Engine\Noop\NoopDocumentTranslateEngine;
use Tests\TestCase;
use Tests\Unit\Utils\DocumentTranslatePayloadFixture;

class NoopDocumentTranslateEngineTest extends TestCase
{
    private NoopDocumentTranslateEngine $engine;

    protected function setUp(): void
    {
        $this->engine = NoopDocumentTranslateEngine::new();
    }

    public function test(): void
    {
        $payload = DocumentTranslatePayloadFixture::get();
        $result = $this->engine->translateDocument($payload);

        $this->assertSame(
            '123',
            $result->id,
        );
    }

    public function testDownloadPath(): void
    {
        $payload = new DocumentTranslation(id: '123');
        $result = $this->engine->downloadPath($payload);

        $this->assertSame(
            'placeholder',
            $result,
        );
    }
}
