<?php

declare(strict_types=1);

namespace Tests\Integration\Api\DeepL\V2\Glossaries;

use Tempest\Http\Status;
use Tests\Integration\TestCase;

class GlossariesControllerTest extends TestCase
{
    public function testPost(): void
    {
        $response = $this->http->post(
            '/deepl/v2/glossaries',
            [
                'name' => 'test',
                'source_lang' => 'en',
                'target_lang' => 'pl',
                'entries' => 'test1,test2',
                'entries_format' => 'csv',
            ],
            headers: [
                'Authorization' => 'DeepL-Auth-Key test',
            ],
        );

        $response->assertStatus(Status::CREATED);

        $this->assertEquals('2', $response->body['glossary_id']);
        $this->assertEquals('test', $response->body['name']);
        $this->assertEquals('en', $response->body['source_lang']);
        $this->assertEquals('pl', $response->body['target_lang']);
        $this->assertEquals(1, $response->body['entry_count']);
        $this->assertNotEmpty($response->body['creation_time']);
    }

    public function testGet(): void
    {
        $response = $this->http->get(
            '/deepl/v2/glossaries',
            headers: [
                'Authorization' => 'DeepL-Auth-Key test',
            ],
        );

        $response->assertOk();

        $this->assertEquals('1', $response->body['glossaries'][0]['glossary_id']);
        $this->assertEquals('stub', $response->body['glossaries'][0]['name']);
        $this->assertEquals('EN', $response->body['glossaries'][0]['source_lang']);
        $this->assertEquals('PL', $response->body['glossaries'][0]['target_lang']);
        $this->assertEquals(1, $response->body['glossaries'][0]['entry_count']);
        $this->assertNotEmpty($response->body['glossaries'][0]['creation_time']);
    }

    public function testGetById(): void
    {
        $response = $this->http->get(
            '/deepl/v2/glossaries/1',
            headers: [
                'Authorization' => 'DeepL-Auth-Key test',
            ],
        );

        $response->assertOk();

        $this->assertEquals('1', $response->body['glossary_id']);
        $this->assertEquals('stub', $response->body['name']);
        $this->assertEquals('EN', $response->body['source_lang']);
        $this->assertEquals('PL', $response->body['target_lang']);
        $this->assertEquals(1, $response->body['entry_count']);
        $this->assertNotEmpty($response->body['creation_time']);
    }

    public function testDelete(): void
    {
        $response = $this->http->delete(
            '/deepl/v2/glossaries/1',
            headers: [
                'Authorization' => 'DeepL-Auth-Key test',
            ],
        );
        $response->assertStatus(Status::NO_CONTENT);
    }

    public function testEntries(): void
    {
        $response = $this->http->get(
            '/deepl/v2/glossaries/1/entries',
            headers: [
                'Authorization' => 'DeepL-Auth-Key test',
            ],
        );

        $response->assertOk();

        $this->assertEquals("hello\thej", $response->body);
    }
}
