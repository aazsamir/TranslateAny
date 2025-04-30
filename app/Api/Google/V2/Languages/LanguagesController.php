<?php

declare(strict_types=1);

namespace App\Api\Google\V2\Languages;

use App\Engine\Languages;
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

    #[Get('/google/v2/language/translate/v2/languages')]
    public function __invoke(): Response
    {
        $languages = $this->translate->languages();
        $response = [];

        foreach ($languages as $lan) {
            $response[] = [
                'language' => $lan->language,
                'name' => Languages::getName($lan->language),
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
