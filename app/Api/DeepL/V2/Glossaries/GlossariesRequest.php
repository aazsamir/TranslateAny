<?php

declare(strict_types=1);

namespace App\Api\DeepL\V2\Glossaries;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;

class GlossariesRequest implements Request
{
    use IsRequest;

    public string $name;
    public string $source_lang;
    public string $target_lang;
    public string $entries;
    public string $entries_format;
}
