<?php

declare(strict_types=1);

namespace App\Engine;

use App\System\Language;
use Tempest\Http\Upload;

readonly class DocumentTranslatePayload
{
    public function __construct(
        public Upload $file,
        public Language $targetLanguage,
    ) {
    }
}
