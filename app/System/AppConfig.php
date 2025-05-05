<?php

declare(strict_types=1);

namespace App\System;

use App\Engine\DetectionEngine;
use App\Engine\DocumentTranslateEngine;
use App\Engine\TranslateEngine;

readonly class AppConfig
{
    public function __construct(
        public ?TranslateEngine $translate = null,
        public ?DetectionEngine $detection = null,
        public ?DocumentTranslateEngine $document = null,
        public bool $debug = false,
    ) {
    }
}
