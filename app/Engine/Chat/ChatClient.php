<?php

declare(strict_types=1);

namespace App\Engine\Chat;

interface ChatClient
{
    /**
     * @param ChatMessage[] $messages
     */
    public function chat(array $messages): string;
}
