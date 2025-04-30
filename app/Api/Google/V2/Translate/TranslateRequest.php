<?php

declare(strict_types=1);

namespace App\Api\Google\V2\Translate;

use Tempest\Router\IsRequest;
use Tempest\Router\Request;

class TranslateRequest implements Request
{
    use IsRequest;

    public string $q;
    public string $target;
    public ?string $source;
}
