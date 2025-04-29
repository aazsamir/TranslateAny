<?php

declare(strict_types=1);

namespace App\Engine;

readonly class AvailableLanguage
{
    /**
     * @param string[] $targets
     */
    public function __construct(
        public string $language,
        public array $targets,
    ) {
    }
}
