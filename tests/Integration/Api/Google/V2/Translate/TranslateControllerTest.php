<?php

declare(strict_types=1);

namespace Tests\Integration\Api\Google\V2\Translate;

use Tests\Integration\TestCase;

class TranslateControllerTest extends TestCase
{
    public function test(): void
    {
        $response = $this->http->post('/google/v2/language/translate/v2', [
            'q' => 'Hello world!',
            'source' => 'auto',
            'target' => 'pl',
        ]);

        $response->assertOk();
        $body = $response->body;

        $this->assertIsArray($body);
        $this->assertSame(
            [
                'data' => [
                    'translations' => [
                        [
                            'translatedText' => 'Hello world!',
                            'detectedSourceLanguage' => 'en',
                        ],
                        [
                            'translatedText' => 'Hello world!',
                            'detectedSourceLanguage' => 'en',
                        ],
                    ],
                ],
            ],
            $body,
        );
    }
}
