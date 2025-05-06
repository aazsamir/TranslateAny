<?php

declare(strict_types=1);

namespace Tests\Unit\System\Document;

use App\System\Document\TextExtractor;
use Laminas\Diactoros\UploadedFile;
use Tempest\Http\Upload;
use Tests\TestCase;

class TextExtractorTest extends TestCase
{
    private TextExtractor $extractor;

    protected function setUp(): void
    {
        $this->extractor = new TextExtractor();
    }

    public function testExtractFromTxt(): void
    {
        $text = $this->extractor->extract(
            new Upload(
                new UploadedFile(
                    streamOrFile: __DIR__ . '/data/file.txt',
                    size: null,
                    errorStatus: 0,
                ),
            ),
        );

        $this->assertEquals(['test content'], \iterator_to_array($text));
    }
}
