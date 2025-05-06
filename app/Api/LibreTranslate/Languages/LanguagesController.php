<?php

declare(strict_types=1);

namespace App\Api\LibreTranslate\Languages;

use App\Api\LibreTranslate\AuthMiddleware;
use App\Engine\AvailableLanguage;
use App\Engine\TranslateEngine;
use App\Middleware\LogMiddleware;
use App\System\Language;
use Tempest\Http\Response;
use Tempest\Http\Responses\Ok;
use Tempest\Router\Get;

readonly class LanguagesController
{
    public function __construct(
        private TranslateEngine $engine,
    ) {
    }

    #[Get(
        uri: '/libre/languages',
        middleware: [
            LogMiddleware::class,
            AuthMiddleware::class,
        ],
    )]
    public function __invoke(): Response
    {
        $languages = $this->engine->languages();

        $response = array_map(
            fn (AvailableLanguage $language) => [
                'code' => $language->language->lower(),
                'name' => $language->language->value,
                'targets' => array_map(
                    fn (Language $l) => $l->lower(),
                    $language->targets,
                ),
            ],
            $languages,
        );

        return new Ok($response);
    }
}
