<?php

declare(strict_types=1);

namespace Tests\Mock;

use App\System\Glossary\Glossary;
use App\System\Glossary\GlossaryRepository;
use App\System\Language;

class GlossaryRepositoryMock implements GlossaryRepository
{
    /**
     * @var array<string, Glossary>
     */
    public array $glossaries = [];

    public function __construct()
    {
        $this->save(
            new Glossary(
                name: 'stub',
                sourceLanguage: Language::en,
                targetLanguage: Language::pl,
                entries: [
                    'hello' => 'hej',
                ],
                id: '1',
            ),
        );
    }

    public function exists(string $id): bool
    {
        return isset($this->glossaries[$id]);
    }

    public function get(?string $id): ?Glossary
    {
        if ($id === null) {
            return null;
        }

        if (! isset($this->glossaries[$id])) {
            throw new \InvalidArgumentException("Glossary with ID $id does not exist.");
        }

        return $this->glossaries[$id];
    }

    public function all(): array
    {
        return array_values($this->glossaries);
    }

    public function remove(string $id): void
    {
        if (isset($this->glossaries[$id])) {
            unset($this->glossaries[$id]);
        }
    }

    public function save(Glossary $glossary): string
    {
        $id = (string) (count($this->glossaries) + 1);
        $this->glossaries[$id] = $glossary;

        return $id;
    }
}
