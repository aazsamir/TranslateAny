<?php

declare(strict_types=1);

namespace App\Engine\OpenAI;

use OpenAI\Contracts\ClientContract;

readonly class ClientFactory
{
    public static function make(string $host, ?string $apiKey = null): ClientContract
    {
        $host = rtrim($host, '/');

        $factory = \OpenAI::factory()
            ->withBaseUri($host)
            ->withHttpClient(new \GuzzleHttp\Client(['timeout' => 0]));

        if ($apiKey) {
            $factory->withApiKey($apiKey);
        }

        return $factory->make();
    }
}
