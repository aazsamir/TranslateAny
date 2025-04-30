<?php

declare(strict_types=1);

namespace Tests\Integration\Api\LibreTranslate\Languages;

use Tests\Integration\TestCase;

class LanguagesControllerTest extends TestCase
{
    public function test(): void
    {
        $response = $this->http->get('/libre/languages');

        $response->assertOk();
        $body = $response->body;

        $this->assertIsArray($body);
        $this->assertSame(
            [
                [
                    'code' => 'en',
                    'name' => 'English',
                    'targets' => [
                        'pl',
                    ],
                ],
                [
                    'code' => 'pl',
                    'name' => 'Polish',
                    'targets' => [
                        'en',
                    ],
                ],
            ],
            $body,
        );
    }
}
