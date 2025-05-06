<?php

declare(strict_types=1);

namespace Tests\Integration\Api\DeepL\V2\GlossaryLanguagePairs;

use Tests\Integration\TestCase;

class GlossaryLanguagePairsControllerTest extends TestCase
{
    public function test(): void
    {
        $response = $this->http->get('/deepl/v2/glossary-language-pairs', headers: [
            'Authorization' => 'DeepL-Auth-Key test',
        ]);

        $response->assertOk();
        $this->assertEquals(
            [
                'supported_languages' => [
                    [
                        'source_lang' => 'en',
                        'target_lang' => 'pl',
                    ],
                    [
                        'source_lang' => 'pl',
                        'target_lang' => 'en',
                    ],
                ],
            ],
            $response->body,
        );
    }
}
