<?php

declare(strict_types=1);

namespace App\Engine;

use App\System\Language;

readonly class TranslatePayload
{
    public function __construct(
        public string $text,
        public Language $targetLanguage,
        public ?Language $sourceLanguage = null,
        public ?string $format = null,
        public ?int $alternatives = null,
    ) {
    }
}
