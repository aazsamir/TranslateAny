<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Middleware\LogMiddleware;
use App\System\AppConfig;
use App\System\Logger\MemoryLogger;
use Tempest\Http\GenericRequest;
use Tempest\Http\Method;
use Tests\Mock\HttpMiddlewareCallableMock;
use Tests\TestCase;

class LogMiddlewareTest extends TestCase
{
    public function testDebugOff(): void
    {
        $config = new AppConfig(
            debug: false,
        );
        $logger = new MemoryLogger();
        $middleware = new LogMiddleware(
            logger: $logger,
            appConfig: $config,
        );

        $middleware->__invoke(
            request: new GenericRequest(
                method: Method::GET,
                uri: '/test',
            ),
            next: new HttpMiddlewareCallableMock()->toCallable(),
        );

        $this->assertTrue($logger->empty());
    }

    public function testDebugOn(): void
    {
        $config = new AppConfig(
            debug: true,
        );
        $logger = new MemoryLogger();
        $middleware = new LogMiddleware(
            logger: $logger,
            appConfig: $config,
        );

        $middleware->__invoke(
            request: new GenericRequest(
                method: Method::GET,
                uri: '/test',
            ),
            next: new HttpMiddlewareCallableMock()->toCallable(),
        );

        $this->assertFalse($logger->empty());
    }
}
