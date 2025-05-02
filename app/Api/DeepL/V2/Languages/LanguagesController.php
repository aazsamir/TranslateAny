<?php

declare(strict_types=1);

namespace App\Api\DeepL\V2\Languages;

use App\Engine\TranslateEngine;
use Tempest\Router\Get;
use Tempest\Router\Response;
use Tempest\Router\Responses\Ok;

readonly class LanguagesController
{
    public function __construct(
        private TranslateEngine $translate,
    ) {
    }

    #[Get('/deepl/v2/languages')]
    public function __invoke(): Response
    {
        $languages = $this->translate->languages();
        $response = [];

        foreach ($languages as $language) {
            $response[] = [
                'language' => $language->language->upper(),
                'name' => $language->language->value,
                'supports_formality' => false,
            ];
        }

        return new Ok($response);
    }
}
