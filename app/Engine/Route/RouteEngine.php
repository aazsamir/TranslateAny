<?php

declare(strict_types=1);

namespace App\Engine\Route;

use App\Engine\TranslateEngine;
use App\Engine\TranslatePayload;
use App\Engine\Translation;
use App\System\Logger\PrefixLogger;
use Tempest\Log\Logger;

use function Tempest\get;

readonly class RouteEngine implements TranslateEngine
{
    use PrefixLogger;

    /**
     * @param TranslateRoute[] $routes
     */
    public function __construct(
        private array $routes,
        private Logger $logger,
    ) {
    }

    public static function new(TranslateRoute ...$routes): self
    {
        $lazy = new \ReflectionClass(self::class);
        $lazy = $lazy->newLazyGhost(function (RouteEngine $object) use ($routes) {
            $object->__construct(
                routes: $routes,
                logger: get(Logger::class),
            );
        });

        return $lazy;
    }

    public function translate(TranslatePayload $payload): Translation
    {
        foreach ($this->routes as $route) {
            if ($route->supports($payload->targetLanguage)) {
                $this->logger->debug(
                    $this->prefixLog(
                        'Route',
                        'matched',
                    ),
                    [
                        'lang' => $payload->targetLanguage->lower(),
                        'route' => $route->engine::class,
                    ],
                );

                return $route->engine->translate($payload);
            }
        }

        throw new RouteException('Language not supported');
    }

    public function languages(): array
    {
        $languages = [];

        foreach ($this->routes as $route) {
            foreach ($route->engine->languages() as $language) {
                $languages[$language->language->lower()] = $language;
            }
        }

        return \array_values($languages);
    }
}
