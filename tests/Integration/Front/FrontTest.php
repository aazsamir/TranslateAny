<?php

declare(strict_types=1);

namespace Tests\Integration\Front;

use Tests\Integration\TestCase;
use Tests\IntegrationTestCase;

/**
 * @internal
 */
final class FrontTest extends TestCase
{
    public function test_index(): void
    {
        $this->http->get('/')->assertOk()->assertSee('TranslateAny');
    }
}
