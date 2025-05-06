<?php

declare(strict_types=1);

namespace Tests\Unit\Engine\Initializer;

use App\Engine\Initializer\TranslateInitializer;
use App\System\AppConfig;
use Tempest\Container\Container;
use Tests\Mock\TranslateEngineMock;
use Tests\TestCase;

class TranslateInitializerTest extends TestCase
{
    public function test(): void
    {
        $config = new AppConfig(
            translate: new TranslateEngineMock(),
        );
        $initializer = new TranslateInitializer(
            $config,
        );
        $container = $this->createStub(Container::class);

        $init = $initializer->initialize($container);

        $this->assertSame(
            $config->translate,
            $init,
        );
    }
}
