<?php

declare(strict_types=1);

namespace Tests\Unit\Engine\Route;

use App\Engine\Noop\NoopDetectionEngine;
use App\Engine\Noop\NoopTranslateEngine;
use App\Engine\Route\RouteEngine;
use App\Engine\Route\TranslateRoute;
use App\Engine\TranslatePayload;
use App\Engine\Translation;
use App\System\Language;
use Tests\Mock\NullLogger;
use Tests\Mock\TranslateEngineMock;
use Tests\TestCase;

class RouteEngineTest extends TestCase
{
    private RouteEngine $engine;

    protected function setUp(): void
    {
        $this->engine = new RouteEngine(
            routes: [
                TranslateRoute::new(
                    languages: [Language::en],
                    engine: new TranslateEngineMock(
                        new Translation(
                            text: 'first',
                        ),
                    ),
                ),
                TranslateRoute::new(
                    languages: null,
                    engine: new TranslateEngineMock(
                        new Translation(
                            text: 'second',
                        ),
                    ),
                ),
            ],
            logger: new NullLogger(),
        );
    }

    public function testFirstMatching(): void
    {
        $payload = new TranslatePayload(
            text: 'test',
            targetLanguage: Language::en,
        );

        $translation = $this->engine->translate($payload);

        $this->assertEquals('first', $translation->text);
    }

    public function testSecondMatching(): void
    {
        $payload = new TranslatePayload(
            text: 'test',
            targetLanguage: Language::pl,
        );

        $translation = $this->engine->translate($payload);

        $this->assertEquals('second', $translation->text);
    }

    public function testNoMatching(): void
    {
        $payload = new TranslatePayload(
            text: 'test',
            targetLanguage: Language::fr,
        );

        $engine = new RouteEngine([], new NullLogger());

        $this->expectException(\RuntimeException::class);

        $engine->translate($payload);
    }

    public function testLanguages(): void
    {
        $languages = $this->engine->languages();

        $this->assertCount(2, $languages);
    }
}
