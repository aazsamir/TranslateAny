<?php

declare(strict_types=1);

namespace App\Engine;

readonly class Detection
{
    public function __construct(
        public string $language,
        public float $confidence,
    ) {
    }
}
