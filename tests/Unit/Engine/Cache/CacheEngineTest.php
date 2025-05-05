<?php

declare(strict_types=1);

namespace Tests\Unit\Engine\Cache;

use App\Engine\Cache\CacheEngine;
use Tempest\Cache\Cache;
use Tests\Mock\CacheMock;
use Tests\Mock\NullLogger;
use Tests\Mock\TranslateEngineMock;
use Tests\TestCase;
use Tests\Unit\Utils\TranslatePayloadFixture;

class CacheEngineTest extends TestCase
{
    private CacheMock $cache;
    private CacheEngine $engine;

    protected function setUp(): void
    {
        $this->cache = new CacheMock();
        $this->engine = new CacheEngine($this->cache, new TranslateEngineMock(), new NullLogger());
    }

    public function testTranslateWithoutCache(): void
    {
        $payload = TranslatePayloadFixture::get();

        $translation = $this->engine->translate($payload);

        $this->assertEquals('Hello world!', $translation->text);
    }

    public function testTranslateWithoutCacheWithCache(): void
    {
        $payload = TranslatePayloadFixture::get();

        // warm up the cache
        $this->engine->translate($payload);
        $translation = $this->engine->translate($payload);

        $this->assertCount(1, $this->cache->cache);
        $this->assertEquals('Hello world!', $translation->text);
    }
}
