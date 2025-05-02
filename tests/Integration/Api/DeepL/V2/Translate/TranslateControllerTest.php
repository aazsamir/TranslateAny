<?php

declare(strict_types=1);

namespace Tests\Integration\Api\DeepL\V2\Languages;

use Tests\Integration\TestCase;

class TranslateControllerTest extends TestCase
{
    public function test(): void
    {
        $response = $this->http->post('/deepl/v2/translate', [
            'text' => [
                'Hello, world!',
            ],
            'source_lang' => 'en',
            'target_lang' => 'pl',
        ]);

        $response->assertOk();
        $body = $response->body;

        $this->isArray($body);
        $this->assertSame(
            [
                'translations' => [
                    [
                        'text' => 'Hello, world!',
                        'detected_source_language' => 'EN',
                        'billed_characters' => 0,
                    ],
                ],
            ],
            $body,
        );
    }
}
