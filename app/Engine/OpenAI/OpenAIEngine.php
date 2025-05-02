<?php

declare(strict_types=1);

namespace App\Engine\OpenAI;

use App\Engine\AvailableLanguage;
use App\Engine\Languages;
use App\Engine\TranslateEngine;
use App\Engine\TranslatePayload;
use App\Engine\Translation;
use App\System\Language;
use OpenAI\Contracts\ClientContract;

readonly class OpenAIEngine implements TranslateEngine
{
    public function __construct(
        private ClientContract $client,
        private string $model,
        private ?string $systemPrompt = null,
    ) {
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
        $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => $messages,
            ]);

        if (! isset($response->choices[0])) {
            return new Translation(
                text: '',
            );
        }

        $choice = $response->choices[0];
        $choice = $choice->message->content;

        return new Translation(
            text: $this->formatText($choice),
        );
    }

    private function formatText(string $text): string
    {
        $text = preg_replace('/<think>.*?<\/think>/s', '', $text);
        $text = preg_replace('/<thinking>.*?<\/thinking>/s', '', $text);
        $text = trim($text);

        return $text;
    }

    public function languages(): array
    {
        $languages = [];

        foreach (Languages::LANGUAGES as $code => $name) {
            $languages[] = new AvailableLanguage(
                language: $code,
                targets: Languages::getLanguages(),
            );
        }

        return $languages;
    }
}
