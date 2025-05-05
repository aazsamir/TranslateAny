<?php

declare(strict_types=1);

namespace App\System\ConfigBuilder;

use App\System\AppConfig;
use Tempest\Console\Console;
use Tempest\Console\ConsoleCommand;

class ConfigBuilderCommand
{
    public function __construct(
        private Console $console,
    ) {}

    #[ConsoleCommand(
        name: 'create-config',
        aliases: [
            'cc',
        ],
        description: 'Create a configuration file.'
    )]
    public function __invoke(): void
    {
        $this->createConfigFile(
            $this->getUserParams(),
        );
    }

    private function getUserParams(): UserParams
    {
        $params = new UserParams();
        $this->console->info('TranslateAny config creator.');
        $params->translateEngine = $this->console->ask(
            question: 'What engine will you use?',
            options: [
                'openai' => 'OpenAI API compatible server',
                'libre' => 'LibreTranslate',
                'noop' => 'No translation',
            ],
            default: 'openai',
        );

        if ($params->isOpenAITranslate()) {
            $this->console->info('What model will you use? For example: gpt-3.5-turbo, qwen3:14b, etc.');
            $model = $this->console->readln();
            $model = trim($model);
            $params->translateModel = $model;

            $host = $this->console->ask(
                question: 'What is the host of your OpenAI API compatible server?',
                options: [
                    'https://api.openai.com/v1',
                    'http://localhost:11434/v1',
                    'http://host.docker.internal:11434/v1',
                    'custom',
                ],
                default: 'http://localhost:11434/v1',
            );

            if ($host === 'custom') {
                $this->console->info('What is the host of your OpenAI API compatible server? For example: http://localhost:11434/v1');
                $host = $this->console->readln();
                $host = trim($host);
            }

            $params->translateHost = $host;

            $this->console->info('What is your OpenAI API key? If you are using a local server, you can leave this blank.');
            $key = $this->console->readln();
            $key = trim($key);
            $key = $key === '' ? null : $key;
            $params->translateApiKey = $key;
        }

        if ($params->isLibreTranslate()) {
            $this->console->info('What is the host of your LibreTranslate server? For example: http://localhost:5000');
            $host = $this->console->readln();
            $host = trim($host);
            $params->translateHost = $host;

            $this->console->info('What is your LibreTranslate API key? If you are using a local server, you can leave this blank.');
            $key = $this->console->readln();
            $key = trim($key);
            $key = $key === '' ? null : $key;
            $params->translateApiKey = $key;
        }

        if (!$params->isNoopTranslate()) {
            $params->translateWithCache = (bool) $this->console->ask(
                question: 'Do you want to cache responses?',
                options: [
                    true => 'Yes',
                    false => 'No',
                ],
                default: 'Yes',
            );
        }

        if ($params->isWithCache()) {
            $cache = $this->console->ask(
                question: 'For how long do you want to cache responses?',
                options: [
                    '1' => '1 minute',
                    '5' => '5 minutes',
                    '10' => '10 minutes',
                    '15' => '15 minutes',
                    '30' => '30 minutes',
                    '60' => '1 hour',
                    '24' => '1 day',
                    '168' => '1 week',
                    null => 'No cache',
                ],
                default: '15 minutes',
            );

            if ($cache === null) {
                $params->translateWithCache = false;
            } else {
                $params->translateCacheTime = (int) $cache;
            }
        }

        $withDetection = $this->console->ask(
            question: 'Do you want to configure language detection?',
            options: [
                true => 'Yes',
                false => 'No',
            ],
            default: 'No',
        );

        if ($withDetection) {
            $params->detectEngine = $this->console->ask(
                question: 'What language detection engine will you use?',
                options: [
                    'openai' => 'OpenAI API compatible server',
                    'libre' => 'LibreTranslate',
                    'noop' => 'No detection',
                ],
                default: 'openai',
            );
        }

        if ($params->isOpenAIDetect()) {
            $this->console->info('What model will you use for language detection? For example: gpt-3.5-turbo, qwen3:14b, etc.');
            $detectionModel = $this->console->readln();
            $detectionModel = trim($detectionModel);
            $params->detectModel = $detectionModel;

            if ($params->isOpenAITranslate()) {
                $params->detectHost = $params->translateHost;
                $params->detectApiKey = $params->translateApiKey;
            } else {
                $this->console->info('What is the host of your OpenAI API compatible server? For example: http://localhost:11434/v1');
                $params->detectHost = $this->console->readln();
                $params->detectHost = trim($params->detectHost);

                $this->console->info('What is your OpenAI API key? If you are using a local server, you can leave this blank.');
                $params->detectApiKey = $this->console->readln();
                $params->detectApiKey = trim($params->detectApiKey);
            }
        }

        if ($params->isLibreDetect()) {
            if ($params->isLibreTranslate()) {
                $params->detectHost = $params->translateHost;
                $params->detectApiKey = $params->translateApiKey;
            } else {
                $this->console->info('What is the host of your LibreTranslate server? For example: http://localhost:5000');
                $params->detectHost = $this->console->readln();
                $params->detectHost = trim($params->detectHost);

                $this->console->info('What is your LibreTranslate API key? If you are using a local server, you can leave this blank.');
                $params->detectApiKey = $this->console->readln();
                $params->detectApiKey = trim($params->detectApiKey);
                $params->detectApiKey = $params->detectApiKey === '' ? null : $params->detectApiKey;
            }
        }

        return $params;
    }

    private function createConfigFile(UserParams $params): void
    {
        $configfile = <<<'PHP'
        <?php
        // This file is generated by the TranslateAny config creator.
        // Feel free to edit it however you want.

        use App\Engine\Cache\CacheEngine;
        use App\Engine\OpenAI\OpenAIEngine;
        use App\Engine\OpenAI\OpenAIDetectEngine;
        use App\Engine\LibreTranslate\LibreTranslateEngine;
        use App\Engine\Noop\NoopTranslateEngine;
        use App\Engine\Noop\NoopDetectEngine;
        use App\System\AppConfig;

        return new AppConfig(
            translate: %s,
            detection: %s,
        );
        PHP;

        if ($params->isOpenAITranslate()) {
            $translate = <<<PHP
            OpenAIEngine::new(
                model: '$params->translateModel',
                host: '$params->translateHost',
                apiKey: '$params->translateApiKey',
            )
            PHP;
        }

        if ($params->isLibreTranslate()) {
            $translate = <<<PHP
            LibreTranslateEngine::new(
                host: '$params->translateHost',
                apiKey: '$params->translateApiKey',
            )
            PHP;
        }

        if ($params->isNoopTranslate()) {
            $translate = <<<PHP
            NoopTranslateEngine::new()
            PHP;
        }

        if ($params->isWithCache()) {
            $translate = $this->indent($translate, 1);
            $translate = <<<PHP
            CacheEngine::new(
                engine: $translate,
                cacheMinutes: $params->translateCacheTime,
            )
            PHP;
        }

        if ($params->isOpenAIDetect()) {
            $detection = <<<PHP
            OpenAIDetectEngine::new(
                host: '$params->detectHost',
                apiKey: '$params->detectApiKey',
                model: '$params->detectModel',
            )
            PHP;
        }

        if ($params->isLibreDetect()) {
            $detection = <<<PHP
            LibreTranslateEngine::new(
                host: '$params->detectHost',
                apiKey: '$params->detectApiKey',
            )
            PHP;
        }

        if ($params->isNoopDetect()) {
            $detection = <<<PHP
            NoopDetectEngine::new()
            PHP;
        }

        $translate = $this->indent($translate, 1);
        $detection = $this->indent($detection, 1);

        $configfile = sprintf(
            $configfile,
            $translate,
            $detection,
        );

        $this->saveFile($configfile);
    }

    private function indent(string $string, int $level = 1): string
    {
        $indent = str_repeat('    ', $level);
        $lines = explode("\n", $string);
        $indented = [];
        foreach ($lines as $i => $line) {
            if ($i === 0) {
                $indented[] = $line;

                continue;
            }

            $indented[] = $indent . $line;
        }

        return implode("\n", $indented);
    }

    private function saveFile(string $configfile): void
    {
        $path = __DIR__ . '/../../Config/app.config.php';
        file_put_contents($path, $configfile);
        $path = realpath($path);
        $this->console->info('Config file created at ' . $path);

        try {
            /**
             * @var AppConfig $config
             */
            $config = require $path;
        } catch (\Throwable $e) {
            $this->console->error('Error loading config file: ' . $e->getMessage());
            $newPath = __DIR__ . '/../../Config/app.config.broken.php';
            rename($path, $newPath);
            $newPath = realpath($newPath);
            $this->console->warning('File was moved to ' . $newPath);
            $this->console->info('Please check the file and try again.');

            return;
        }

        $this->console->success('Config file loaded successfully!');
    }
}
