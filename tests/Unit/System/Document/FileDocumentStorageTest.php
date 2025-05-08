<?php

declare(strict_types=1);

namespace Tests\Unit\System\Document;

use App\System\Document\FileDocumentStorage;
use Tests\TestCase;

class FileDocumentStorageTest extends TestCase
{
    private FileDocumentStorage $storage;

    protected function setUp(): void
    {
        $this->storage = new FileDocumentStorage(
            __DIR__ . '/storage',
        );

        $files = \glob(__DIR__ . '/storage/*');

        foreach ($files as $file) {
            if (\str_ends_with($file, '.gitignore')) {
                continue;
            }

            \unlink($file);
        }
    }

    protected function tearDown(): void
    {
        $files = \glob(__DIR__ . '/storage/*');

        foreach ($files as $file) {
            if (\str_ends_with($file, '.gitignore')) {
                continue;
            }

            \unlink($file);
        }
    }

    public function testStore(): void
    {
        $pages = ['content'];

        $file = $this->storage->storeTranslated($pages);

        $this->assertFileExists(__DIR__ . '/storage/' . $file);
    }

    public function testDownloadPath(): void
    {
        $path = $this->storage->downloadPath('.gitignore');

        $this->assertEquals(
            realpath(__DIR__ . '/storage/.gitignore'),
            $path,
        );
    }

    public function testExceptionInvalidDownloadPath(): void
    {
        $this->expectException(\RuntimeException::class);

        $this->storage->downloadPath('invalid_path');
    }
}