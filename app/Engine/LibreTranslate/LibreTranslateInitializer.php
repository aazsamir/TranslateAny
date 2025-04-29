<?php

declare(strict_types=1);

namespace App\Engine\LibreTranslate;

use App\System\AppConfig;
use GuzzleHttp\Client;
use Tempest\Container\Container;
use Tempest\Container\Initializer;

readonly class LibreTranslateInitializer implements Initializer
{
    public function __construct(
        private AppConfig $appConfig,
    ) {
    }

    public function initialize(Container $container): LibreTranslateEngine
    {
        // TODO: it shouldn't look like that
        if ($this->appConfig->detection instanceof LibreTranslateConfig) {
            $host = $this->appConfig->detection->url;
        } elseif ($this->appConfig->translate instanceof LibreTranslateConfig) {
            $host = $this->appConfig->translate->url;
        } else {
            throw new \RuntimeException('LibreTranslate config not found.');
        }

        $host = rtrim($host, '/');

        return new LibreTranslateEngine(
            url: $host,
            client: new Client(),
        );
    }
}
