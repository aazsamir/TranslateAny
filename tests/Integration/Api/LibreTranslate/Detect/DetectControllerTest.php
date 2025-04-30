<?php

declare(strict_types=1);

namespace Tests\Integration\Api\LibreTranslate\Detect;

use Tests\Integration\TestCase;

class DetectControllerTest extends TestCase
{
    public function test(): void
    {
        $response = $this->http->post('/libre/detect', [
            'q' => 'Hello world!',
        ]);

        $response->assertOk();
        $body = $response->body;

        $this->assertIsArray($body);
        $this->assertSame(
            [
                [
                    'confidence' => 0.5,
                    'language' => 'en',
                ],
            ],
            $body,
        );
    }
}
