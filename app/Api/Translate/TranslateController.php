<?php

declare(strict_types=1);

namespace App\Api\Translate;

use App\Engine\TranslateEngine;
use Tempest\Http\Method;
use Tempest\Http\Status;
use Tempest\Router\GenericResponse;
use Tempest\Router\Get;
use Tempest\Router\Post;
use Tempest\Router\Response;
use Tempest\Router\Responses\Ok;

readonly class TranslateController
{
    public function __construct(
        private TranslateEngine $engine,
    ) {
    }

    #[Post('/translate')]
    public function __invoke(TranslateRequest $request): Response
    {
        return $this->handle($request);
    }

    #[Get('/translate')]
    public function invokeGet(): Response
    {
        $q = $_GET['q'] ?? '';
        $source = $_GET['source'] ?? 'auto';
        $target = $_GET['target'] ?? '';

        if ($q === '') {
            return new GenericResponse(
                status: Status::BAD_REQUEST,
                body: [
                    'error' => 'Missing query parameter "q".',
                ],
            );
        }

        if ($target === '') {
            return new GenericResponse(
                status: Status::BAD_REQUEST,
                body: [
                    'error' => 'Missing query parameter "target".',
                ],
            );
        }

        $request = new TranslateRequest(
            method: Method::GET,
            uri: '/translate',
        );
        $request->q = $q;
        $request->source = $source;
        $request->target = $target;

        return $this->handle($request);
    }

    private function handle(TranslateRequest $request): Response
    {
        $source = $request->source === 'auto' ? null : $request->source;
        $translation = $this->engine->translate(
            $request->q,
            $request->target,
            $source,
            $request->format,
            $request->alternatives,
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
                'language' => $translation->detectedLanguage->language,
            ];
        }

        return new Ok($response);
    }
}
