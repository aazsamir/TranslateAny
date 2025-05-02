<?php

declare(strict_types=1);

namespace App\Engine;

interface TranslateEngine
{
    public function translate(TranslatePayload $payload): Translation;

    /**
     * @return AvailableLanguage[]
     */
    public function languages(): array;
}
