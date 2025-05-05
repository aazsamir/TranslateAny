<?php

declare(strict_types=1);

namespace Tests\Mock;

use App\Engine\DocumentTranslateEngine;
use App\Engine\DocumentTranslatePayload;
use App\Engine\DocumentTranslation;

class DocumentTranslateMock implements DocumentTranslateEngine
{
    public function translateDocument(DocumentTranslatePayload $payload): DocumentTranslation
    {
        return new DocumentTranslation(
            id: '123',
        );
    }

    public function downloadPath(DocumentTranslation $document): string
    {
        return \realpath(__DIR__ . '/file');
    }
}
