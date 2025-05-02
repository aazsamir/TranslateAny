<?php

declare(strict_types=1);

namespace App\Engine\Noop;

use App\Engine\TranslateEngine;
use App\Engine\TranslatePayload;
use App\Engine\Translation;
use App\System\Language;

class NoopTranslateEngine implements TranslateEngine
{
    public function translate(TranslatePayload $payload): Translation
    {
        return new Translation(
            text: $payload->text,
        );
    }

    public function languages(): array
    {
        return [];
    }
}
