<?php

declare(strict_types=1);

namespace App\Api\LibreTranslate\Detect;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;

class DetectRequest implements Request
{
    use IsRequest;

    public string $q;
}
