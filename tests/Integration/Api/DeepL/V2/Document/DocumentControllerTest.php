<?php

declare(strict_types=1);

namespace Tests\Integration\Api\DeepL\V2\Document;

use Laminas\Diactoros\UploadedFile;
use Tempest\Http\GenericRequest;
use Tempest\Http\Method;
use Tests\Integration\TestCase;

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
                    'file' => new UploadedFile(
                        streamOrFile: \realpath(__DIR__ . '/../../../../../Mock/file'),
                        size: null,
                        clientFilename: 'upload',
                        errorStatus: 0,
                    ),
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
