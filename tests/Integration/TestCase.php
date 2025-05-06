<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Auth\Authorizator;
use App\Auth\SingleKeyAuthorizator;
use App\Engine\DetectEngine;
use App\Engine\DocumentTranslateEngine;
use App\Engine\TranslateEngine;
use App\System\Glossary\GlossaryRepository;
use Tempest\Framework\Testing\IntegrationTest;
use Tempest\Log\Logger;
use Tests\Mock\DetectEngineMock;
use Tests\Mock\DocumentTranslateMock;
use Tests\Mock\GlossaryRepositoryMock;
use Tests\Mock\NullLogger;
use Tests\Mock\TranslateEngineMock;

abstract class TestCase extends IntegrationTest
{
    protected string $root = __DIR__ . '/../../';

    protected function setUp(): void
    {
        parent::setUp();

        $this->container->register(DetectEngine::class, function () {
            return new DetectEngineMock();
        });
        $this->container->register(TranslateEngine::class, function () {
            return new TranslateEngineMock();
        });
        $this->container->register(DocumentTranslateEngine::class, function () {
            return new DocumentTranslateMock();
        });
        $this->container->register(GlossaryRepository::class, function () {
            return new GlossaryRepositoryMock();
        });
        $this->container->register(Logger::class, function () {
            return new NullLogger();
        });
        $this->container->register(Authorizator::class, function () {
            return new SingleKeyAuthorizator('test');
        });
    }
}
