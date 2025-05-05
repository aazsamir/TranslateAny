<?php

declare(strict_types=1);

namespace App\Engine\Noop;

use App\Engine\DocumentTranslateEngine;
use App\Engine\DocumentTranslatePayload;
use App\Engine\DocumentTranslation;

class NoopDocumentTranslateEngine implements DocumentTranslateEngine
{
    public static function new(): self
    {
        return new self();
    }

    public function translateDocument(DocumentTranslatePayload $payload): DocumentTranslation
    {
        return new DocumentTranslation(
            id: '123',
        );
    }

    public function downloadPath(DocumentTranslation $document): string
    {
        return 'placeholder';
    }
}
