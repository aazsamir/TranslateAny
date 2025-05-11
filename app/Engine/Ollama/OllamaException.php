<?php

declare(strict_types=1);

namespace App\Engine\Ollama;

use App\Engine\Chat\ChatException;
use Psr\Http\Message\ResponseInterface;

class OllamaException extends ChatException
{
    public static function fromResponse(ResponseInterface $response): self
    {
        return new OllamaException(
            message: 'Ollama error: ' . $response->getStatusCode() . ' ' . $response->getReasonPhrase(),
            code: $response->getStatusCode(),
            previous: null,
        );
    }
}
