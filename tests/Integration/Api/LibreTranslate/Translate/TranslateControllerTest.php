<?php

declare(strict_types=1);

namespace Tests\Integration\Api\LibreTranslate\Translate;

use Tests\Integration\TestCase;

class TranslateControllerTest extends TestCase
{
    public function test(): void
    {
        $response = $this->http->post('/libre/translate', [
            'q' => 'Hello world!',
            'source' => 'auto',
            'target' => 'pl',
        ]);

        $response->assertOk();
        $body = $response->body;

        $this->assertIsArray($body);
        $this->assertSame(
            [
                'translatedText' => 'Hello world!',
                'alternatives' => [
                    'Hello world!',
                ],
                'detectedLanguage' => [
                    'confidence' => 0.5,
                    'language' => 'en',
                ],
            ],
            $body,
        );
    }
}
