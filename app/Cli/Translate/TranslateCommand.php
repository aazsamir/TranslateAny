<?php

declare(strict_types=1);

namespace App\Cli\Translate;

use App\Engine\TranslateEngine;
use App\Engine\TranslatePayload;
use App\System\Language;
use App\System\Logger\MemoryLogger;
use Tempest\Console\Console;
use Tempest\Console\ConsoleCommand;
use Tempest\Container\Container;
use Tempest\Log\Logger;

class TranslateCommand
{
    public function __construct(
        private Console $console,
        private Container $container,
        private TranslateEngine $engine,
    ) {
    }

    #[ConsoleCommand(
        name: 'translate',
        description: 'Translate the given text.',
    )]
    public function __invoke(
        string $text,
        string $target,
        ?string $source = null,
        bool $verbose = false,
        bool $logs = true,
    ): void {
        if (! $logs) {
            $this->disableLogs();
        }

        $startedAt = microtime(true);

        $target = Language::tryFromAny($target);

        if ($target === null) {
            $this->console->error('Invalid target language.');
            $this->dumpLanguages();

            return;
        }

        if ($source !== null) {
            $source = Language::tryFromAny($source);

            if ($source === null) {
                $this->console->error('Invalid source language.');
                $this->dumpLanguages();

                return;
            }
        }

        $payload = new TranslatePayload(
            text: $text,
            targetLanguage: $target,
            sourceLanguage: $source,
        );

        $result = $this->engine->translate($payload);
        $this->console->writeln($result->text);

        if ($verbose) {
            $this->console->info('took ' . round(microtime(true) - $startedAt, 2) . ' seconds.');
        }
    }

    private function disableLogs(): void
    {
        $this->container->register(Logger::class, function () {
            return new MemoryLogger();
        });
    }

    private function dumpLanguages(): void
    {
        $this->console->info(
            "Available languages:\n" . \implode(',', array_map(
                fn (Language $lang) => $lang->lower(),
                Language::alphabetically(),
            )),
        );
    }
}
