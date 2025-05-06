<?php

declare(strict_types=1);

namespace App\Api\DeepL\V2\Translate;

use App\Api\DeepL\AuthMiddleware;
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
        uri: '/deepl/v2/translate',
        middleware: [
            LogMiddleware::class,
            AuthMiddleware::class,
        ],
    )]
    public function __invoke(TranslateRequest $request): Response
    {
        $translations = [];

        foreach ($request->text as $text) {
            $translations[] = $this->translate->translate(
                new TranslatePayload(
                    text: $text,
                    targetLanguage: Language::fromAny($request->target_lang),
                    sourceLanguage: Language::tryFromAny($request->source_lang),
                    glossaryId: $request->glossary_id,
                ),
            );
        }

        $response = [];

        foreach ($translations as $translation) {
            $response[] = [
                'text' => $translation->text,
                'detected_source_language' => $translation->detectedLanguage?->language->upper(),
                'billed_characters' => 0,
            ];
        }

        $response = [
            'translations' => $response,
        ];

        return new Ok($response);
    }
}
