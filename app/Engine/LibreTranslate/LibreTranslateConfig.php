<?php

declare(strict_types=1);

namespace App\Engine\LibreTranslate;

use App\Engine\DetectionConfig;
use App\Engine\TranslateConfig;

readonly class LibreTranslateConfig implements TranslateConfig, DetectionConfig
{
    public function __construct(
        public string $url,
    ) {
    }
}
