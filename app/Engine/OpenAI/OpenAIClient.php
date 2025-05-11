<?php

declare(strict_types=1);

namespace App\Engine\OpenAI;

use App\Engine\Chat\ChatClient;
use OpenAI\Contracts\ClientContract;

class OpenAIClient implements ChatClient
{
    public function __construct(
        private ClientContract $client,
        private string $model,
        private ?float $temperature = null,
        private ?float $topP = null,
        private ?float $frequencyPenalty = null,
    ) {
    }

    public static function new(
        string $model = 'gpt-3.5-turbo',
        string $host = 'https://api.openai.com/v1',
        ?string $apiKey = null,
        ?float $temperature = null,
        ?float $topP = null,
        ?float $frequencyPenalty = null,
    ): self {
        $lazy = new \ReflectionClass(self::class);
        $lazy = $lazy->newLazyGhost(function (OpenAIClient $object) use ($model, $host, $apiKey, $temperature, $topP, $frequencyPenalty) {
            $object->__construct(
                client: ClientFactory::make(
                    host: $host,
                    apiKey: $apiKey,
                ),
                model: $model,
                temperature: $temperature,
                topP: $topP,
                frequencyPenalty: $frequencyPenalty,
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
        ];

        if ($this->temperature) {
            $request['temperature'] = $this->temperature;
        }

        if ($this->topP) {
            $request['top_p'] = $this->topP;
        }

        if ($this->frequencyPenalty) {
            $request['frequency_penalty'] = $this->frequencyPenalty;
        }

        try {
            return $this->client->chat()->create($request)->choices[0]->message->content ?? '';
        } catch (\Throwable $e) {
            throw OpenAIException::fromPrevious($e);
        }
    }
}
