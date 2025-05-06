<?php

declare(strict_types=1);

namespace App\Api\DeepL;

use App\Auth\Authorizator;
use App\Auth\Credentials;
use Tempest\Discovery\SkipDiscovery;
use Tempest\Http\Request;
use Tempest\Http\Response;
use Tempest\Http\Responses\Forbidden;
use Tempest\Router\HttpMiddleware;
use Tempest\Router\HttpMiddlewareCallable;

#[SkipDiscovery]
readonly class AuthMiddleware implements HttpMiddleware
{
    public function __construct(
        private Authorizator $authorizator,
    ) {
    }

    public function __invoke(Request $request, HttpMiddlewareCallable $next): Response
    {
        $credentials = Credentials::fromAuthToken($request->headers, 'DeepL-Auth-Key');

        if ($this->authorizator->isAuthenticated($credentials)) {
            return $next($request);
        }

        return new Forbidden();
    }
}
