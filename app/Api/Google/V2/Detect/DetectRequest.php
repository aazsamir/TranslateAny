<?php

declare(strict_types=1);

namespace App\Api\Google\V2\Detect;

use Tempest\Router\IsRequest;
use Tempest\Router\Request;

class DetectRequest implements Request
{
    use IsRequest;

    public string $q;
}
