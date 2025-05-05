<?php

declare(strict_types=1);

namespace App\Engine\Initializer;

use App\Engine\Noop\NoopTranslateEngine;
use App\Engine\TranslateEngine;
use App\System\AppConfig;
use Tempest\Container\Container;
use Tempest\Container\Initializer;

readonly class TranslateInitializer implements Initializer
{
    public function __construct(
        private AppConfig $appConfig,
    ) {
    }

    public function initialize(Container $container): TranslateEngine
    {
        return $this->appConfig->translate ?? new NoopTranslateEngine();
    }
}
