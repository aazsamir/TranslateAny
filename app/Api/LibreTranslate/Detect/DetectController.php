<?php

declare(strict_types=1);

namespace App\Api\LibreTranslate\Detect;

use App\Engine\Detection;
use App\Engine\DetectionEngine;
use Tempest\Router\Post;
use Tempest\Router\Response;
use Tempest\Router\Responses\Ok;

readonly class DetectController
{
    public function __construct(
        private DetectionEngine $detectionEngine,
    ) {
    }

    #[Post('/libre/detect')]
    public function __invoke(DetectRequest $request): Response
    {
        $detections = $this->detectionEngine->detect($request->q);

        $response = array_map(
            fn (Detection $detection) => [
                'confidence' => $detection->confidence,
                'language' => $detection->language,
            ],
            $detections,
        );

        return new Ok($response);
    }
}
