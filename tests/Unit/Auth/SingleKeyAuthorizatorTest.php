<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\Auth\Credentials;
use App\Auth\SingleKeyAuthorizator;
use Tests\TestCase;

class SingleKeyAuthorizatorTest extends TestCase
{
    private SingleKeyAuthorizator $authorizator;

    protected function setUp(): void
    {
        $this->authorizator = new SingleKeyAuthorizator('test');
    }

    public function testNull(): void
    {
        $this->assertFalse($this->authorizator->isAuthenticated(null));
    }

    public function testValidKey(): void
    {
        $this->assertTrue($this->authorizator->isAuthenticated(new Credentials('test')));
    }

    public function testInvalidKey(): void
    {
        $this->assertFalse($this->authorizator->isAuthenticated(new Credentials('invalid')));
    }
}
