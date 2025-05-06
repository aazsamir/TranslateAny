<?php

declare(strict_types=1);

namespace App\System\ConfigBuilder;

use App\System\AppConfig;
use Tempest\Console\Console;
use Tempest\Console\ConsoleCommand;

class ConfigBuilderCommand
{
    private const DEFAULT_SAVE_PATH = __DIR__ . '/../../Config/app.config.php';

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
    public function __invoke(string $path = self::DEFAULT_SAVE_PATH): void
    {
        $this->createConfigFile(
            $this->getUserParams(),
            $path,
        );
    }

    private function getUserParams(): UserParams
    {
        $params = new UserParams();
        $this->console->info('TranslateAny config creator.');
        $this->translateEngine($params);
        $this->translateParams($params);
        $this->translateCache($params);
        $this->detection($params);

        return $params;
    }

    private function translateEngine(UserParams $params): void
    {
        $params->translateEngine = $this->console->ask(
            question: 'What engine will you use?',
            options: [
                'openai' => 'OpenAI API compatible server',
                'ollama' => 'Ollama',
                'libre' => 'LibreTranslate',
                'noop' => 'No translation',
            ],
            default: 'openai',
        );
    }

    private function translateParams(UserParams $params): void
    {
        if ($params->isOpenAITranslate()) {
            $params->translateModel = $this->openAiModel();
            $params->translateHost = $this->openAiHost();
            $params->translateApiKey = $this->openAIApiKey();
        }

        if ($params->isOllamaTranslate()) {
            $params->translateModel = $this->ollamaModel();
            $params->translateHost = $this->ollamaHost();
        }

        if ($params->isLibreTranslate()) {
            $params->translateHost = $this->libreTranslateHost();
            $params->translateApiKey = $this->libreTranslateApiKey();
        }
    }

    private function translateCache(UserParams $params): void
    {
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
    }

    private function detection(UserParams $params): void
    {
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
                    'ollama' => 'Ollama',
                    'libre' => 'LibreTranslate',
                    'noop' => 'No detection',
                ],
                default: 'openai',
            );
        }

        if ($params->isOpenAIDetect()) {
            $params->detectModel = $this->openAiModel();

            if ($params->isOpenAITranslate()) {
                $params->detectHost = $params->translateHost;
                $params->detectApiKey = $params->translateApiKey;
            } else {
                $params->detectHost = $this->openAiHost();
                $params->detectApiKey = $this->openAIApiKey();
            }
        }

        if ($params->isOllamaDetect()) {
            $params->detectModel = $this->ollamaModel();

            if ($params->isOllamaTranslate()) {
                $params->detectHost = $params->translateHost;
            } else {
                $params->detectHost = $this->ollamaHost();
            }
        }

        if ($params->isLibreDetect()) {
            if ($params->isLibreTranslate()) {
                $params->detectHost = $params->translateHost;
                $params->detectApiKey = $params->translateApiKey;
            } else {
                $params->detectHost = $this->libreTranslateHost();
                $params->detectApiKey = $this->libreTranslateApiKey();
            }
        }
    }

    private function openAiModel(): string
    {
        $model = $this->read('What model will you use? For example: gpt-3.5-turbo, qwen3:14b, etc.');
        $model = trim($model);

        return $model;
    }

    private function openAiHost(): string
    {
        $translateHost = $this->console->ask(
            question: 'What is the host of your OpenAI API compatible server?',
            options: [
                'https://api.openai.com/v1',
                'http://localhost:11434/v1',
                'http://host.docker.internal:11434/v1',
                'custom',
            ],
            default: 'http://localhost:11434/v1',
        );

        if ($translateHost === 'custom') {
            $translateHost = $this->read('What is the host of your OpenAI API compatible server? For example: http://localhost:11434/v1');
        }

        return $translateHost;
    }

    private function openAIApiKey(): ?string
    {
        $key = $this->read('What is your OpenAI API key? If you are using a local server, you can leave this blank.');
        $key = $key === '' ? null : $key;

        return $key;
    }

    private function ollamaModel(): string
    {
        $model = $this->read('What model will you use? For example: qwen3:14b, etc.');
        $model = trim($model);

        return $model;
    }

    private function ollamaHost(): string
    {
        $translateHost = $this->console->ask(
            question: 'What is the host of your Ollama server?',
            options: [
                'http://localhost:11434',
                'http://host.docker.internal:11434',
                'custom',
            ],
            default: 'http://localhost:11434',
        );

        if ($translateHost === 'custom') {
            $translateHost = $this->read('What is the host of your Ollama server? For example: http://localhost:11434');
        }

        return $translateHost;
    }

    private function libreTranslateHost(): string
    {
        return $this->read('What is the host of your LibreTranslate server? For example: http://localhost:5000');
    }

    private function libreTranslateApiKey(): ?string
    {
        $key = $this->read('What is your LibreTranslate API key? If you are using a local server, you can leave this blank.');
        $key = $key === '' ? null : $key;

        return $key;
    }

    private function read(string $question): string
    {
        $this->console->info($question);
        $input = $this->console->readln();
        $input = trim($input);

        return $input;
    }

    private function createConfigFile(UserParams $params, string $savePath): void
    {
        $configfile = <<<'PHP'
        <?php
        // This file is generated by the TranslateAny config creator.
        // Feel free to edit it however you want.

        use App\Engine\Cache\CacheEngine;
        use App\Engine\Chat\ChatEngine;
        use App\Engine\Chat\ChatDetectEngine;
        use App\Engine\OpenAI\OpenAIClient;
        use App\Engine\Ollama\OllamaClient;
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
            ChatEngine::new(
                client: OpenAIClient::new(
                    host: "$params->translateHost",
                    apiKey: "$params->translateApiKey",
                    model: "$params->translateModel",
                ),
            )
            PHP;
        }

        if ($params->isOllamaTranslate()) {
            $translate = <<<PHP
            ChatEngine::new(
                client: OllamaClient::new(
                    host: "$params->translateHost",
                    model: "$params->translateModel",
                ),
            )
            PHP;
        }

        if ($params->isLibreTranslate()) {
            $translate = <<<PHP
            LibreTranslateEngine::new(
                host: "$params->translateHost",
                apiKey: "$params->translateApiKey",
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
            ChatDetectEngine::new(
                client: OpenAIClient::new(
                    host: "$params->detectHost",
                    apiKey: "$params->detectApiKey",
                    model: "$params->detectModel",
                ),
            )
            PHP;
        }

        if ($params->isOllamaDetect()) {
            $detection = <<<PHP
            ChatDetectEngine::new(
                client: OllamaClient::new(
                    host: "$params->detectHost",
                    model: "$params->detectModel",
                ),
            )
            PHP;
        }

        if ($params->isLibreDetect()) {
            $detection = <<<PHP
            LibreTranslateEngine::new(
                host: "$params->detectHost",
                apiKey: "$params->detectApiKey",
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

        $this->saveFile($configfile, $savePath);
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

    private function saveFile(string $configfile, string $path): void
    {
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
