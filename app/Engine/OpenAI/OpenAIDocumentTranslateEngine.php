<?php

declare(strict_types=1);

namespace App\Engine\OpenAI;

use App\Engine\DocumentTranslateEngine;
use App\Engine\DocumentTranslatePayload;
use App\Engine\DocumentTranslation;
use App\System\Document\DocumentStorage;
use App\System\Document\TextExtractor;
use App\System\Logger\PrefixLogger;
use OpenAI\Contracts\ClientContract;
use ReflectionClass;
use Tempest\Log\Logger;

use function Tempest\get;

readonly class OpenAIDocumentTranslateEngine implements DocumentTranslateEngine
{
    use PrefixLogger;

    public function __construct(
        private ClientContract $client,
        private string $model,
        private string $systemPrompt,
        private TextExtractor $textExtractor,
        private DocumentStorage $documentStorage,
        private Logger $logger,
    ) {
    }

    public static function new(
        string $host = 'https://api.openai.com/v1',
        string $model = 'gpt-3.5-turbo',
        string $systemPrompt = 'You are an automated translation system. Translate text to the target language. Do not add any additional information or context, just the translation. You will be given text from file extraction. Keep document layout untouched.',
        ?string $apiKey = null,
    ): self {
        $lazy = new ReflectionClass(self::class);
        $lazy = $lazy->newLazyGhost(function (OpenAIDocumentTranslateEngine $object) use ($host, $model, $systemPrompt, $apiKey) {
            $object->__construct(
                client: ClientFactory::make(
                    host: $host,
                    apiKey: $apiKey,
                ),
                model: $model,
                systemPrompt: $systemPrompt,
                textExtractor: new TextExtractor(),
                documentStorage: new DocumentStorage(),
                logger: get(Logger::class),
            );
        });

        return $lazy;
    }

    /**
     * @todo This naive implementation of document translation results in low quality translation. Treat it as a placeholder.
     */
    public function translateDocument(DocumentTranslatePayload $payload): DocumentTranslation
    {
        $pages = $this->textExtractor->extract($payload->file);
        $systemMessage = [
            'role' => 'system',
            'content' => $this->systemPrompt,
        ];

        $translated = [];
        $userHistory = [];
        $assistantHistory = [];

        foreach ($pages as $i => $page) {
            $messages = [];
            $messages[] = $systemMessage;

            $message = [
                'role' => 'user',
                'content' => 'page to translate to ' . $payload->targetLanguage->name . ':\n' . $page,
            ];

            if (isset($userHistory[$i - 1])) {
                $messages[] = $userHistory[$i - 1];
                $messages[] = $assistantHistory[$i - 1];
            }

            $messages[] = $message;
            $userHistory[] = $message;

            $this->logger->debug(
                $this->prefixLog(
                    'OpenAIDocument',
                    'translateDocument',
                ),
                [
                    'page' => $i,
                    'messages' => $messages,
                ],
            );

            $response = $this->client->chat()->create([
                    'model' => $this->model,
                    'messages' => $messages,
                ]);

            if (! isset($response->choices[0])) {
                throw new \RuntimeException('No response!');
            }

            $choice = $response->choices[0];
            $choice = $choice->message->content;

            $this->logger->debug(
                $this->prefixLog(
                    'OpenAIDocument',
                    'translateDocument',
                ),
                [
                    'response' => $choice,
                ],
            );

            $translated[] = $choice ?? '';
            $assistantHistory[] = [
                'role' => 'assistant',
                'content' => $choice,
            ];
        }

        $fileid = $this->documentStorage->storeTranslated($translated);

        return new DocumentTranslation(
            id: $fileid,
        );
    }

    public function downloadPath(DocumentTranslation $document): string
    {
        return $this->documentStorage->downloadPath($document->id);
    }
}
