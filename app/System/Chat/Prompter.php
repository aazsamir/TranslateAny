<?php

declare(strict_types=1);

namespace App\System\Chat;

use App\Engine\TranslatePayload;
use App\System\Glossary\Glossary;

class Prompter
{
    public static function systemWithGlossary(
        string $systemPrompt,
        ?string $glossaryPrompt,
        ?Glossary $glossary,
    ): string {
        if ($glossary === null) {
            return $systemPrompt;
        }

        if ($glossaryPrompt === null) {
            return $systemPrompt;
        }

        foreach ($glossary->entries as $source => $target) {
            $glossaryPrompt .= "\n- $source => $target";
        }

        $systemPrompt .= ' ' . $glossaryPrompt;

        return $systemPrompt;
    }

    public static function translatePrompt(TranslatePayload $payload): string
    {
        return 'Translate to ' . $payload->targetLanguage->value . ' language:\n' . $payload->text;
    }

    public static function detectPrompt(string $text): string
    {
        return 'Detect language of the following text:\n' . $text;
    }
}
