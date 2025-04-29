<?php

declare(strict_types=1);

namespace App\System;

use App\Engine\DetectionConfig;
use App\Engine\TranslateConfig;

readonly class AppConfig
{
    public function __construct(
        public ?TranslateConfig $translate = null,
        public ?DetectionConfig $detection = null,
    ) {
    }
}
