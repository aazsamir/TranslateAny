<?php

declare(strict_types=1);

namespace Tests\Unit\Engine\Initializer;

use App\Engine\Initializer\DocumentTranslateInitializer;
use App\System\AppConfig;
use Tempest\Container\Container;
use Tests\Mock\DocumentTranslateMock;
use Tests\TestCase;

class DocumentTranslateInitializerTest extends TestCase
{
    public function test(): void
    {
        $config = new AppConfig(
            document: new DocumentTranslateMock(),
        );
        $initializer = new DocumentTranslateInitializer(
            $config,
        );
        $container = $this->createStub(Container::class);

        $init = $initializer->initialize($container);

        $this->assertSame(
            $config->document,
            $init,
        );
    }
}
