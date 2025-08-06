<?php

declare(strict_types=1);

namespace App\Engine\Ollama;

use App\Engine\Chat\ChatClient;
use App\System\PsrClientFactory;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;

class OllamaClient implements ChatClient
{
    public function __construct(
        private ClientInterface $client,
        private string $model,
        private string $host,
        private ?OllamaSettings $settings = null,
        private ?int $keepAlive = null,
    ) {
    }

    public static function new(
        string $model,
        string $host = 'http://localhost:11434',
        ?OllamaSettings $settings = null,
        ?int $keepAlive = null,
    ): self {
        $lazy = new \ReflectionClass(self::class);
        $lazy = $lazy->newLazyGhost(function (OllamaClient $object) use ($model, $host, $settings, $keepAlive) {
            $object->__construct(
                client: PsrClientFactory::new(),
                model: $model,
                host: $host,
                settings: $settings,
                keepAlive: $keepAlive,
            );
        });

        return $lazy;
    }

    public function chat(array $messages): string
    {
        $requestMessages = [];

        foreach ($messages as $message) {
            $requestMessages[] = [
                'role' => $message->role,
                'content' => $message->content,
            ];
        }

        $request = [
            'model' => $this->model,
            'messages' => $requestMessages,
            'stream' => false,
        ];

        if ($this->settings) {
            $request['settings'] = $this->settings->toArray();
        }

        if ($this->keepAlive) {
            $request['keep_alive'] = $this->keepAlive . 'm';
        }

        $request = new Request(
            method: 'POST',
            uri: $this->host . '/api/chat',
            headers: [
                'Content-Type' => 'application/json',
            ],
            body: json_encode($request, flags: JSON_THROW_ON_ERROR),
        );
        $response = $this->client->sendRequest($request);

        if ($response->getStatusCode() !== 200) {
            throw OllamaException::fromResponse($response);
        }

        /**
         * @var array{
         *  message: array{
         *      content: ?string,
         *  }
         * }
         */
        $response = json_decode($response->getBody()->getContents(), true);
        $content = $response['message']['content'] ?? '';

        return $content;
    }
}
