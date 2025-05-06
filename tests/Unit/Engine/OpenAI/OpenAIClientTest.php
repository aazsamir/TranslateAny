<?php

declare(strict_types=1);

namespace Tests\Unit\Engine\OpenAI;

use App\Engine\Chat\ChatMessage;
use App\Engine\OpenAI\OpenAIClient;
use OpenAI\Resources\Chat;
use OpenAI\Responses\Chat\CreateResponse;
use OpenAI\Testing\ClientFake;
use Tests\TestCase;

class OpenAIClientTest extends TestCase
{
    private ClientFake $clientFake;
    private OpenAIClient $client;

    protected function setUp(): void
    {
        $this->clientFake = new ClientFake();
        $this->client = new OpenAIClient(
            client: $this->clientFake,
            model: 'gpt-3.5-turbo',
            temperature: 0.7,
            topP: 1.0,
            frequencyPenalty: 0.2,
        );
    }

    public function testChat(): void
    {
        $this->clientFake->addResponses([CreateResponse::fake()]);

        $response = $this->client->chat([
            new ChatMessage(
                role: 'user',
                content: 'Hello world!',
            ),
        ]);

        $this->assertStringContainsString('Hello there, this is a fake chat response.', $response);

        $this->clientFake->assertSent(Chat::class);
    }
}
