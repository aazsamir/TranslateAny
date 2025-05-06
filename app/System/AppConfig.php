<?php

declare(strict_types=1);

namespace App\System;

use App\Auth\Authorizator;
use App\Engine\DetectEngine;
use App\Engine\DocumentTranslateEngine;
use App\Engine\TranslateEngine;

readonly class AppConfig
{
    public function __construct(
        public ?TranslateEngine $translate = null,
        public ?DetectEngine $detection = null,
        public ?DocumentTranslateEngine $document = null,
        public ?Authorizator $authorizator = null,
        public bool $debug = false,
    ) {
    }
}
