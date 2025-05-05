<?php

declare(strict_types=1);

namespace Tests\Unit\Engine\OpenAI;

use App\Engine\OpenAI\OpenAIDetectEngine;
use App\System\Language;
use OpenAI\Responses\Chat\CreateResponse;
use OpenAI\Testing\ClientFake;
use Tests\Mock\NullLogger;
use Tests\TestCase;

class OpenAIDetectEngineTest extends TestCase
{
    private ClientFake $client;
    private OpenAIDetectEngine $engine;

    protected function setUp(): void
    {
        $this->client = new ClientFake();
        $this->engine = new OpenAIDetectEngine(
            client: $this->client,
            model: 'test:1.b',
            systemPrompt: 'test',
            logger: new NullLogger(),
        );
    }

    public function testDetect(): void
    {
        $this->client->addResponses([CreateResponse::fake([
            'choices' => [
                [
                    'message' => [
                        'content' => 'This text is in English.',
                    ],
                ],
            ],
        ])]);

        $result = $this->engine->detect('Hello world!');

        $this->assertCount(1, $result);
        $detected = $result[0];
        $this->assertEquals(Language::en, $detected->language);
        $this->assertEquals(1.0, $detected->confidence);
    }

    public function testDetectFromThinkingTags(): void
    {
        $this->client->addResponses([CreateResponse::fake([
            'choices' => [
                [
                    'message' => [
                        'content' => '<think>This may be both English and Polish</think> I dont know.',
                    ],
                ],
            ],
        ])]);

        $result = $this->engine->detect('Hello world!');

        $this->assertCount(2, $result);
        $english = $result[0];
        $polish = $result[1];

        $this->assertEquals(Language::en, $english->language);
        $this->assertEquals(0.5, $english->confidence);
        $this->assertEquals(Language::pl, $polish->language);
        $this->assertEquals(0.5, $polish->confidence);
    }
}
