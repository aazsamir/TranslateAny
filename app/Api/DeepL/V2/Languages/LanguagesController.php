<?php

declare(strict_types=1);

namespace App\Api\DeepL\V2\Languages;

use App\Engine\TranslateEngine;
use App\Middleware\LogMiddleware;
use Tempest\Http\Response;
use Tempest\Http\Responses\Ok;
use Tempest\Router\Get;

readonly class LanguagesController
{
    public function __construct(
        private TranslateEngine $translate,
    ) {
    }

    #[Get(
        uri: '/deepl/v2/languages',
        middleware: [
            LogMiddleware::class,
        ],
    )]
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
