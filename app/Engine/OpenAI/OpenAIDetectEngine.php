<?php

declare(strict_types=1);

namespace App\Engine\OpenAI;

use App\Engine\DetectEngine;
use App\Engine\Detection;
use App\System\Chat\TextTrimmer;
use App\System\Language;
use App\System\Logger\PrefixLogger;
use OpenAI\Contracts\ClientContract;
use Tempest\Log\Logger;

use function Tempest\get;

class OpenAIDetectEngine implements DetectEngine
{
    use PrefixLogger;

    public function __construct(
        private ClientContract $client,
        private Logger $logger,
        private string $model,
        private string $systemPrompt,
        private ?float $temperature = null,
        private int $maxTokens = 256,
    ) {
    }

    public static function new(
        string $host = 'https://api.openai.com/v1',
        string $model = 'gpt-3.5-turbo',
        string $systemPrompt = 'You are an automated language detection system. Respond with the language name detected in the text.',
        ?string $apiKey = null,
        ?float $temperature = null,
        int $maxTokens = 256,
    ): self {
        $lazy = new \ReflectionClass(self::class);
        $lazy = $lazy->newLazyGhost(
            function (OpenAIDetectEngine $object) use ($host, $model, $systemPrompt, $temperature, $maxTokens, $apiKey) {
                $object->__construct(
                    client: ClientFactory::make(
                        host: $host,
                        apiKey: $apiKey,
                    ),
                    model: $model,
                    systemPrompt: $systemPrompt,
                    temperature: $temperature,
                    maxTokens: $maxTokens,
                    logger: get(Logger::class),
                );
            },
        );

        return $lazy;
    }

    public function detect(string $text): array
    {
        $request = [
            'model' => $this->model,
            'max_tokens' => $this->maxTokens,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $this->systemPrompt,
                ],
                [
                    'role' => 'user',
                    'content' => 'Detect the language of the following text:\n' . $text,
                ],
            ],
        ];

        if ($this->temperature !== null) {
            $request['temperature'] = $this->temperature;
        }

        $this->logger->debug(
            $this->prefixLog(
                'OpenAIDetect',
                'detecting language',
            ),
            [
                'request' => $request,
            ],
        );
        $response = $this->client->chat()->create($request);
        $message = $response->choices[0]->message->content ?? null;
        $this->logger->debug(
            $this->prefixLog(
                'OpenAIDetect',
                'detected language',
            ),
            [
                'response' => $response,
            ],
        );

        if ($message === null) {
            return [];
        }

        $trimmed = TextTrimmer::trim($message);
        $trimmed = \strtolower($trimmed);

        foreach (Language::cases() as $language) {
            if (\str_contains($trimmed, $language->titleLower())) {
                return [
                    new Detection(
                        language: $language,
                        confidence: 1.0,
                    ),
                ];
            }
        }

        $message = \strtolower($message);
        $languages = [];

        foreach (Language::cases() as $language) {
            if (\str_contains($message, $language->titleLower())) {
                $languages[] = $language;
            }
        }

        $detections = [];

        foreach ($languages as $language) {
            $detections[] = new Detection(
                language: $language,
                confidence: 1.0 / \count($languages),
            );
        }

        return $detections;
    }
}
