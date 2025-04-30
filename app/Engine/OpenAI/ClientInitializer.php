<?php

declare(strict_types=1);

namespace App\Engine\OpenAI;

use App\System\AppConfig;
use OpenAI\Contracts\ClientContract;
use Tempest\Container\Container;
use Tempest\Container\Initializer;

final readonly class ClientInitializer implements Initializer
{
    public function __construct(
        private AppConfig $appConfig,
    ) {
    }

    public function initialize(Container $container): ClientContract
    {
        if (! ($this->appConfig->translate instanceof OpenAIConfig)) {
            throw new \RuntimeException('OpenAIEngine is not set in the config.');
        }
        $host = $this->appConfig->translate->host;
        $host = rtrim($host, '/');

        return \OpenAI::factory()
            ->withBaseUri($host)
            ->withHttpClient(new \GuzzleHttp\Client(['timeout' => 0]))
            ->make();
    }
}
