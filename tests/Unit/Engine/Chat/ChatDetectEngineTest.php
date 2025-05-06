<?php

declare(strict_types=1);

namespace Tests\Unit\Engine\OpenAI;

use App\Engine\Chat\ChatDetectEngine;
use App\System\Language;
use App\System\Logger\MemoryLogger;
use Tests\Mock\ChatClientMock;
use Tests\TestCase;

class ChatDetectEngineTest extends TestCase
{
    private ChatClientMock $client;
    private ChatDetectEngine $engine;

    protected function setUp(): void
    {
        $this->client = new ChatClientMock();
        $this->engine = new ChatDetectEngine(
            client: $this->client,
            systemPrompt: 'test',
            logger: new MemoryLogger(),
        );
    }

    public function testDetect(): void
    {
        $this->client->response = 'This text is in English.';
        $result = $this->engine->detect('Hello world!');

        $this->assertCount(1, $result);
        $detected = $result[0];
        $this->assertEquals(Language::en, $detected->language);
        $this->assertEquals(1.0, $detected->confidence);
    }

    public function testDetectFromThinkingTags(): void
    {
        $this->client->response = '<think>This may be both English and Polish</think> I dont know.';

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
