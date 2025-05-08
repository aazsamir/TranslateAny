<?php

declare(strict_types=1);

namespace App\System\Document;

use Smalot\PdfParser\Parser;
use Tempest\Http\Upload;

interface TextExtractor
{
    /**
     * @return iterable<int, string>
     */
    public function extract(Upload $file): iterable;
}
