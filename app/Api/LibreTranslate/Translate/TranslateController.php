<?php

declare(strict_types=1);

namespace App\Api\LibreTranslate\Translate;

use App\Engine\TranslateEngine;
use App\Engine\TranslatePayload;
use App\Middleware\LogMiddleware;
use App\System\Language;
use Tempest\Http\Response;
use Tempest\Http\Responses\Ok;
use Tempest\Router\Get;
use Tempest\Router\Post;

readonly class TranslateController
{
    public function __construct(
        private TranslateEngine $engine,
    ) {
    }

    #[Post(
        uri: '/libre/translate',
        middleware: [
            LogMiddleware::class,
        ],
    )]
    #[Get(
        uri: '/libre/translate',
        middleware: [
            LogMiddleware::class,
        ],
    )]
    public function __invoke(TranslateRequest $request): Response
    {
        return $this->handle($request);
    }

    private function handle(TranslateRequest $request): Response
    {
        $source = $request->source === 'auto' ? null : $request->source;
        $translation = $this->engine->translate(
            new TranslatePayload(
                $request->q,
                Language::fromAny($request->target),
                Language::tryFromAny($source),
                $request->format,
                $request->alternatives,
            ),
        );

        $response = [
            'translatedText' => $translation->text,
        ];

        if ($translation->alternatives) {
            $response['alternatives'] = $translation->alternatives;
        }

        if ($translation->detectedLanguage) {
            $response['detectedLanguage'] = [
                'confidence' => $translation->detectedLanguage->confidence,
                'language' => $translation->detectedLanguage->language->lower(),
            ];
        }

        return new Ok($response);
    }
}
