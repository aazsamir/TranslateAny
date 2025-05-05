<?php

declare(strict_types=1);

namespace App\Engine\OpenAI;

use App\Engine\AvailableLanguage;
use App\Engine\TranslateEngine;
use App\Engine\TranslatePayload;
use App\Engine\Translation;
use App\System\Chat\TextTrimmer;
use App\System\Glossary\GlossaryRepository;
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
        private Logger $logger,
        private GlossaryRepository $glossaryRepository,
        private string $model,
        private ?string $systemPrompt = null,
        private ?string $glossaryPrompt = null,
        private ?float $temperature = null,
        private ?float $topP = null,
        private ?float $frequencyPenalty = null,
    ) {
    }

    public static function new(
        string $host = 'https://api.openai.com/v1',
        string $model = 'gpt-3.5-turbo',
        ?string $systemPrompt = 'You are an automated translation system. Translate text to the target language. Do not add any additional information or context, just the translation.',
        ?string $glossaryPrompt = 'User provided glossary, use words from it if possible:',
        ?string $apiKey = null,
        ?float $temperature = null,
        ?float $topP = null,
        ?float $frequencyPenalty = null,
    ): self {
        $lazy = new ReflectionClass(self::class);
        $lazy = $lazy->newLazyGhost(
            function (OpenAIEngine $object) use ($host, $model, $systemPrompt, $glossaryPrompt, $apiKey, $temperature, $topP, $frequencyPenalty) {
                $object->__construct(
                    client: ClientFactory::make(
                        host: $host,
                        apiKey: $apiKey,
                    ),
                    model: $model,
                    systemPrompt: $systemPrompt,
                    glossaryPrompt: $glossaryPrompt,
                    temperature: $temperature,
                    topP: $topP,
                    frequencyPenalty: $frequencyPenalty,
                    logger: get(Logger::class),
                    glossaryRepository: get(GlossaryRepository::class),
                );
            },
        );

        return $lazy;
    }

    public function translate(TranslatePayload $payload): Translation
    {
        $messages = [];
        $systemPrompt = $this->systemPrompt;

        if ($payload->glossaryId) {
            $this->logger->debug(
                $this->prefixLog(
                    'OpenAI',
                    'using glossary',
                ),
                [
                    'glossaryId' => $payload->glossaryId,
                ],
            );
            $glossary = $this->glossaryRepository->get($payload->glossaryId);
            $glossaryPrompt = $this->glossaryPrompt;

            foreach ($glossary->entries as $source => $target) {
                $glossaryPrompt .= "\n- $source => $target";
            }

            $systemPrompt .= ' ' . $glossaryPrompt;
        }

        if ($systemPrompt) {
            $messages[] = [
                'role' => 'system',
                'content' => $systemPrompt,
            ];
        }

        $messages[] = [
            'role' => 'user',
            'content' => 'translate to ' . $payload->targetLanguage->name . ' language:\n' . $payload->text,
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
            text: TextTrimmer::trim($choice),
        );
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
