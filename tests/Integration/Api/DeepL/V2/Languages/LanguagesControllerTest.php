<?php

declare(strict_types=1);

namespace Tests\Integration\Api\DeepL\V2\Languages;

use Tests\Integration\TestCase;

class LanguagesControllerTest extends TestCase
{
    public function test(): void
    {
        $response = $this->http->get('/deepl/v2/languages');

        $response->assertOk();
        $body = $response->body;

        $this->isArray($body);
        $this->assertSame(
            [
                [
                    'language' => 'EN',
                    'name' => 'English',
                    'supports_formality' => false,
                ],
                [
                    'language' => 'PL',
                    'name' => 'Polish',
                    'supports_formality' => false,
                ],
            ],
            $body,
        );
    }
}
