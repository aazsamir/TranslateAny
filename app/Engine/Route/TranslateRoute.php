<?php

declare(strict_types=1);

namespace App\Engine\Route;

use App\Engine\TranslateEngine;
use App\System\Language;

readonly class TranslateRoute
{
    /**
     * @param Language[]|null $languages
     */
    public function __construct(
        public TranslateEngine $engine,
        public ?array $languages = null,
    ) {
    }

    /**
     * @param Language[]|null $languages
     */
    public static function new(
        TranslateEngine $engine,
        ?array $languages = null,
    ): self {
        if ($languages === []) {
            $languages = null;
        }

        return new self(
            languages: $languages,
            engine: $engine,
        );
    }

    public function supports(Language $language): bool
    {
        if ($this->languages === null || $this->languages === []) {
            return true;
        }

        return in_array($language, $this->languages, true);
    }
}
