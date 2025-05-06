<?php

declare(strict_types=1);

namespace Tests\Unit\Engine\Chat;

use App\Engine\Chat\ChatEngine;
use Tests\Mock\ChatClientMock;
use Tests\Mock\GlossaryRepositoryMock;
use Tests\Mock\NullLogger;
use Tests\TestCase;
use Tests\Unit\Utils\TranslatePayloadFixture;

class ChatEngineTest extends TestCase
{
    private ChatClientMock $client;
    private ChatEngine $engine;

    protected function setUp(): void
    {
        $this->client = new ChatClientMock();
        $this->engine = new ChatEngine(
            client: $this->client,
            systemPrompt: 'test',
            glossaryPrompt: 'test',
            logger: new NullLogger(),
            glossaryRepository: new GlossaryRepositoryMock(),
        );
    }

    public function testTranslate(): void
    {
        $translation = $this->engine->translate(TranslatePayloadFixture::get());

        $this->assertEquals(
            'Hello there, this is a fake chat response.',
            $translation->text,
        );
    }

    public function testThinkTagsRemoval(): void
    {
        $this->client->response = "<think>Let's think</think>\nHello world!";

        $translation = $this->engine->translate(TranslatePayloadFixture::get());

        $this->assertEquals('Hello world!', $translation->text);
    }
}
