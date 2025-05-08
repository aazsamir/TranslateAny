<?php

declare(strict_types=1);

namespace Tests\Mock;

use App\System\Document\TextExtractor;
use Tempest\Http\Upload;

class TextExtractorMock implements TextExtractor
{
    /**
     * @var array<int, string>
     */
    public array $extracted = [];

    public function extract(Upload $file): iterable
    {
        return $this->extracted;
    }
}