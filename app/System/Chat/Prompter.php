<?php

declare(strict_types=1);

namespace App\System\Chat;

use App\Engine\TranslatePayload;
use App\System\Glossary\Glossary;

class Prompter
{
    public static function systemWithGlossary(
        string $systemPrompt,
        ?string $context = null,
        ?string $glossaryPrompt = null,
        ?Glossary $glossary = null,
    ): string {
        if ($context !== null) {
            $systemPrompt .= "\n" . $context;
        }

        if ($glossary === null) {
            return $systemPrompt;
        }

        if ($glossaryPrompt === null) {
            return $systemPrompt;
        }

        foreach ($glossary->entries as $source => $target) {
            $glossaryPrompt .= "\n- $source => $target";
        }

        $systemPrompt .= "\n" . $glossaryPrompt;

        return $systemPrompt;
    }

    public static function translatePrompt(TranslatePayload $payload): string
    {
        if ($payload->sourceLanguage !== null) {
            $prompt = 'Translate from ' . $payload->sourceLanguage->value . ' to ' . $payload->targetLanguage->value . ' language:\n';
        } else {
            $prompt = 'Translate to ' . $payload->targetLanguage->value . ' language:\n';
        }

        $prompt = 'Translate to ' . $payload->targetLanguage->value . ' language:\n';
        $prompt .= '###';
        $prompt .= $payload->text;
        $prompt .= '###';

        return $prompt;
    }

    public static function detectPrompt(string $text): string
    {
        return 'Detect language of the following text:\n' . $text;
    }
}
