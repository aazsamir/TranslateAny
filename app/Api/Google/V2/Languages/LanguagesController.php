<?php

declare(strict_types=1);

namespace App\Api\Google\V2\Languages;

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
        uri: '/google/v2/language/translate/v2/languages',
        middleware: [
            LogMiddleware::class,
        ],
    )]
    public function __invoke(): Response
    {
        $languages = $this->translate->languages();
        $response = [];

        foreach ($languages as $lan) {
            $response[] = [
                'language' => $lan->language->lower(),
                'name' => $lan->language->value,
            ];
        }

        $response = [
            'data' => [
                'languages' => $response,
            ],
        ];

        return new Ok($response);
    }
}
