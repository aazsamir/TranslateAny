<?php

declare(strict_types=1);

namespace App\Engine;

readonly class Translation
{
    /**
     * @param string[] $alternatives
     */
    public function __construct(
        public string $text,
        public array $alternatives = [],
        public ?Detection $detectedLanguage = null,
    ) {
    }
}
