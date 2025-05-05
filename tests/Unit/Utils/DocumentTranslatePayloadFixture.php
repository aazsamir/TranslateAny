<?php

declare(strict_types=1);

namespace Tests\Unit\Utils;

use App\Engine\DocumentTranslatePayload;
use App\System\Language;
use Laminas\Diactoros\UploadedFile;
use Tempest\Http\Upload;

class DocumentTranslatePayloadFixture
{
    public static function get(): DocumentTranslatePayload
    {
        return new DocumentTranslatePayload(
            file: new Upload(
                new UploadedFile(
                    streamOrFile: __DIR__ . '/../../Mock/file',
                    size: null,
                    errorStatus: 0,
                ),
            ),
            targetLanguage: Language::pl,
        );
    }
}
