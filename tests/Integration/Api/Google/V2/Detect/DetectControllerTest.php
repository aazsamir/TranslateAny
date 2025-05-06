<?php

declare(strict_types=1);

namespace Tests\Integration\Api\Google\V2\Detect;

use Tests\Integration\TestCase;

class DetectControllerTest extends TestCase
{
    public function test(): void
    {
        $response = $this->http->post(
            '/google/v2/language/translate/v2/detect',
            [
                'q' => 'Hello world!',
            ],
            headers: [
                'Authorization' => 'Bearer test',
            ],
        );

        $response->assertOk();
        $body = $response->body;

        $this->assertIsArray($body);
        $this->assertSame(
            [
                'data' => [
                    'detections' => [
                        [
                            'language' => 'en',
                            'isReliable' => false,
                            'confidence' => 0.5,
                        ],
                    ],
                ],
            ],
            $body,
        );
    }
}
