<?php

declare(strict_types=1);

namespace Tests\Unit\Utils;

use App\Engine\TranslatePayload;
use App\System\Language;

class PayloadFixture
{
    public static function get(): TranslatePayload
    {
        return new TranslatePayload(
            text: 'Hello world!',
            targetLanguage: Language::pl,
            sourceLanguage: Language::en,
        );
    }
}
