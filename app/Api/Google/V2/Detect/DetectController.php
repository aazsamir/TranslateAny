<?php

declare(strict_types=1);

namespace App\Api\Google\V2\Detect;

use App\Engine\DetectionEngine;
use Tempest\Router\Post;
use Tempest\Router\Response;
use Tempest\Router\Responses\Ok;

readonly class DetectController
{
    public function __construct(
        private DetectionEngine $detection,
    ) {
    }

    #[Post('/google/v2/language/translate/v2/detect')]
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
