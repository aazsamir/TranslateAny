<?php

declare(strict_types=1);

namespace App\Engine\Cache;

use App\Engine\TranslateEngine;
use App\Engine\TranslatePayload;
use App\Engine\Translation;
use App\System\Logger\PrefixLogger;
use DateTimeImmutable;
use ReflectionClass;
use Tempest\Cache\Cache;
use Tempest\DateTime\DateTime;
use Tempest\Log\Logger;

use function Tempest\get;

readonly class CacheEngine implements TranslateEngine
{
    use PrefixLogger;

    public function __construct(
        private Cache $cache,
        private TranslateEngine $engine,
        private Logger $logger,
        private int $cacheMinutes = 5,
    ) {
    }

    public static function new(
        TranslateEngine $engine,
        int $cacheMinutes = 5,
    ): self {
        $lazy = new ReflectionClass(self::class);
        $lazy = $lazy->newLazyGhost(function (CacheEngine $object) use ($engine, $cacheMinutes) {
            $object->__construct(
                cache: get(Cache::class),
                logger: get(Logger::class),
                engine: $engine,
                cacheMinutes: $cacheMinutes,
            );
        });

        return $lazy;
    }

    public function translate(TranslatePayload $payload): Translation
    {
        $key = [
            'text' => $payload->text,
            'targetLanguage' => $payload->targetLanguage->lower(),
            'sourceLanguage' => $payload->sourceLanguage?->lower(),
            'format' => $payload->format,
            'alternatives' => $payload->alternatives,
        ];
        $key = md5(json_encode($key, JSON_THROW_ON_ERROR));

        /** @var ?Translation */
        $cached = $this->cache->get($key);

        if ($cached !== null) {
            $this->logger->debug(
                $this->prefixLog(
                    'Cache',
                    'Cache hit',
                ),
                [
                    'key' => $key,
                ],
            );

            return $cached;
        }

        $this->logger->debug(
            $this->prefixLog(
                'Cache',
                'Cache miss',
            ),
            [
                'key' => $key,
            ],
        );

        $translation = $this->engine->translate($payload);

        $this->cache->put(
            key: $key,
            value: $translation,
            expiresAt: DateTime::now()->plusMinutes($this->cacheMinutes),
        );

        return $translation;
    }

    public function languages(): array
    {
        return $this->engine->languages();
    }
}
