<?php

declare(strict_types=1);

namespace App\Api\DeepL\V2\Glossaries;

class GlossaryService
{
    /**
     * @return array<string, string>
     */
    public function toArray(string $entriesString, string $separator = ','): array
    {
        if ($entriesString === '' || $separator === '') {
            return [];
        }

        $entries = [];

        foreach (explode("\n", $entriesString) as $line) {
            if ($line === '') {
                continue;
            }

            [$source, $target] = explode($separator, $line);
            $entries[$source] = $target;
        }

        return $entries;
    }
}
