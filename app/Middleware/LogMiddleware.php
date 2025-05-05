<?php

declare(strict_types=1);

namespace App\Middleware;

use App\System\AppConfig;
use Tempest\Http\Request;
use Tempest\Http\Response;
use Tempest\Log\Logger;
use Tempest\Router\HttpMiddleware;
use Tempest\Router\HttpMiddlewareCallable;

class LogMiddleware implements HttpMiddleware
{
    public function __construct(
        private Logger $logger,
        private AppConfig $appConfig,
    ) {
    }

    public function __invoke(Request $request, HttpMiddlewareCallable $next): Response
    {
        if (! $this->appConfig->debug) {
            return $next($request);
        }

        $response = $next($request);

        $this->logger->debug(
            $request->path,
            [
                'path' => $request->path,
                'method' => $request->method->value,
                'request_body' => $request->body,
                'response_body' => $response->body,
            ],
        );

        return $response;
    }
}
