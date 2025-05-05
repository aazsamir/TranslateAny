<?php

declare(strict_types=1);

namespace App\Engine\Initializer;

use App\Engine\DocumentTranslateEngine;
use App\Engine\Noop\NoopDocumentTranslateEngine;
use App\System\AppConfig;
use Tempest\Container\Container;
use Tempest\Container\Initializer;

readonly class DocumentTranslateInitializer implements Initializer
{
    public function __construct(
        private AppConfig $appConfig,
    ) {
    }

    public function initialize(Container $container): DocumentTranslateEngine
    {
        return $this->appConfig->document ?? new NoopDocumentTranslateEngine();
    }
}
