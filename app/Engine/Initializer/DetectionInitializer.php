<?php

declare(strict_types=1);

namespace App\Engine\Initializer;

use App\Engine\DetectEngine;
use App\Engine\Noop\NoopDetectEngine;
use App\System\AppConfig;
use Tempest\Container\Container;
use Tempest\Container\Initializer;

class DetectionInitializer implements Initializer
{
    public function __construct(
        private AppConfig $appConfig,
    ) {
    }

    public function initialize(Container $container): DetectEngine
    {
        return $this->appConfig->detection ?? new NoopDetectEngine();
    }
}
