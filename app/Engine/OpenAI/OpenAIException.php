<?php

declare(strict_types=1);

namespace App\Engine\OpenAI;

use App\Engine\Chat\ChatException;

class OpenAIException extends ChatException
{
    public static function fromPrevious(\Throwable $previous): self
    {
        return new self(
            message: 'OpenAI error: ' . $previous->getMessage(),
            code: 0,
            previous: $previous,
        );
    }
}
