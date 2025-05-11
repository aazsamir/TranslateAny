<?php

declare(strict_types=1);

namespace App\System\Document;

interface DocumentStorage
{
    /**
     * @param string[] $pages
     */
    public function storeTranslated(array $pages): string;

    public function downloadPath(string $name): string;
}
