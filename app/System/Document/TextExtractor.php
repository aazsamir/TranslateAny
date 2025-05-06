<?php

declare(strict_types=1);

namespace App\System\Document;

use Smalot\PdfParser\Parser;
use Tempest\Http\Upload;

class TextExtractor
{
    /**
     * @return iterable<string>
     */
    public function extract(Upload $file): iterable
    {
        if ($file->getClientMediaType() === 'application/pdf') {
            $tmppath = \tempnam(sys_get_temp_dir(), 'translate_any_pdf_');
            $file->moveTo($tmppath);
            $parser = new Parser();
            $pdf = $parser->parseFile($tmppath);

            foreach ($pdf->getPages() as $page) {
                yield $page->getText();
            }
        } else {
            yield $file->getStream()->getContents();
        }
    }
}
