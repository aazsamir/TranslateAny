<?php

declare(strict_types=1);

namespace App\Engine\Route;

use App\Engine\TranslateEngine;
use App\Engine\TranslatePayload;
use App\Engine\Translation;

readonly class RouteEngine implements TranslateEngine
{
    /**
     * @param TranslateRoute[] $routes,
     */
    public function __construct(
        private array $routes,
    ) {
    }

    public static function new(TranslateRoute ...$routes): self
    {
        return new self($routes);
    }

    public function translate(TranslatePayload $payload): Translation
    {
        foreach ($this->routes as $route) {
            if ($route->supports($payload->targetLanguage)) {
                return $route->engine->translate($payload);
            }
        }

        throw new \RuntimeException('Language not supported');
    }

    public function languages(): array
    {
        $languages = [];

        foreach ($this->routes as $route) {
            $languages = array_merge($languages, $route->engine->languages());
        }

        return array_unique($languages);
    }
}
