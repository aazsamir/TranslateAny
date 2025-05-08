<?php

declare(strict_types=1);

namespace Tests\Unit\System\Document;

use App\System\Document\SimpleTextExtractor;
use Laminas\Diactoros\UploadedFile;
use Tempest\Http\Upload;
use Tests\TestCase;

class SimpleTextExtractorTest extends TestCase
{
    private SimpleTextExtractor $extractor;

    protected function setUp(): void
    {
        $this->extractor = new SimpleTextExtractor();
    }

    public function testExtractFromTxt(): void
    {
        $text = $this->extractor->extract(
            $this->file('file.txt', 'text/plain'),
        );

        $this->assertEquals(['test content'], \iterator_to_array($text));
    }

    public function testExtractFromPdf(): void
    {
        $text = $this->extractor->extract(
            $this->file('file.pdf', 'application/pdf'),
        );

        $this->assertEquals(['PLACEHOLDER PDF  '], \iterator_to_array($text));
    }

    private function file(string $name, string $mediaType): Upload
    {
        $stream = \fopen(__DIR__ . '/data/' . $name, 'r');

        return new Upload(
            new UploadedFile(
                streamOrFile: $stream,
                size: null,
                errorStatus: 0,
                clientFilename: $name,
                clientMediaType: $mediaType,
            ),
        );
    }
}
