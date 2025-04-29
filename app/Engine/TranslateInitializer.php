<?php

declare(strict_types=1);

namespace App\Engine;

use App\Engine\LibreTranslate\LibreTranslateConfig;
use App\Engine\LibreTranslate\LibreTranslateEngine;
use App\Engine\Noop\NoopTranslateEngine;
use App\Engine\OpenAI\OpenAIConfig;
use App\Engine\OpenAI\OpenAIEngine;
use App\System\AppConfig;
use Tempest\Container\Container;
use Tempest\Container\Initializer;

readonly class TranslateInitializer implements Initializer
{
    public function __construct(
        private AppConfig $appConfig,
    ) {
    }

    public function initialize(Container $container): TranslateEngine
    {
        $config = $this->appConfig->translate;

        if ($config === null) {
            return $container->get(NoopTranslateEngine::class);
        }

        if ($config instanceof OpenAIConfig) {
            return $container->get(OpenAIEngine::class);
        }

        if ($config instanceof LibreTranslateConfig) {
            return $container->get(LibreTranslateEngine::class);
        }

        throw new \RuntimeException('Unknown translate engine.');
    }
}
