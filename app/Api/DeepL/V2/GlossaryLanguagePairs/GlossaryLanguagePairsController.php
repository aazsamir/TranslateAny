<?php

declare(strict_types=1);

namespace App\Api\DeepL\V2\GlossaryLanguagePairs;

use App\Api\DeepL\AuthMiddleware;
use App\Engine\TranslateEngine;
use App\Middleware\LogMiddleware;
use Tempest\Http\Response;
use Tempest\Http\Responses\Ok;
use Tempest\Router\Get;

readonly class GlossaryLanguagePairsController
{
    public function __construct(
        private TranslateEngine $translateEngine,
    ) {
    }

    #[Get(
        uri: '/deepl/v2/glossary-language-pairs',
        middleware: [
            LogMiddleware::class,
            AuthMiddleware::class,
        ],
    )]
    public function __invoke(): Response
    {
        $languages = $this->translateEngine->languages();
        $response = [];

        foreach ($languages as $language) {
            foreach ($language->targets as $target) {
                $response[] = [
                    'source_lang' => $language->language->lower(),
                    'target_lang' => $target->lower(),
                ];
            }
        }

        $response = [
            'supported_languages' => $response,
        ];

        return new Ok($response);
    }
}
