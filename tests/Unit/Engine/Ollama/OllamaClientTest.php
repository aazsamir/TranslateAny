<?php

declare(strict_types=1);

namespace Tests\Unit\Engine\Ollama;

use App\Engine\Chat\ChatMessage;
use App\Engine\Ollama\OllamaClient;
use GuzzleHttp\Psr7\Response;
use Tests\Mock\PsrClientMock;
use Tests\TestCase;

class OllamaClientTest extends TestCase
{
    private PsrClientMock $psrClient;
    private OllamaClient $client;

    protected function setUp(): void
    {
        $this->psrClient = new PsrClientMock();
        $this->client = new OllamaClient(
            client: $this->psrClient,
            model: 'llama2',
            host: 'http://localhost:11434',
        );
    }

    public function testChat(): void
    {
        $this->psrClient->response = new Response(
            body: \json_encode([
                'message' => [
                    'content' => 'Hello there, this is a fake chat response.',
                ],
            ]),
        );

        $result = $this->client->chat([
            new ChatMessage(
                role: 'user',
                content: 'Hello world!',
            ),
        ]);

        $this->assertEquals('Hello there, this is a fake chat response.', $result);
    }
}
