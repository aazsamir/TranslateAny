<?php

declare(strict_types=1);

namespace App\Engine\Chat;

use App\Engine\AvailableLanguage;
use App\Engine\TranslateEngine;
use App\Engine\TranslatePayload;
use App\Engine\Translation;
use App\System\Chat\Prompter;
use App\System\Chat\TextTrimmer;
use App\System\Glossary\GlossaryRepository;
use App\System\Logger\PrefixLogger;
use Tempest\Log\Logger;

use function Tempest\get;

class ChatEngine implements TranslateEngine
{
    use PrefixLogger;

    public function __construct(
        private ChatClient $client,
        private Logger $logger,
        private GlossaryRepository $glossaryRepository,
        private string $systemPrompt,
        private string $glossaryPrompt,
    ) {
    }

    public static function new(
        ChatClient $client,
        string $systemPrompt = 'You are an automated translation system. Translate text to the target language. Do not add any additional information or context, just the translation.',
        string $glossaryPrompt = 'User provided glossary, use words from it if possible:',
    ): self {
        $lazy = new \ReflectionClass(self::class);
        $lazy = $lazy->newLazyGhost(function (ChatEngine $object) use ($client, $systemPrompt, $glossaryPrompt) {
            $object->__construct(
                client: $client,
                systemPrompt: $systemPrompt,
                glossaryPrompt: $glossaryPrompt,
                logger: get(Logger::class),
                glossaryRepository: get(GlossaryRepository::class),
            );
        });

        return $lazy;
    }

    public function translate(TranslatePayload $payload): Translation
    {
        $messages = [];
        $messages[] = new ChatMessage(
            role: 'system',
            content: Prompter::systemWithGlossary(
                systemPrompt: $this->systemPrompt,
                context: $payload->context,
                glossaryPrompt: $this->glossaryPrompt,
                glossary: $this->glossaryRepository->get($payload->glossaryId),
            ),
        );
        $messages[] = new ChatMessage(
            role: 'user',
            content: Prompter::translatePrompt($payload),
        );

        $content = $this->client->chat($messages);

        $this->logger->debug(
            $this->prefixLog(
                'Chat',
                'translate',
            ),
            [
                'messages' => $messages,
                'response' => $content,
            ],
        );

        return new Translation(
            text: TextTrimmer::trim($content),
        );
    }

    public function languages(): array
    {
        return AvailableLanguage::all();
    }
}
