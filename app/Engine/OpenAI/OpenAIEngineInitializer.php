<?php

declare(strict_types=1);

namespace App\Engine\OpenAI;

use App\System\AppConfig;
use OpenAI\Client;
use Tempest\Container\Container;
use Tempest\Container\Initializer;

readonly class OpenAIEngineInitializer implements Initializer
{
    public function __construct(
        private AppConfig $appConfig,
    ) {
    }

    public function initialize(Container $container): OpenAIEngine
    {
        if (! ($this->appConfig->translate instanceof OpenAIConfig)) {
            throw new \RuntimeException('OpenAIEngine is not set in the config.');
        }

        return new OpenAIEngine(
            client: $container->get(Client::class),
            model: $this->appConfig->translate->model,
            systemPrompt: $this->appConfig->translate->systemPrompt,
        );
    }
}
