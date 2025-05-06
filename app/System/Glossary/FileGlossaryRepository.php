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

    public function get(?string $id): ?Glossary
    {
        if ($id === null) {
            return null;
        }

        $content = file_get_contents($this->dir . '/' . $id);

        if ($content === false) {
            throw new \RuntimeException('Could not read glossary file');
        }

        /**
         * @var array{
         *  id: string,
         *  name: string,
         *  sourceLanguage: string,
         *  targetLanguage: string,
         *  entries: array<string, string>
         * }
         */
        $json = json_decode($content, true, flags: JSON_THROW_ON_ERROR);
        $source = Language::fromAny($json['sourceLanguage']);
        $target = Language::fromAny($json['targetLanguage']);

        return new Glossary(
            id: $id,
            name: $json['name'],
            sourceLanguage: $source,
            targetLanguage: $target,
            entries: $json['entries'],
        );
    }

    public function all(): array
    {
        $glossaries = [];

        foreach (glob($this->dir . '/*') ?: [] as $file) {
            if (basename($file) === '.gitignore') {
                continue;
            }

            $glossary = $this->get(basename($file));

            if ($glossary === null) {
                continue;
            }

            $glossaries[] = $glossary;
        }

        return $glossaries;
    }

    public function remove(string $id): void
    {
        if (! file_exists($this->dir . '/' . $id)) {
            throw new \RuntimeException('Glossary not found');
        }

        unlink($this->dir . '/' . $id);
    }

    public function save(Glossary $glossary): string
    {
        $id = uuid_create();
        $json = [
            'id' => $id,
            'name' => $glossary->name,
            'sourceLanguage' => $glossary->sourceLanguage->lower(),
            'targetLanguage' => $glossary->targetLanguage->lower(),
            'entries' => $glossary->entries,
        ];

        file_put_contents($this->dir . '/' . $id, json_encode($json));

        return $id;
    }
}
