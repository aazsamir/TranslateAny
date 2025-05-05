<?php

declare(strict_types=1);

namespace Tests\Unit\System\Glossary;

use App\System\Glossary\FileGlossaryRepository;
use App\System\Glossary\Glossary;
use App\System\Language;
use Tests\TestCase;

class FileGlossaryRepositoryTest extends TestCase
{
    private FileGlossaryRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new FileGlossaryRepository(
            dir: __DIR__ . '/data',
        );
    }

    protected function tearDown(): void
    {
        // iterate over all files in the directory and delete them
        foreach (glob(__DIR__ . '/data/*') as $file) {
            // leave .gitignore
            if (basename($file) === '.gitignore') {
                continue;
            }
            // leave prepared file
            if (\basename($file) === 'data') {
                continue;
            }
            unlink($file);
        }
    }

    public function testExists(): void
    {
        $this->assertTrue($this->repository->exists('.gitignore'));
    }

    public function testNotExists(): void
    {
        $this->assertFalse($this->repository->exists('non-existing-glossary'));
    }

    public function testSave(): void
    {
        $id = $this->repository->save(
            $this->glossary(),
        );

        $this->assertTrue($this->repository->exists($id));
    }

    public function testGet(): void
    {
        $glossary = $this->repository->get('data');

        $this->assertEquals('name', $glossary->name);
        $this->assertEquals(Language::en, $glossary->sourceLanguage);
        $this->assertEquals(Language::pl, $glossary->targetLanguage);
        $this->assertEquals(
            [
                'hello' => 'hej',
            ],
            $glossary->entries,
        );
        $this->assertEquals('data', $glossary->id);
    }

    public function testDelete(): void
    {
        $id = $this->repository->save(
            $this->glossary(),
        );

        $this->assertTrue($this->repository->exists($id));

        $this->repository->remove($id);

        $this->assertFalse($this->repository->exists($id));
    }

    public function testAll(): void
    {
        $glossaries = $this->repository->all();

        $this->assertCount(1, $glossaries);
        $this->assertEquals('name', $glossaries[0]->name);
        $this->assertEquals(Language::en, $glossaries[0]->sourceLanguage);
        $this->assertEquals(Language::pl, $glossaries[0]->targetLanguage);
        $this->assertEquals(
            [
                'hello' => 'hej',
            ],
            $glossaries[0]->entries,
        );
    }

    private function glossary(): Glossary
    {
        return new Glossary(
            name: 'name',
            sourceLanguage: Language::en,
            targetLanguage: Language::pl,
            entries: [
                'hello' => 'hej',
            ],
        );
    }
}
