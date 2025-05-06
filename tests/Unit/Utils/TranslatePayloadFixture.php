<?php

declare(strict_types=1);

namespace Tests\Unit\Utils;

use App\Engine\TranslatePayload;
use App\System\Language;

class TranslatePayloadFixture
{
    public static function get(
        string $text = 'Hello world!',
        Language $targetLanguage = Language::pl,
        ?Language $sourceLanguage = Language::en,
        ?string $format = null,
        ?int $alternatives = null,
        ?string $glossaryId = null,
    ): TranslatePayload {
        return new TranslatePayload(
            text: $text,
            targetLanguage: $targetLanguage,
            sourceLanguage: $sourceLanguage,
            format: $format,
            alternatives: $alternatives,
            glossaryId: $glossaryId,
        );
    }
}
