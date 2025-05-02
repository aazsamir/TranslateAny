<?php

declare(strict_types=1);

namespace App\Api\DeepL\V2\Translate;

use App\Engine\TranslateEngine;
use App\Engine\TranslatePayload;
use App\System\Language;
use Tempest\Router\Post;
use Tempest\Router\Response;
use Tempest\Router\Responses\Ok;

readonly class TranslateController
{
    public function __construct(
        private TranslateEngine $translate,
    ) {
    }

    #[Post('/deepl/v2/translate')]
    public function __invoke(TranslateRequest $request): Response
    {
        $translations = [];

        foreach ($request->text as $text) {
            $translations[] = $this->translate->translate(
                new TranslatePayload(
                    text: $text,
                    targetLanguage: Language::fromAny($request->target_lang),
                    sourceLanguage: Language::fromAny($request->source_lang),
                ),
            );
        }

        $response = [];

        foreach ($translations as $translation) {
            $response[] = [
                'text' => $translation->text,
                'detected_source_language' => $translation->detectedLanguage->language->upper(),
                'billed_characters' => 0,
            ];
        }

        $response = [
            'translations' => $response,
        ];

        return new Ok($response);
    }
}
