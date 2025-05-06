<?php

declare(strict_types=1);

namespace Tests\Integration\Cli\ConfigBuilder;

use Tests\Integration\TestCase;

class ConfigBuilderCommandTest extends TestCase
{
    public function testDefaults(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'translate_any_test_') . '.php';
        $this->console->withoutPrompting()->call(
            'create-config',
            [
                'path' => $path,
            ],
        );

        $this->console->assertContains('Config file loaded successfully!');
        $this->assertFileExists($path);
        unlink($path);
    }
}
