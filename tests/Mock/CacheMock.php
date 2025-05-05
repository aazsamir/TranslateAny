<?php

declare(strict_types=1);

namespace Tests\Mock;

use Closure;
use DateTimeInterface as GlobalDateTimeInterface;
use Psr\Cache\CacheItemInterface;
use Tempest\Cache\Cache;
use Tempest\DateTime\DateTimeInterface;

class CacheMock implements Cache
{
    public array $cache = [];
    public bool $enabled = true;

    public function put(string $key, mixed $value, ?DateTimeInterface $expiresAt = null): CacheItemInterface
    {
        $this->cache[$key] = $value;

        return new class($key, $value) implements CacheItemInterface {
            public function __construct(
                private string $key,
                private mixed $value,
            ) {
            }

            public function getKey(): string
            {
                return $this->key;
            }

            public function get(): mixed
            {
                return $this->value;
            }

            public function isHit(): bool
            {
                return true;
            }

            public function set(mixed $value): static
            {
                $this->value = $value;

                return $this;
            }

            public function expiresAt(?GlobalDateTimeInterface $expiration): static
            {
                // No-op for mock
                return $this;
            }

            public function expiresAfter(mixed $time): static
            {
                // No-op for mock
                return $this;
            }
        };
    }

    public function get(string $key): mixed
    {
        return $this->cache[$key] ?? null;
    }

    public function resolve(string $key, Closure $cache, ?DateTimeInterface $expiresAt = null): mixed
    {
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        $value = $cache();
        $this->put($key, $value, $expiresAt);

        return $value;
    }

    public function remove(string $key): void
    {
        unset($this->cache[$key]);
    }

    public function clear(): void
    {
        $this->cache = [];
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
