<?php

declare(strict_types=1);

namespace Tests\Integration\Api\DeepLX\Translate;

use Tests\Integration\TestCase;

class TranslateControllerTest extends TestCase
{
    public function test(): void
    {
        $response = $this->http->post(
            '/deeplx/translate',
            [
                'text' => 'Hello world',
                'target_lang' => 'PL',
                'source_lang' => 'EN',
            ],
            headers: [
                'Authorization' => 'Bearer test',
            ],
        );

        $response->assertOk();

        $this->assertEquals(['Hello world'], $response->body['alternatives']);
        $this->assertEquals(200, $response->body['code']);
        $this->assertEquals('Hello world', $response->body['data']);
        $this->assertNotNull($response->body['id']);
        $this->assertEquals('Free', $response->body['method']);
        $this->assertEquals('EN', $response->body['source_lang']);
        $this->assertEquals('PL', $response->body['target_lang']);
    }
}
