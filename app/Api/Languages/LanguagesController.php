<?php

declare(strict_types=1);

namespace App\Api\Languages;

use App\Engine\AvailableLanguage;
use App\Engine\TranslateEngine;
use Tempest\Router\Get;
use Tempest\Router\Response;
use Tempest\Router\Responses\Ok;

readonly class LanguagesController
{
    public function __construct(
        private TranslateEngine $engine,
    ) {
    }

    #[Get('/languages')]
    public function __invoke(): Response
    {
        $languages = $this->engine->languages();

        $response = array_map(
            fn (AvailableLanguage $language) => [
                'code' => $language->language,
                'name' => $language->language,
                'targets' => $language->targets,
            ],
            $languages,
        );

        return new Ok($response);
    }
}
