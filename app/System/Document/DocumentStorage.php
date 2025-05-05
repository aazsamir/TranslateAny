<?php

declare(strict_types=1);

namespace App\System\Document;

class DocumentStorage
{
    private string $dir;

    public function __construct(?string $dir = null)
    {
        if ($dir === null) {
            $dir = __DIR__ . '/../../../var/documents/translated';
        }

        $this->dir = $dir;
    }

    /**
     * @param string[] $pages
     */
    public function storeTranslated(array $pages): string
    {
        $randomname = base64_encode(random_bytes(32));
        $randomname = strtolower(preg_replace('/[^a-zA-Z]*/', '', $randomname) ?? '');

        $file = fopen($this->dir . '/' . $randomname, 'a');

        if ($file === false) {
            throw new \RuntimeException('Could not open file for writing.');
        }

        foreach ($pages as $page) {
            fwrite($file, $page);
        }

        return $randomname;
    }

    public function downloadPath(string $name): string
    {
        $path = $this->dir . '/' . $name;

        if (! file_exists($path)) {
            throw new \RuntimeException('File not found.');
        }

        return $path;
    }
}
