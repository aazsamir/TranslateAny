<?php

declare(strict_types=1);

namespace App\Engine\Noop;

use App\Engine\DetectionEngine;

class NoopDetectionEngine implements DetectionEngine
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
