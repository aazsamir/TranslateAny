<?php

declare(strict_types=1);

namespace App\System\Document;

use Smalot\PdfParser\Parser;
use Tempest\Container\Autowire;
use Tempest\Http\Upload;

#[Autowire]
class SimpleTextExtractor implements TextExtractor
{
    public function __construct(
        private bool $moveFiles = true,
    ) {}

    /**
     * @return iterable<int, string>
     */
    public function extract(Upload $file): iterable
    {
        if ($file->getClientMediaType() === 'application/pdf') {
            $path = \tempnam(sys_get_temp_dir(), 'translate_any_pdf_');
            $file->moveTo($path);

            $parser = new Parser();
            $pdf = $parser->parseFile($path);

            foreach ($pdf->getPages() as $page) {
                yield $page->getText();
            }
        } else {
            yield $file->getStream()->getContents();
        }
    }
}
