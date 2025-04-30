<?php

declare(strict_types=1);

namespace Tests\Mock;

use App\Engine\AvailableLanguage;
use App\Engine\Detection;
use App\Engine\TranslateEngine;
use App\Engine\Translation;

/**
 * @internal
 */
class TranslateEngineMock implements TranslateEngine
{
    public ?Translation $translation = null;

    public function translate(string $text, string $targetLanguage, ?string $sourceLanguage = null, ?string $format = null, ?int $alternatives = null): Translation
    {
        if ($this->translation !== null) {
            return $this->translation;
        }

        return new Translation(
            text: $text,
            alternatives: [
                $text,
            ],
            detectedLanguage: new Detection(
                language: 'en',
                confidence: 0.5,
            ),
        );
    }

    public function languages(): array
    {
        return [
            new AvailableLanguage(
                language: 'en',
                targets: ['pl'],
            ),
            new AvailableLanguage(
                language: 'pl',
                targets: ['en'],
            ),
        ];
    }
}
