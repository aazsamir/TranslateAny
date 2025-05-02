<?php

declare(strict_types=1);

namespace App\Engine;

use App\System\Language;

readonly class Detection
{
    public function __construct(
        public Language $language,
        public float $confidence,
    ) {
    }
}
