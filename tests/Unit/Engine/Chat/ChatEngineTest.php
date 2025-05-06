<?php

declare(strict_types=1);

namespace Tests\Unit\Engine\Chat;

use App\Engine\AvailableLanguage;
use App\Engine\Chat\ChatEngine;
use App\System\Logger\MemoryLogger;
use Tests\Mock\ChatClientMock;
use Tests\Mock\GlossaryRepositoryMock;
use Tests\TestCase;
use Tests\Unit\Utils\TranslatePayloadFixture;

class ChatEngineTest extends TestCase
{
    private ChatClientMock $client;
    private GlossaryRepositoryMock $glossaryRepository;
    private ChatEngine $engine;

    protected function setUp(): void
    {
        $this->client = new ChatClientMock();
        $this->glossaryRepository = new GlossaryRepositoryMock();
        $this->engine = new ChatEngine(
            client: $this->client,
            systemPrompt: 'system',
            glossaryPrompt: 'glossary',
            logger: new MemoryLogger(),
            glossaryRepository: $this->glossaryRepository,
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

    public function testTranslateWithGlossary(): void
    {
        $payload = TranslatePayloadFixture::get(
            glossaryId: '1',
        );

        $this->engine->translate($payload);
        $messages = $this->client->gotMessages;

        $this->assertCount(2, $messages);
        $message = $messages[0];
        $this->assertEquals('system', $message->role);
        $this->assertEquals(
            "system glossary\n- hello => hej",
            $message->content,
        );
    }

    public function testLanguages(): void
    {
        $languages = $this->engine->languages();

        $this->assertNotEmpty($languages);
        $item = $languages[0];
        $this->assertInstanceOf(AvailableLanguage::class, $item);
    }
}
