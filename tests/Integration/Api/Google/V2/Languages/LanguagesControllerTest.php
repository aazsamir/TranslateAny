<?php

declare(strict_types=1);

namespace Tests\Integration\Api\Google\V2\Languages;

use Tests\Integration\TestCase;

class LanguagesControllerTest extends TestCase
{
    public function test(): void
    {
        $response = $this->http->get(
            '/google/v2/language/translate/v2/languages',
            headers: [
                'Authorization' => 'Bearer test',
            ],
        );

        $response->assertOk();
        $body = $response->body;

        $this->isArray($body);
        $this->assertSame(
            [
                'data' => [
                    'languages' => [
                        [
                            'language' => 'en',
                            'name' => 'English',
                        ],
                        [
                            'language' => 'pl',
                            'name' => 'Polish',
                        ],
                    ],
                ],
            ],
            $body,
        );
    }
}
