<?php

declare(strict_types=1);

namespace App\Engine\OpenAI;

use App\Engine\TranslateConfig;

readonly class OpenAIConfig implements TranslateConfig
{
    public function __construct(
        public string $model,
        public ?string $apikey = null,
        public string $host = 'https://api.openai.com/v1',
        public ?string $systemPrompt = null,
    ) {
    }
}
