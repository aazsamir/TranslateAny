<?php

declare(strict_types=1);

namespace App\Api\DeepL\V2\Document;

use App\Api\DeepL\AuthMiddleware;
use App\Engine\DocumentTranslateEngine;
use App\Engine\DocumentTranslatePayload;
use App\Middleware\LogMiddleware;
use App\System\Language;
use Tempest\Http\Response;
use Tempest\Http\Responses\Ok;
use Tempest\Http\Responses\ServerError;
use Tempest\Router\Post;

readonly class DocumentController
{
    public function __construct(
        private DocumentTranslateEngine $documentTranslate,
    ) {
    }

    #[Post(
        uri: '/deepl/v2/document',
        middleware: [
            LogMiddleware::class,
            AuthMiddleware::class,
        ],
    )]
    public function __invoke(DocumentRequest $request): Response
    {
        $file = $request->files['file'] ?? null;

        if (! $file) {
            return new ServerError('file required');
        }

        $payload = new DocumentTranslatePayload(
            file: $file,
            targetLanguage: Language::fromAny($request->target_lang),
        );
        $translation = $this->documentTranslate->translateDocument($payload);
        $response = [
            'document_id' => $translation->id,
            'document_key' => 'placeholder',
        ];

        return new Ok($response);
    }
}
