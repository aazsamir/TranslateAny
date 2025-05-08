<?php

declare(strict_types=1);

namespace Tests\Unit\Utils;

use App\Engine\DocumentTranslatePayload;
use App\System\Language;
use Tests\Mock\UploadMock;

class DocumentTranslatePayloadFixture
{
    public static function get(): DocumentTranslatePayload
    {
        return new DocumentTranslatePayload(
            file: UploadMock::upload(),
            targetLanguage: Language::pl,
        );
    }
}
