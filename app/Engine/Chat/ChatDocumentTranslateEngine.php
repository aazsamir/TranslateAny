<?php

declare(strict_types=1);

namespace App\Engine\Chat;

use App\Engine\DocumentTranslateEngine;
use App\Engine\DocumentTranslatePayload;
use App\Engine\DocumentTranslation;
use App\System\Chat\TextTrimmer;
use App\System\Document\Chunking\ChunkingStrategyFactory;
use App\System\Document\DocumentStorage;
use App\System\Document\FileDocumentStorage;
use App\System\Document\SimpleTextExtractor;
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
        private ChunkingStrategyFactory $chunkingStrategyFactory,
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
                textExtractor: new SimpleTextExtractor(),
                documentStorage: new FileDocumentStorage(),
                logger: get(Logger::class),
                chunkingStrategyFactory: get(ChunkingStrategyFactory::class),
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
        $chunkingStrategy = $this->chunkingStrategyFactory->create();

        foreach ($chunkingStrategy->chunk($pages) as $i => $chunk) {
            $this->logger->debug(
                $this->prefixLog(
                    'ChatDocumentTranslate',
                    'translate',
                ),
                [
                    'chunk' => $i,
                    'page' => $chunk->page,
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
                content: 'Translate to ' . $payload->targetLanguage->value . " language:\n" . $chunk->text,
            );
            $messages[] = $message;
            $lastUserMessage = $message;

            $response = $this->client->chat($messages);

            $lastAssistantMessage = new ChatMessage(
                role: 'assistant',
                content: $response,
            );
            $translated[] = TextTrimmer::trim($response);
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
