<?php

declare(strict_types=1);

namespace Tests\Mock;

use App\Engine\Chat\ChatClient;
use App\Engine\Chat\ChatMessage;

class ChatClientMock implements ChatClient
{
    /**
     * @var ChatMessage[]
     */
    public array $gotMessages = [];

    public function __construct(
        public string $response = 'Hello there, this is a fake chat response.',
    ) {
    }

    public function chat(array $messages): string
    {
        $this->gotMessages = $messages;

        return $this->response;
    }
}
