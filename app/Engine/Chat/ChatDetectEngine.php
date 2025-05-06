<?php

declare(strict_types=1);

namespace App\Engine\Chat;

use App\Engine\DetectEngine;
use App\Engine\Detection;
use App\System\Chat\Prompter;
use App\System\Chat\TextTrimmer;
use App\System\Language;
use App\System\Logger\PrefixLogger;
use Tempest\Log\Logger;

use function Tempest\get;

class ChatDetectEngine implements DetectEngine
{
    use PrefixLogger;

    public function __construct(
        private ChatClient $client,
        private string $systemPrompt,
        private Logger $logger,
    ) {
    }

    public static function new(
        ChatClient $client,
        string $systemPrompt = 'You are an automated language detection system. Respond with the language name detected in the text.',
    ): self {
        $lazy = new \ReflectionClass(self::class);
        $lazy = $lazy->newLazyGhost(
            function (ChatDetectEngine $object) use ($client, $systemPrompt) {
                $object->__construct(
                    client: $client,
                    systemPrompt: $systemPrompt,
                    logger: get(Logger::class),
                );
            },
        );

        return $lazy;
    }

    public function detect(string $text): array
    {
        $messages = [];
        $messages[] = new ChatMessage(
            role: 'system',
            content: $this->systemPrompt,
        );
        $messages[] = new ChatMessage(
            role: 'user',
            content: Prompter::detectPrompt($text),
        );

        $message = $this->client->chat($messages);

        $this->logger->debug(
            $this->prefixLog(
                'ChatDetect',
                'detect',
            ),
            [
                'messages' => $messages,
                'response' => $message,
            ],
        );

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
