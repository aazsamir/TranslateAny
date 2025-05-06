<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\Auth\NoopAuthorizator;
use Tests\TestCase;

class NoopAuthorizatorTest extends TestCase
{
    public function test(): void
    {
        $this->assertTrue(
            new NoopAuthorizator()->isAuthenticated(null),
        );
    }
}
