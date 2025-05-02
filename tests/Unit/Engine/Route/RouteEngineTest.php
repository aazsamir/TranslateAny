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
use Tests\Mock\TranslateEngineMock;
use Tests\TestCase;

class RouteEngineTest extends TestCase
{
    private RouteEngine $engine;

    protected function setUp(): void
    {
        $this->engine = new RouteEngine([
            new TranslateRoute(
                languages: [Language::en],
                engine: new TranslateEngineMock(
                    new Translation(
                        text: 'first',
                    ),
                ),
            ),
            new TranslateRoute(
                languages: null,
                engine: new TranslateEngineMock(
                    new Translation(
                        text: 'second',
                    ),
                ),
            ),
        ]);
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
}
