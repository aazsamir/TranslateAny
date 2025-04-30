<?php

declare(strict_types=1);

namespace Tests\Mock;

use App\Engine\Detection;
use App\Engine\DetectionEngine;

/**
 * @internal
 */
class DetectionEngineMock implements DetectionEngine
{
    public array $detections = [];

    public function __construct()
    {
        $this->detections = [
            new Detection(
                language: 'en',
                confidence: 0.5,
            ),
        ];
    }

    public function detect(string $text): array
    {
        return $this->detections;
    }
}
