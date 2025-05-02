<?php

declare(strict_types=1);

namespace App\Engine;

use App\System\Language;

readonly class AvailableLanguage
{
    /**
     * @param Language[] $targets
     */
    public function __construct(
        public Language $language,
        public array $targets = [],
    ) {
    }
}
