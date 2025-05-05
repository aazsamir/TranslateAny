<?php

declare(strict_types=1);

namespace App\Engine\OpenAI;

use App\Engine\AvailableLanguage;
use App\Engine\TranslateEngine;
use App\Engine\TranslatePayload;
use App\Engine\Translation;
use App\System\Language;
use App\System\Logger\PrefixLogger;
use OpenAI\Contracts\ClientContract;
use ReflectionClass;
use Tempest\Log\Logger;

use function Tempest\get;

readonly class OpenAIEngine implements TranslateEngine
{
    use PrefixLogger;

    public function __construct(
        private ClientContract $client,
        private string $model,
        private Logger $logger,
        private ?string $systemPrompt = null,
        private ?float $temperature = null,
        private ?float $topP = null,
        private ?float $frequencyPenalty = null,
    ) {
    }

    public static function new(
        string $host = 'https://api.openai.com/v1',
        string $model = 'gpt-3.5-turbo',
        ?string $systemPrompt = null,
        ?string $apiKey = null,
        ?float $temperature = null,
        ?float $topP = null,
        ?float $frequencyPenalty = null,
    ): self {
        $lazy = new ReflectionClass(self::class);
        $lazy = $lazy->newLazyGhost(
            function (OpenAIEngine $object) use ($host, $model, $systemPrompt, $apiKey, $temperature, $topP, $frequencyPenalty) {
                $object->__construct(
                    client: ClientFactory::make(
                        host: $host,
                        apiKey: $apiKey,
                    ),
                    model: $model,
                    systemPrompt: $systemPrompt,
                    temperature: $temperature,
                    topP: $topP,
                    frequencyPenalty: $frequencyPenalty,
                    logger: get(Logger::class),
                );
            },
        );

        return $lazy;
    }

    public function translate(TranslatePayload $payload): Translation
    {
        $messages = [];

        if ($this->systemPrompt) {
            $messages[] = [
                'role' => 'system',
                'content' => $this->systemPrompt,
            ];
        }

        $messages[] = [
            'role' => 'user',
            'content' => 'translate to ' . $payload->targetLanguage->name . ':\n' . $payload->text,
        ];

        $this->logger->debug(
            $this->prefixLog(
                'OpenAI',
                'chat translate',
            ),
            [
                'messages' => $messages,
            ],
        );

        $request = [
            'model' => $this->model,
            'messages' => $messages,
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

        if ($payload->alternatives) {
            $request['n'] = $payload->alternatives;
        }

        $response = $this->client->chat()->create($request);

        if (! isset($response->choices[0])) {
            throw new \RuntimeException('No message in response!');
        }

        $choice = $response->choices[0];
        $choice = $choice->message->content;

        $this->logger->debug(
            $this->prefixLog(
                'OpenAI',
                'chat translated',
            ),
            [
                'choice' => $choice,
            ],
        );

        return new Translation(
            text: $this->formatText($choice),
        );
    }

    private function formatText(?string $text): string
    {
        if ($text === null) {
            return '';
        }

        $text = preg_replace('/<think>.*?<\/think>/s', '', $text) ?? '';
        $text = preg_replace('/<thinking>.*?<\/thinking>/s', '', $text) ?? '';
        $text = trim($text);

        return $text;
    }

    public function languages(): array
    {
        $languages = [];

        foreach (Language::cases() as $language) {
            $languages[] = new AvailableLanguage(
                language: $language,
                targets: Language::cases(),
            );
        }

        return $languages;
    }
}
