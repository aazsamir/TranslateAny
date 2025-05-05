<?php

declare(strict_types=1);

namespace App\Engine\Noop;

use App\Engine\DetectEngine;

class NoopDetectEngine implements DetectEngine
{
    public static function new(): self
    {
        return new self();
    }

    public function detect(string $text): array
    {
        return [];
    }
}
