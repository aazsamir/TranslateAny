<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\Auth\AuthorizatorInitializer;
use App\Auth\NoopAuthorizator;
use App\Auth\SingleKeyAuthorizator;
use App\System\AppConfig;
use Tempest\Container\Container;
use Tests\TestCase;

class AuthorizatorInitializerTest extends TestCase
{
    protected function setUp(): void
    {
        unset($_ENV['TRANSLATE_ANY_TOKEN']);
    }

    protected function tearDown(): void
    {
        unset($_ENV['TRANSLATE_ANY_TOKEN']);
    }

    public function testFromConfig(): void
    {
        $config = new AppConfig(
            authorizator: new SingleKeyAuthorizator('test'),
        );
        $initializer = new AuthorizatorInitializer(
            $config,
        );
        $container = $this->createStub(Container::class);

        $init = $initializer->initialize($container);

        $this->assertSame(
            $config->authorizator,
            $init,
        );
    }

    public function testNull(): void
    {
        $config = new AppConfig(
            authorizator: null,
        );
        $initializer = new AuthorizatorInitializer(
            $config,
        );
        $container = $this->createStub(Container::class);

        $init = $initializer->initialize($container);

        $this->assertInstanceOf(
            NoopAuthorizator::class,
            $init,
        );
    }

    public function testByEnv(): void
    {
        $_ENV['TRANSLATE_ANY_TOKEN'] = 'test';

        $config = new AppConfig(
            authorizator: null,
        );
        $initializer = new AuthorizatorInitializer(
            $config,
        );
        $container = $this->createStub(Container::class);

        $init = $initializer->initialize($container);

        $this->assertInstanceOf(
            SingleKeyAuthorizator::class,
            $init,
        );
    }
}
