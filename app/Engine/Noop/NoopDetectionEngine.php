<?php

declare(strict_types=1);

namespace App\Engine\Noop;

use App\Engine\DetectionEngine;

class NoopDetectionEngine implements DetectionEngine
{
    public function detect(string $text): array
    {
        return [];
    }
}
