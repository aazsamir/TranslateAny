<?php

declare(strict_types=1);

namespace Tests\Unit\Engine\Chat;

use App\Engine\Chat\ChatDocumentTranslateEngine;
use App\Engine\DocumentTranslatePayload;
use App\Engine\DocumentTranslation;
use App\System\Logger\MemoryLogger;
use Laminas\Diactoros\UploadedFile;
use Tempest\Http\Upload;
use Tests\Mock\ChatClientMock;
use Tests\Mock\ChunkingStrategyFactoryMock;
use Tests\Mock\DocumentStorageMock;
use Tests\Mock\TextExtractorMock;
use Tests\TestCase;
use Tests\Unit\Utils\DocumentTranslatePayloadFixture;

class ChatDocumentTranslateEngineTest extends TestCase
{
    private ChatClientMock $client;
    private TextExtractorMock $textExtractor;
    private DocumentStorageMock $documentStorage;
    private MemoryLogger $logger;
    private ChunkingStrategyFactoryMock $chunkingStrategyFactory;
    private ChatDocumentTranslateEngine $engine;

    protected function setUp(): void
    {
        $this->client = new ChatClientMock();
        $this->textExtractor = new TextExtractorMock();
        $this->documentStorage = new DocumentStorageMock();
        $this->logger = new MemoryLogger();
        $this->chunkingStrategyFactory = new ChunkingStrategyFactoryMock();
        $this->engine = new ChatDocumentTranslateEngine(
            $this->client,
            $this->textExtractor,
            $this->documentStorage,
            $this->logger,
            $this->chunkingStrategyFactory,
            'system prompt',
        );

        $this->textExtractor->extracted = [
            'page 1',
            'page 2',
        ];
    }

    public function test(): void
    {
        $result = $this->engine->translateDocument(
            DocumentTranslatePayloadFixture::get(),
        );

        $this->assertEquals('123', $result->id);

        $this->assertCount(2, $this->documentStorage->pages);
    }

    public function testGetDownloadPath(): void
    {
        $result = $this->engine->downloadPath(new DocumentTranslation('123'));

        $this->assertEquals('download_path/123', $result);
    }
}