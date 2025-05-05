<?php

declare(strict_types=1);

namespace App\Api\Google\V2\Translate;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;

class TranslateRequest implements Request
{
    use IsRequest;

    public string $q;
    public string $target;
    public ?string $source = null;
}
