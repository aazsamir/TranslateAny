<?php

declare(strict_types=1);

namespace App\System\Glossary;

use App\System\Language;

readonly class FileGlossaryRepository implements GlossaryRepository
{
    private string $dir;

    public function __construct(?string $dir = null)
    {
        if ($dir === null) {
            $dir = __DIR__ . '/../../../var/glossaries';
        }

        $this->dir = $dir;
    }

    public function exists(string $id): bool
    {
        return file_exists($this->dir . '/' . $id);
    }

    public function get(string $id): Glossary
    {
        $content = file_get_contents($this->dir . '/' . $id);

        if ($content === false) {
            throw new \RuntimeException('Could not read glossary file');
        }

        /**
         * @var array{
         *  sourceLanguage: string,
         *  targetLanguage: string,
         *  entries: array<string, string>
         * }
         */
        $json = json_decode($content, true, flags: JSON_THROW_ON_ERROR);
        $source = Language::fromAny($json['sourceLanguage']);
        $target = Language::fromAny($json['targetLanguage']);

        return new Glossary(
            sourceLanguage: $source,
            targetLanguage: $target,
            entries: $json['entries'],
        );
    }

    public function all(): array
    {
        return [];
    }

    public function remove(string $id): void
    {
    }

    public function save(Glossary $glossary): string
    {
        $json = [
            'sourceLanguage' => $glossary->sourceLanguage->lower(),
            'targetLanguage' => $glossary->targetLanguage->lower(),
            'entries' => $glossary->entries,
        ];

        $id = uuid_create();

        file_put_contents($this->dir . '/' . $id, json_encode($json));

        return $id;
    }
}
