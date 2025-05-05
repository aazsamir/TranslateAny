<?php

declare(strict_types=1);

namespace App\Engine;

readonly class DocumentTranslation
{
    public function __construct(
        public string $id,
    ) {
    }
}
