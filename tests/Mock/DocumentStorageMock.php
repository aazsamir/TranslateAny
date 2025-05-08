<?php

declare(strict_types=1);

namespace Tests\Mock;

use App\System\Document\DocumentStorage;

class DocumentStorageMock implements DocumentStorage
{
    public array $pages = [];

    public function storeTranslated(array $pages): string
    {
        $this->pages = $pages;

        return '123';
    }

    public function downloadPath(string $name): string
    {
        return 'download_path/' . $name;
    }
}