<?php

declare(strict_types=1);

namespace App\System\Logger;

use Stringable;

trait PrefixLogger
{
    public function prefixLog(string $prefix, string|Stringable $message): string
    {
        return "[$prefix] " . $message;
    }
}
