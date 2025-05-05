<?php

declare(strict_types=1);

namespace App\Api\LibreTranslate\Translate;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;

class TranslateRequest implements Request
{
    use IsRequest;

    public string $q;
    public string $source;
    public string $target;
    public ?string $format = null;
    public ?int $alternatives = null;
}
