<?php

declare(strict_types=1);

namespace App\Api\DeepL\V2\Glossaries;

use App\Middleware\LogMiddleware;
use App\System\Glossary\Glossary;
use App\System\Glossary\GlossaryRepository;
use App\System\Language;
use DateTimeImmutable;
use DateTimeInterface;
use Tempest\Http\GenericResponse;
use Tempest\Http\Response;
use Tempest\Http\Responses\Created;
use Tempest\Http\Responses\NotFound;
use Tempest\Http\Responses\Ok;
use Tempest\Http\Status;
use Tempest\Router\Delete;
use Tempest\Router\Get;
use Tempest\Router\Post;

readonly class GlossariesController
{
    public function __construct(
        private GlossaryRepository $glossaryRepository,
        private GlossaryService $glossaryService,
    ) {
    }

    #[Post(
        uri: '/deepl/v2/glossaries',
        middleware: [
            LogMiddleware::class,
        ],
    )]
    public function post(GlossariesRequest $request): Response
    {
        $entries = $this->glossaryService->toArray(
            $request->entries,
            $request->entries_format === 'csv' ? ',' : "\t",
        );
        $id = $this->glossaryRepository->save(
            new Glossary(
                name: $request->name,
                sourceLanguage: Language::fromAny($request->source_lang),
                targetLanguage: Language::fromAny($request->target_lang),
                entries: $entries,
            ),
        );

        $response = [
            'glossary_id' => $id,
            'ready' => true,
            'name' => $request->name,
            'source_lang' => $request->source_lang,
            'target_lang' => $request->target_lang,
            'creation_time' => new DateTimeImmutable()->format(DateTimeInterface::ATOM),
            'entry_count' => count($entries),
        ];

        return new Created($response);
    }

    #[Get(
        uri: '/deepl/v2/glossaries',
        middleware: [
            LogMiddleware::class,
        ],
    )]
    public function get(): Response
    {
        $glossaries = $this->glossaryRepository->all();
        $response = [];

        foreach ($glossaries as $glossary) {
            $response[] = [
                'glossary_id' => $glossary->id,
                'name' => $glossary->name,
                'ready' => true,
                'source_lang' => $glossary->sourceLanguage->upper(),
                'target_lang' => $glossary->targetLanguage->upper(),
                'creation_time' => new DateTimeImmutable()->format(DateTimeInterface::ATOM),
                'entry_count' => count($glossary->entries),
            ];
        }

        $response = [
            'glossaries' => $response,
        ];

        return new Ok($response);
    }

    #[Get(
        uri: '/deepl/v2/glossaries/{id}',
        middleware: [
            LogMiddleware::class,
        ],
    )]
    public function getById(string $id): Response
    {
        $glossary = $this->glossaryRepository->get($id);

        if ($glossary === null) {
            return new NotFound();
        }

        $response = [
            'glossary_id' => $glossary->id,
            'name' => $glossary->name,
            'ready' => true,
            'source_lang' => $glossary->sourceLanguage->upper(),
            'target_lang' => $glossary->targetLanguage->upper(),
            'creation_time' => new DateTimeImmutable()->format(DateTimeInterface::ATOM),
            'entry_count' => count($glossary->entries),
        ];

        return new Ok($response);
    }

    #[Delete(
        uri: '/deepl/v2/glossaries/{id}',
        middleware: [
            LogMiddleware::class,
        ],
    )]
    public function delete(string $id): Response
    {
        $this->glossaryRepository->remove($id);

        return new GenericResponse(
            status: Status::NO_CONTENT,
        );
    }

    #[Get(
        uri: '/deepl/v2/glossaries/{id}/entries',
        middleware: [
            LogMiddleware::class,
        ],
    )]
    public function entires(string $id): Response
    {
        $glossary = $this->glossaryRepository->get($id);

        if ($glossary === null) {
            return new NotFound();
        }

        $response = [];

        foreach ($glossary->entries as $source => $target) {
            $response[] = $source . "\t" . $target;
        }

        $response = \implode("\n", $response);

        return new GenericResponse(
            status: Status::OK,
            body: $response,
            headers: [
                'Content-Type' => 'text/tab-separated-values',
            ],
        );
    }
}
