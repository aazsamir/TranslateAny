<?php

declare(strict_types=1);

namespace App\Auth;

use App\System\AppConfig;
use Tempest\Container\Container;
use Tempest\Container\Initializer;

class AuthorizatorInitializer implements Initializer
{
    public function __construct(
        private AppConfig $appConfig,
    ) {
    }

    public function initialize(Container $container): Authorizator
    {
        if ($this->appConfig->authorizator !== null) {
            return $this->appConfig->authorizator;
        }

        /** @var ?string */
        $token = $_ENV['TRANSLATE_ANY_TOKEN'] ?? null;
        $token = $token === '' ? null : $token;

        if ($token === null) {
            return new NoopAuthorizator();
        }

        return new SingleKeyAuthorizator(
            token: $token,
        );
    }
}
