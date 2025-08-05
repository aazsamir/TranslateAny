<?php

declare(strict_types=1);

namespace Tests\Mock;

use Closure;
use DateTimeInterface as GlobalDateTimeInterface;
use Psr\Cache\CacheItemInterface;
use Stringable;
use Tempest\Cache\Cache;
use Tempest\Cache\Lock;
use Tempest\DateTime\DateTimeInterface;
use Tempest\DateTime\Duration;

class CacheMock implements Cache
{
    public array $cache = [];
    public bool $enabled = true;

    public function lock(Stringable|string $key, null|Duration|DateTimeInterface $expiration = null, null|Stringable|string $owner = null): Lock
    {
        throw new \RuntimeException('Locking is not implemented in CacheMock.');
    }

    public function putMany(iterable $values, null|Duration|DateTimeInterface $expiration = null): array
    {
        $items = [];

        foreach ($values as $key => $value) {
            $items[] = $this->put($key, $value, $expiration);
        }

        return $items;
    }

    public function getMany(iterable $key): array
    {
        $results = [];

        foreach ($key as $k) {
            $results[$k] = $this->cache[$k] ?? null;
        }

        return $results;
    }

    public function has(Stringable|string $key): bool
    {
        return isset($this->cache[$key]);
    }

    public function increment(Stringable|string $key, int $by = 1): int
    {
        if (! isset($this->cache[$key])) {
            $this->cache[$key] = 0;
        }

        return $this->cache[$key] += $by;
    }

    public function decrement(Stringable|string $key, int $by = 1): int
    {
        if (! isset($this->cache[$key])) {
            $this->cache[$key] = 0;
        }

        return $this->cache[$key] -= $by;
    }

    public function put(Stringable|string $key, mixed $value, null|Duration|DateTimeInterface $expiration = null): CacheItemInterface
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

    public function get(Stringable|string $key): mixed
    {
        return $this->cache[$key] ?? null;
    }

    public function resolve(Stringable|string $key, Closure $callback, null|Duration|DateTimeInterface $expiration = null, ?Duration $stale = null): mixed
    {
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        $value = $callback();
        $this->put($key, $value, $expiration);

        return $value;
    }

    public function remove(Stringable|string $key): void
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
