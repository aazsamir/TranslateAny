<?php

declare(strict_types=1);

namespace App\Api\DeepLX\Translate;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;

class TranslateRequest implements Request
{
    use IsRequest;

    public string $text;
    public string $target_lang;
    public ?string $source_lang = null;
}
