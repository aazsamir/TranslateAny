<?php

declare(strict_types=1);

namespace Tests\Mock;

use App\Engine\DetectEngine;
use App\Engine\Detection;
use App\System\Language;

/**
 * @internal
 */
class DetectEngineMock implements DetectEngine
{
    public array $detections = [];

    public function __construct()
    {
        $this->detections = [
            new Detection(
                language: Language::en,
                confidence: 0.5,
            ),
        ];
    }

    public function detect(string $text): array
    {
        return $this->detections;
    }
}
