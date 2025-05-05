<?php

declare(strict_types=1);

namespace App\System\Glossary;

use App\System\Language;

readonly class Glossary
{
    /**
     * @param array<string, string> $entries
     */
    public function __construct(
        public string $name,
        public Language $sourceLanguage,
        public Language $targetLanguage,
        public array $entries,
        public ?string $id = null,
    ) {
    }
}
