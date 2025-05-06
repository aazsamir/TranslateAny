<?php

declare(strict_types=1);

namespace App\Api\Google\V2\Translate;

use App\Api\Google\V2\AuthMiddleware;
use App\Engine\TranslateEngine;
use App\Engine\TranslatePayload;
use App\Middleware\LogMiddleware;
use App\System\Language;
use Tempest\Http\Response;
use Tempest\Http\Responses\Ok;
use Tempest\Router\Post;

readonly class TranslateController
{
    public function __construct(
        private TranslateEngine $translate,
    ) {
    }

    #[Post(
        uri: '/google/v2/language/translate/v2',
        middleware: [
            LogMiddleware::class,
            AuthMiddleware::class,
        ],
    )]
    public function __invoke(TranslateRequest $request): Response
    {
        $translation = $this->translate->translate(
            new TranslatePayload(
                text: $request->q,
                targetLanguage: Language::fromAny($request->target),
                sourceLanguage: Language::tryFromAny($request->source),
            ),
        );

        $translations = [
            [
                'translatedText' => $translation->text,
                'detectedSourceLanguage' => $translation->detectedLanguage?->language->lower(),
            ],
        ];

        foreach ($translation->alternatives as $alt) {
            $translations[] = [
                'translatedText' => $alt,
                'detectedSourceLanguage' => $translation->detectedLanguage?->language->lower(),
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
