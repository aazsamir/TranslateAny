<?php

declare(strict_types=1);

namespace Tests\Mock;

use App\Engine\Chat\ChatClient;

class ChatClientMock implements ChatClient
{
    public function __construct(
        public string $response = 'Hello there, this is a fake chat response.',
    ) {
    }

    public function chat(array $messages): string
    {
        return $this->response;
    }
}
