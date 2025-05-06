<?php

declare(strict_types=1);

namespace App\Api\Google\V2\Detect;

use App\Api\Google\V2\AuthMiddleware;
use App\Engine\DetectEngine;
use App\Middleware\LogMiddleware;
use Tempest\Http\Response;
use Tempest\Http\Responses\Ok;
use Tempest\Router\Post;

readonly class DetectController
{
    public function __construct(
        private DetectEngine $detection,
    ) {
    }

    #[Post(
        uri: '/google/v2/language/translate/v2/detect',
        middleware: [
            LogMiddleware::class,
            AuthMiddleware::class,
        ],
    )]
    public function __invoke(DetectRequest $request): Response
    {
        $detection = $this->detection->detect(
            text: $request->q,
        );

        $detections = [];

        foreach ($detection as $det) {
            $detections[] = [
                'language' => $det->language->lower(),
                'isReliable' => false,
                'confidence' => $det->confidence,
            ];
        }

        $response = [
            'data' => [
                'detections' => $detections,
            ],
        ];

        return new Ok($response);
    }
}
