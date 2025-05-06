<?php

declare(strict_types=1);

namespace Tests\Integration\Api\DeepL\V2\Document\Result;

use Tests\Integration\TestCase;

class DocumentResultControllerTest extends TestCase
{
    public function test(): void
    {
        $response = $this->http->post('/deepl/v2/document/123/result', headers: [
            'Authorization' => 'DeepL-Auth-Key test',
        ]);

        $response->assertOk();
    }
}
