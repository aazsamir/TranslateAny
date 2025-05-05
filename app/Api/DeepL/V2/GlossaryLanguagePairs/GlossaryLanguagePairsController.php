<?php

declare(strict_types=1);

namespace App\Api\DeepL\V2\GlossaryLanguagePairs;

use App\Engine\TranslateEngine;
use Tempest\Http\Response;
use Tempest\Http\Responses\Ok;
use Tempest\Router\Get;

readonly class GlossaryLanguagePairsController
{
    public function __construct(
        private TranslateEngine $translateEngine,
    ) {
    }

    #[Get('/deepl/v2/glossary-language-pairs')]
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
