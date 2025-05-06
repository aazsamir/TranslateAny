<?php

declare(strict_types=1);

namespace App\System\Glossary;

interface GlossaryRepository
{
    public function exists(string $id): bool;

    public function get(?string $id): ?Glossary;

    /**
     * @return Glossary[]
     */
    public function all(): array;

    public function remove(string $id): void;

    public function save(Glossary $glossary): string;
}
