<?php

declare(strict_types=1);

namespace Tests\Unit\Engine\LibreTranslate;

use App\Engine\LibreTranslate\LibreTranslateEngine;
use App\System\Language;
use Tests\Mock\PsrClientMock;
use Tests\TestCase;
use Tests\Unit\Utils\PayloadFixture;

class LibreTranslateEngineTest extends TestCase
{
    private PsrClientMock $psrClient;
    private LibreTranslateEngine $engine;

    protected function setUp(): void
    {
        $this->psrClient = new PsrClientMock();
        $this->engine = new LibreTranslateEngine(
            '',
            $this->psrClient,
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
                'detectedLanguage' => [
                    'language' => 'en',
                    'confidence' => 0.5,
                ],
            ],
        );

        $translation = $this->engine->translate(PayloadFixture::get());

        $this->assertEquals('Hello world!', $translation->text);
        $this->assertEquals(['Hello world!'], $translation->alternatives);
        $this->assertEquals('en', $translation->detectedLanguage->language);
        $this->assertEquals(0.5, $translation->detectedLanguage->confidence);
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
        $this->assertEquals('en', $detections[0]->language);
        $this->assertEquals(0.5, $detections[0]->confidence);
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
        $this->assertEquals('en', $languages[0]->language);
        $this->assertEquals(['pl'], $languages[0]->targets);
    }
}
