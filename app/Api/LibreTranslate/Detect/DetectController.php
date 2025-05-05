<?php

declare(strict_types=1);

namespace App\Api\LibreTranslate\Detect;

use App\Engine\Detection;
use App\Engine\DetectionEngine;
use App\Middleware\LogMiddleware;
use Tempest\Http\Response;
use Tempest\Http\Responses\Ok;
use Tempest\Router\Post;

readonly class DetectController
{
    public function __construct(
        private DetectionEngine $detectionEngine,
    ) {
    }

    #[Post(
        uri: '/libre/detect',
        middleware: [
            LogMiddleware::class,
        ],
    )]
    public function __invoke(DetectRequest $request): Response
    {
        $detections = $this->detectionEngine->detect($request->q);

        $response = array_map(
            fn (Detection $detection) => [
                'confidence' => $detection->confidence,
                'language' => $detection->language->lower(),
            ],
            $detections,
        );

        return new Ok($response);
    }
}
