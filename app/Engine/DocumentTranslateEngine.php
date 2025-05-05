<?php

declare(strict_types=1);

namespace App\Engine;

interface DocumentTranslateEngine
{
    public function translateDocument(DocumentTranslatePayload $payload): DocumentTranslation;

    public function downloadPath(DocumentTranslation $document): string;
}
