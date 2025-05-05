<?php

declare(strict_types=1);

namespace Tests\Unit\Engine\OpenAI;

use App\Engine\OpenAI\ClientFactory;
use Tests\TestCase;

class ClientFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $client = ClientFactory::make(
            host: 'https://api.openai.com',
            apiKey: '123',
        );

        $this->assertNotNull($client);
    }

    public function testCreateWithoutKey(): void
    {
        $client = ClientFactory::make(
            host: 'https://api.openai.com',
        );

        $this->assertNotNull($client);
    }
}
