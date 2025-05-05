<?php

declare(strict_types=1);

namespace App\Api\DeepL\V2\Document\Result;

use App\Engine\DocumentTranslateEngine;
use App\Engine\DocumentTranslation;
use App\Middleware\LogMiddleware;
use Tempest\Http\Response;
use Tempest\Http\Responses\Download;
use Tempest\Router\Get;
use Tempest\Router\Post;

readonly class DocumentResultController
{
    public function __construct(
        private DocumentTranslateEngine $documentTranslateEngine,
    ) {
    }

    #[Post(
        uri: '/deepl/v2/document/{documentId}/result',
        middleware: [
            LogMiddleware::class,
        ],
    )]
    #[Get(
        uri: '/deepl/v2/document/{documentId}/result',
        middleware: [
            LogMiddleware::class,
        ],
    )]
    public function __invoke(string $documentId): Response
    {
        $file = $this->documentTranslateEngine->downloadPath(
            new DocumentTranslation(
                id: $documentId,
            ),
        );

        return new Download(path: $file);
    }
}
