<?php

declare(strict_types=1);

namespace App\Api\Google\V2\Translate;

use App\Engine\TranslateEngine;
use Tempest\Router\Post;
use Tempest\Router\Response;
use Tempest\Router\Responses\Ok;

readonly class TranslateController
{
    public function __construct(
        private TranslateEngine $translate,
    ) {
    }

    #[Post('/google/v2/language/translate/v2')]
    public function __invoke(TranslateRequest $request): Response
    {
        $translation = $this->translate->translate(
            text: $request->q,
            targetLanguage: $request->target,
            sourceLanguage: $request->source,
        );

        $translations = [
            [
                'translatedText' => $translation->text,
                'detectedSourceLanguage' => $translation->detectedLanguage->language,
            ],
        ];

        foreach ($translation->alternatives as $alt) {
            $translations[] = [
                'translatedText' => $alt,
                'detectedSourceLanguage' => $translation->detectedLanguage->language,
            ];
        }

        $response = [
            'data' => [
                'translations' => $translations,
            ],
        ];

        return new Ok($response);
    }
}
