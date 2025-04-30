<?php

declare(strict_types=1);

namespace Tests\Unit\Engine\OpenAI;

use App\Engine\OpenAI\OpenAIEngine;
use OpenAI\Responses\Chat\CreateResponse;
use OpenAI\Testing\ClientFake;
use Tests\TestCase;

class OpenAIEngineTest extends TestCase
{
    private ClientFake $openai;
    private OpenAIEngine $engine;

    protected function setUp(): void
    {
        $this->openai = new ClientFake();
        $this->engine = new OpenAIEngine(
            client: $this->openai,
            model: 'test:1.b',
            systemPrompt: 'test',
        );
    }

    public function testTranslate(): void
    {
        $this->openai->addResponses([CreateResponse::fake()]);
        $translation = $this->engine->translate(
            text: 'Hello world!',
            targetLanguage: 'en',
            sourceLanguage: 'pl',
        );

        $this->assertEquals(
            'Hello there, this is a fake chat response.',
            $translation->text,
        );
    }

    public function testThinkTagsRemoval(): void
    {
        $this->openai->addResponses([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => "<think>Let's think</think>\nHello world!",
                        ],
                    ],
                ],
            ]),
        ]);

        $translation = $this->engine->translate(
            text: 'Hello world!',
            targetLanguage: 'en',
        );

        $this->assertEquals('Hello world!', $translation->text);
    }
}
