<?php

declare(strict_types=1);

namespace App\Api\DeepLX\Translate;

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
        uri: '/deeplx/translate',
        middleware: [
            LogMiddleware::class,
        ],
    )]
    #[Post(
        uri: '/deeplx/v1/translate',
        middleware: [
            LogMiddleware::class,
        ],
    )]
    public function __invoke(TranslateRequest $request): Response
    {
        $payload = new TranslatePayload(
            text: $request->text,
            targetLanguage: Language::fromAny($request->target_lang),
            sourceLanguage: Language::tryFromAny($request->source_lang),
        );
        $translation = $this->translate->translate($payload);

        $response = [
            'alternatives' => $translation->alternatives,
            'code' => 200,
            'data' => $translation->text,
            'id' => \random_int(0, \PHP_INT_MAX),
            'method' => 'Free',
            'source_lang' => $payload->sourceLanguage?->upper(),
            'target_lang' => $payload->targetLanguage->upper(),
        ];

        return new Ok($response);
    }
}
