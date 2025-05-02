<?php

declare(strict_types=1);

namespace App\Engine;

use App\Engine\Noop\NoopDetectionEngine;
use App\System\AppConfig;
use Tempest\Container\Container;
use Tempest\Container\Initializer;

class DetectionInitializer implements Initializer
{
    public function __construct(
        private AppConfig $appConfig,
    ) {
    }

    public function initialize(Container $container): DetectionEngine
    {
        return $this->appConfig->detection ?? new NoopDetectionEngine();
    }
}
