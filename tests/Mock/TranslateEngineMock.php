<?php

declare(strict_types=1);

namespace Tests\Mock;

use App\Engine\AvailableLanguage;
use App\Engine\Detection;
use App\Engine\TranslateEngine;
use App\Engine\TranslatePayload;
use App\Engine\Translation;
use App\System\Language;

/**
 * @internal
 */
class TranslateEngineMock implements TranslateEngine
{
    public ?Translation $translation = null;

    public function translate(TranslatePayload $payload): Translation
    {
        if ($this->translation !== null) {
            return $this->translation;
        }

        return new Translation(
            text: $payload->text,
            alternatives: [
                $payload->text,
            ],
            detectedLanguage: new Detection(
                language: Language::en,
                confidence: 0.5,
            ),
        );
    }

    public function languages(): array
    {
        return [
            new AvailableLanguage(
                language: Language::en,
                targets: [Language::pl],
            ),
            new AvailableLanguage(
                language: Language::pl,
                targets: [Language::en],
            ),
        ];
    }
}
