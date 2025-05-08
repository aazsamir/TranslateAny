<?php

declare(strict_types=1);

namespace Tests\Integration\Api\DeepL\V2\Document;

use Laminas\Diactoros\UploadedFile;
use Tempest\Http\GenericRequest;
use Tempest\Http\Method;
use Tests\Integration\TestCase;
use Tests\Mock\UploadMock;

class DocumentControllerTest extends TestCase
{
    public function test(): void
    {
        $response = $this->http->sendRequest(
            new GenericRequest(
                method: Method::POST,
                uri: '/deepl/v2/document',
                body: [
                    'target_lang' => 'EN',
                    'source_lang' => 'PL',
                ],
                headers: [
                    'Authorization' => 'DeepL-Auth-Key test',
                ],
                files: [
                    'file' => UploadMock::uploadedFile(),
                ],
            ),
        );

        $response->assertOk();
        $body = $response->body;

        $this->assertSame(
            [
                'document_id' => '123',
                'document_key' => 'placeholder',
            ],
            $body,
        );
    }
}
