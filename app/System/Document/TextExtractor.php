<?php

declare(strict_types=1);

namespace App\System\Document;

use Tempest\Http\Upload;

class TextExtractor
{
    /**
     * @return string[]
     */
    public function extract(Upload $file): array
    {
        return [$file->getStream()->getContents()];
    }
}
