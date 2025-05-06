<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\Auth\Credentials;
use Tempest\Http\RequestHeaders;
use Tests\TestCase;

class CredentialsTest extends TestCase
{
    public function testFromBearer(): void
    {
        $headers = RequestHeaders::normalizeFromArray([
            'Authorization' => 'Bearer test',
        ]);

        $credentials = Credentials::fromBearer($headers);

        $this->assertEquals('test', $credentials->token);
    }

    public function testNoHeader(): void
    {
        $headers = RequestHeaders::normalizeFromArray([]);

        $credentials = Credentials::fromBearer($headers);

        $this->assertNull($credentials);
    }

    public function testNoBearer(): void
    {
        $headers = RequestHeaders::normalizeFromArray([
            'Authorization' => 'test',
        ]);

        $credentials = Credentials::fromBearer($headers);

        $this->assertNull($credentials);
    }

    public function testEmptyBearer(): void
    {
        $headers = RequestHeaders::normalizeFromArray([
            'Authorization' => 'Bearer ',
        ]);

        $credentials = Credentials::fromBearer($headers);

        $this->assertNull($credentials);
    }
}
