<?php

declare(strict_types=1);

namespace Tests\Mock;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class PsrClientMock implements ClientInterface
{
    public ?ResponseInterface $response = null;

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        if ($this->response !== null) {
            return $this->response;
        }

        return new Response(
            body: '{}',
        );
    }

    public function setResponse(
        array $body,
        int $status = 200,
    ): void {
        $this->response = new Response(
            status: $status,
            body: json_encode($body),
        );
    }
}
