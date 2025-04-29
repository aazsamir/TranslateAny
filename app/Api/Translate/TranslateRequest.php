<?php

declare(strict_types=1);

namespace App\Api\Translate;

use Tempest\Router\IsRequest;
use Tempest\Router\Request;

class TranslateRequest implements Request
{
    use IsRequest;

    public string $q;
    public string $source;
    public string $target;
    public ?string $format = null;
    public ?int $alternatives = null;
}
