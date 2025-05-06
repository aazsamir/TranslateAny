<?php

declare(strict_types=1);

namespace Tests\Unit\Engine\LibreTranslate;

use App\Engine\LibreTranslate\LibreTranslateEngine;
use App\System\Language;
use App\System\Logger\MemoryLogger;
use Tests\Mock\PsrClientMock;
use Tests\TestCase;
use Tests\Unit\Utils\TranslatePayloadFixture;

class LibreTranslateEngineTest extends TestCase
{
    private PsrClientMock $psrClient;
    private LibreTranslateEngine $engine;

    protected function setUp(): void
    {
        $this->psrClient = new PsrClientMock();
        $this->engine = new LibreTranslateEngine(
            host: '',
            client: $this->psrClient,
            logger: new MemoryLogger(),
        );
    }

    public function testTranslate(): void
    {
        $this->psrClient->setResponse(
            [
                'translatedText' => 'Hello world!',
                'alternatives' => [
                    'Hello world!',
                ],
            ],
        );

        $translation = $this->engine->translate(TranslatePayloadFixture::get());

        $this->assertEquals('Hello world!', $translation->text);
        $this->assertEquals(['Hello world!'], $translation->alternatives);
        $this->assertNull($translation->detectedLanguage);

        $this->assertEquals(
            [
                'q' => 'Hello world!',
                'target' => 'pl',
                'source' => 'en',
            ],
            $this->psrClient->getArrayBody(),
        );
    }

    public function testTranslateSourceAuto(): void
    {
        $this->psrClient->setResponse(
            [
                'translatedText' => 'Hello world!',
                'alternatives' => [
                    'Hello world!',
                ],
                'detectedLanguage' => [
                    'language' => 'en',
                    'confidence' => 0.5,
                ],
            ],
        );

        $translation = $this->engine->translate(
            TranslatePayloadFixture::get(
                sourceLanguage: null,
                format: 'html',
                alternatives: 10,
            ),
        );

        $this->assertEquals('Hello world!', $translation->text);
        $this->assertEquals(['Hello world!'], $translation->alternatives);
        $this->assertEquals(Language::en, $translation->detectedLanguage->language);
        $this->assertEquals(0.5, $translation->detectedLanguage->confidence);

        $this->assertEquals(
            [
                'q' => 'Hello world!',
                'target' => 'pl',
                'source' => 'auto',
                'format' => 'html',
                'alternatives' => 10,
            ],
            $this->psrClient->getArrayBody(),
        );
    }

    public function testDetection(): void
    {
        $this->psrClient->setResponse(
            [
                [
                    'language' => 'en',
                    'confidence' => 0.5,
                ],
            ],
        );

        $detections = $this->engine->detect('Hello world!');

        $this->assertCount(1, $detections);
        $this->assertEquals(Language::en, $detections[0]->language);
        $this->assertEquals(0.5, $detections[0]->confidence);

        $this->assertEquals(
            [
                'q' => 'Hello world!',
            ],
            $this->psrClient->getArrayBody(),
        );
    }

    public function testLanguages(): void
    {
        $this->psrClient->setResponse(
            [
                [
                    'code' => 'en',
                    'name' => 'English',
                    'targets' => [
                        'pl',
                    ],
                ],
            ],
        );

        $languages = $this->engine->languages();

        $this->assertCount(1, $languages);
        $this->assertEquals(Language::en, $languages[0]->language);
        $this->assertEquals([Language::pl], $languages[0]->targets);
    }
}
