<?php

declare(strict_types=1);

namespace App\Engine;

interface TranslateEngine
{
    public function translate(
        string $text,
        string $targetLanguage,
        ?string $sourceLanguage = null,
        ?string $format = null,
        ?int $alternatives = null,
    ): Translation;

    /**
     * @return AvailableLanguage[]
     */
    public function languages(): array;
}
