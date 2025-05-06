<?php

declare(strict_types=1);

namespace Tests\Integration\Cli\Translate;

use Tests\Integration\TestCase;

class TranslateCommandTest extends TestCase
{
    public function test(): void
    {
        $this->console->call(
            'translate',
            [
                'text' => 'Hello world!',
                'target' => 'pl',
                'source' => 'en',
            ],
        );

        $this->console->assertContains('Hello world!');
    }
}
