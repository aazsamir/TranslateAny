<?php

declare(strict_types=1);

namespace App\Api\DeepL\V2\Document;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;

class DocumentRequest implements Request
{
    use IsRequest;

    public string $target_lang;
    public ?string $source_lang = null;
    public ?string $output_format = null;
    public ?string $formality = null;
}
