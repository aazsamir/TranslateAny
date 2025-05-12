<?php

declare(strict_types=1);

namespace App\Engine\TranslateLocally;

use App\Engine\AvailableLanguage;
use App\Engine\TranslateEngine;
use App\Engine\TranslatePayload;
use App\Engine\Translation;
use App\System\Language;
use Symfony\Component\Process\Process;

class TranslateLocallyEngine implements TranslateEngine
{
    public function __construct(
        private string $binPath,
        private string $defaultModel = "en-pl-tiny",
    ) {}

    public static function new(
        string $binPath,
        string $defaultModel = "en-pl-tiny",
    ): static {
        return new static(
            binPath: $binPath,
            defaultModel: $defaultModel,
        );
    }

    public function translate(TranslatePayload $payload): Translation
    {
        $cli = new Process(
            [
                $this->binPath,
                "-m",
                $this->modelByPayload($payload),
            ],
        );
        $cli->setInput($payload->text);
        $cli->setTimeout(60);
        $output = $cli->run();

        if ($output !== 0) {
            throw new \RuntimeException("Translation failed");
        }

        $result = $cli->getOutput();

        return new Translation(
            text: $result,
        );
    }

    private function modelByPayload(TranslatePayload $payload): string
    {
        if ($payload->sourceLanguage === null) {
            return $this->defaultModel;
        }

        $pair = $payload->sourceLanguage->value . '-' . $payload->targetLanguage->value;

        foreach ($this->languagesFromCli() as $lang) {
            if (str_contains($lang, $pair)) {
                // English-Polish type: tiny version: 1; To invoke do -m en-pl-tiny
                // -> en-pl-tiny
                $matches = [];
                \preg_match(
                    "/To invoke do -m ([a-zA-Z0-9-]+)/",
                    $lang,
                    $matches,
                );

                if (isset($matches[1])) {
                    return $matches[1];
                }
            }
        }

        return $this->defaultModel;
    }

    public function languages(): array
    {
        $languages = $this->languagesFromCli();
        $available = [];

        foreach (Language::cases() as $case) {
            foreach ($languages as $lang) {
                if (
                    str_contains($lang, $case->value)
                    && $case->notIn($available)
                ) {
                    $available[] = $case;
                }
            }
        }

        return array_map(
            static fn(Language $lang) => new AvailableLanguage(
                language: $lang,
                targets: $available,
            ),
            $available,
        );
    }

    /**
     * @return string[]
     */
    private function languagesFromCli(): array
    {
        $cli = new Process(
            [
                $this->binPath,
                "-l",
            ],
        );

        $cli->setTimeout(10);
        $output = $cli->run();

        if ($output !== 0) {
            throw new \RuntimeException("Failed to get languages");
        }

        $result = $cli->getOutput();
        $languages = explode("\n", $result);
        $languages = \array_filter($languages);

        return $languages;
    }
}
