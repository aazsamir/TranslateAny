<?php

declare(strict_types=1);

namespace App\System;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Http\Client\ClientInterface;

class PsrClientFactory
{
    public static function new(
        ?int $timeout = null,
        ?int $connectTimeout = 5,
    ): ClientInterface {
        return new Client([
            RequestOptions::TIMEOUT => $timeout ?? 0,
            RequestOptions::CONNECT_TIMEOUT => $connectTimeout,
        ]);
    }
}
