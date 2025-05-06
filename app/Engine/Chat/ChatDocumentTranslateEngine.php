<?php

declare(strict_types=1);

namespace App\Engine\Chat;

use App\Engine\DocumentTranslateEngine;
use App\Engine\DocumentTranslatePayload;
use App\Engine\DocumentTranslation;
use App\System\Document\DocumentStorage;
use App\System\Document\TextExtractor;
use App\System\Logger\PrefixLogger;
use ReflectionClass;
use Tempest\Log\Logger;

use function Tempest\get;

readonly class ChatDocumentTranslateEngine implements DocumentTranslateEngine
{
    use PrefixLogger;

    public function __construct(
        private ChatClient $client,
        private TextExtractor $textExtractor,
        private DocumentStorage $documentStorage,
        private Logger $logger,
        private string $systemPrompt,
    ) {
    }

    public static function new(
        ChatClient $client,
        string $systemPrompt = 'You are an automated translation system. Translate text to the target language. Do not add any additional information or context, just the translation. You will be given text from file extraction. Keep document layout untouched.',
    ): self {
        $lazy = new ReflectionClass(self::class);
        $lazy = $lazy->newLazyGhost(function (ChatDocumentTranslateEngine $object) use ($client, $systemPrompt) {
            $object->__construct(
                client: $client,
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
        $messages = [];
        $systemMessage = new ChatMessage(
            role: 'system',
            content: $this->systemPrompt,
        );

        $translated = [];
        $lastUserMessage = null;
        $lastAssistantMessage = null;

        foreach ($pages as $i => $page) {
            $this->logger->debug(
                $this->prefixLog(
                    'ChatDocumentTranslate',
                    'translate',
                ),
                [
                    'page' => $i,
                ],
            );
            $messages = [];
            $messages[] = $systemMessage;

            if ($lastUserMessage !== null && $lastAssistantMessage !== null) {
                $messages[] = $lastUserMessage;
                $messages[] = $lastAssistantMessage;
            }

            $message = new ChatMessage(
                role: 'user',
                content: 'Translate to ' . $payload->targetLanguage->value . " language:\n" . $page,
            );
            $messages[] = $message;
            $lastUserMessage = $message;

            $response = $this->client->chat($messages);
            $translated[] = $response;

            $lastAssistantMessage = new ChatMessage(
                role: 'assistant',
                content: $response,
            );
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
