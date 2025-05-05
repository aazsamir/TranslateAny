<?php

declare(strict_types=1);

namespace Tests\Integration\Front;

use Tests\Integration\TestCase;

/**
 * @internal
 */
final class FrontTest extends TestCase
{
    public function testIndex(): void
    {
        $this->http->get('/')->assertOk()->assertSee('TranslateAny');
    }

    public function testDocuments(): void
    {
        $this->http->get('/documents')->assertOk()->assertSee('TranslateAny');
    }
}
