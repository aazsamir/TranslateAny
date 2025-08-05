<?php

declare(strict_types=1);

namespace App\System;

use Tempest\Core\KernelEvent;
use Tempest\EventBus\EventHandler;
use Tempest\Http\Session\VerifyCsrfMiddleware;
use Tempest\Router\RouteConfig;

/**
 * This is a workaround until Tempest allows for more flexible middleware configuration.
 */
class DisableCsrfMiddleware
{
    public function __construct(
        private RouteConfig $routeConfig,
    ) {
    }

    #[EventHandler(KernelEvent::BOOTED)]
    public function __invoke(): void
    {
        $this->routeConfig->middleware->remove(VerifyCsrfMiddleware::class);
    }
}
