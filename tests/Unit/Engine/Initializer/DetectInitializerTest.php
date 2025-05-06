<?php

declare(strict_types=1);

namespace Tests\Unit\Engine\Initializer;

use App\Engine\Initializer\DetectInitializer;
use App\System\AppConfig;
use Tempest\Container\Container;
use Tests\Mock\DetectEngineMock;
use Tests\TestCase;

class DetectInitializerTest extends TestCase
{
    public function test(): void
    {
        $config = new AppConfig(
            detection: new DetectEngineMock(),
        );
        $initializer = new DetectInitializer(
            $config,
        );
        $container = $this->createStub(Container::class);

        $init = $initializer->initialize($container);

        $this->assertSame(
            $config->detection,
            $init,
        );
    }
}
