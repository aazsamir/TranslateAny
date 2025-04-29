<?php

declare(strict_types=1);

namespace App\Engine\Noop;

use App\Engine\TranslateEngine;
use App\Engine\Translation;

class NoopTranslateEngine implements TranslateEngine
{
    public function translate(
        string $text,
        string $targetLanguage,
        ?string $sourceLanguage = null,
        ?string $format = null,
        ?int $alternatives = null,
    ): Translation {
        return new Translation(
            text: $text,
        );
    }

    public function languages(): array
    {
        return [];
    }
}
