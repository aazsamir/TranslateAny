<?php

declare(strict_types=1);

namespace App\Engine;

interface DetectEngine
{
    /**
     * @return Detection[]
     */
    public function detect(string $text): array;
}
