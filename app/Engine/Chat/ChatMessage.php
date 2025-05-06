<?php

declare(strict_types=1);

namespace App\Engine\Chat;

readonly class ChatMessage
{
    public function __construct(
        public string $role,
        public string $content,
    ) {
    }
}
