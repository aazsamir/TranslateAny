<?php

declare(strict_types=1);

namespace Tests\Mock;

use Tempest\Http\GenericResponse;
use Tempest\Http\Response;
use Tempest\Http\Status;
use Tempest\Router\HttpMiddlewareCallable;

class HttpMiddlewareCallableMock
{
    public Response $response;

    public function __construct(?Response $response = null)
    {
        $this->response = $response ?? new GenericResponse(
            body: 'test',
            status: Status::OK,
        );
    }

    public function toCallable(): HttpMiddlewareCallable
    {
        return new HttpMiddlewareCallable(
            closure: fn () => $this->response,
        );
    }
}
