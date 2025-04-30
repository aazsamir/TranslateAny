<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Engine\DetectionEngine;
use App\Engine\TranslateEngine;
use Tempest\Framework\Testing\IntegrationTest;
use Tests\Mock\DetectionEngineMock;
use Tests\Mock\TranslateEngineMock;

abstract class TestCase extends IntegrationTest
{
    protected string $root = __DIR__ . '/../../';

    protected function setUp(): void
    {
        parent::setUp();

        $this->container->register(DetectionEngine::class, function () {
            return new DetectionEngineMock();
        });
        $this->container->register(TranslateEngine::class, function () {
            return new TranslateEngineMock();
        });
    }

    protected function tearDown(): void
    {
        // tempest doesn't remove it's own error and exception handlers
        // and it is considered risky
        // so this is a temporary workaround
        parent::tearDown();

        $activeErrorHandlers = $this->activeErrorHandlers();

        foreach ($activeErrorHandlers as $handler) {
            restore_error_handler();

            if (count($this->activeErrorHandlers()) === 1) {
                break;
            }
        }

        $activeExceptionHandlers = $this->activeExceptionHandlers();

        foreach ($activeExceptionHandlers as $handler) {
            restore_exception_handler();

            if (count($this->activeExceptionHandlers()) === 1) {
                break;
            }
        }
    }

    /**
     * @return list<callable>
     */
    private function activeErrorHandlers(): array
    {
        $activeErrorHandlers = [];

        while (true) {
            $previousHandler = set_error_handler(static fn () => false);

            restore_error_handler();

            if ($previousHandler === null) {
                break;
            }

            $activeErrorHandlers[] = $previousHandler;

            restore_error_handler();
        }

        $activeErrorHandlers = array_reverse($activeErrorHandlers);
        $invalidErrorHandlerStack = false;

        foreach ($activeErrorHandlers as $handler) {
            if (! is_callable($handler)) {
                $invalidErrorHandlerStack = true;

                continue;
            }

            set_error_handler($handler);
        }

        if ($invalidErrorHandlerStack) {
            throw new \Exception('At least one error handler is not callable outside the scope it was registered in');
        }

        return $activeErrorHandlers;
    }

    /**
     * @return list<callable>
     */
    private function activeExceptionHandlers(): array
    {
        $res = [];

        while (true) {
            $previousHandler = set_exception_handler(static fn () => null);
            restore_exception_handler();

            if ($previousHandler === null) {
                break;
            }
            $res[] = $previousHandler;
            restore_exception_handler();
        }
        $res = array_reverse($res);

        foreach ($res as $handler) {
            set_exception_handler($handler);
        }

        return $res;
    }
}
