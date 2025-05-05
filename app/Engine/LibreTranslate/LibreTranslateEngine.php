<?php

declare(strict_types=1);

namespace App\Engine\LibreTranslate;

use App\Engine\AvailableLanguage;
use App\Engine\DetectEngine;
use App\Engine\Detection;
use App\Engine\TranslateEngine;
use App\Engine\TranslatePayload;
use App\Engine\Translation;
use App\System\Language;
use App\System\Logger\PrefixLogger;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;
use ReflectionClass;
use Tempest\Log\Logger;

use function Tempest\get;

readonly class LibreTranslateEngine implements TranslateEngine, DetectEngine
{
    use PrefixLogger;

    public function __construct(
        private string $host,
        private ClientInterface $client,
        private Logger $logger,
        // @phpstan-ignore-next-line
        private ?string $apiKey = null,
    ) {
    }

    public static function new(
        string $host = 'https://libretranslate.com',
        ?string $apiKey = null,
    ): self {
        $lazy = new ReflectionClass(self::class);
        $lazy = $lazy->newLazyGhost(function (LibreTranslateEngine $object) use ($host, $apiKey) {
            $object->__construct(
                host: $host,
                client: new \GuzzleHttp\Client(),
                apiKey: $apiKey,
                logger: get(Logger::class),
            );
        });

        return $lazy;
    }

    public function detect(string $text): array
    {
        $request = new Request(
            method: 'POST',
            uri: $this->host . '/detect',
            headers: [
                'Content-Type' => 'application/json',
            ],
            body: json_encode(
                [
                    'q' => $text,
                ],
                flags: JSON_THROW_ON_ERROR,
            ),
        );
        $response = $this->client->sendRequest($request);
        /**
         * @var array{
         *  language: string,
         *  confidence: float,
         * }[]
         */
        $response = json_decode($response->getBody()->getContents(), true);

        if (isset($response['error'])) {
            // @phpstan-ignore-next-line
            throw new \RuntimeException('Error: ' . $response['error']);
        }

        $detections = [];

        foreach ($response as $detection) {
            $detections[] = new Detection(
                language: Language::fromAny($detection['language']),
                confidence: (float) $detection['confidence'],
            );
        }

        return $detections;
    }

    public function translate(TranslatePayload $payload): Translation
    {
        $request = [
            'q' => $payload->text,
            'target' => $payload->targetLanguage->lower(),
        ];
        if ($payload->sourceLanguage) {
            $request['source'] = $payload->sourceLanguage->lower();
        } else {
            $request['source'] = 'auto';
        }
        if ($payload->format) {
            $request['format'] = $payload->format;
        }
        if ($payload->alternatives) {
            $request['alternatives'] = $payload->alternatives;
        }

        $this->logger->debug(
            $this->prefixLog(
                'LibreTranslate',
                'translate',
            ),
            $request,
        );

        $request = new Request(
            method: 'POST',
            uri: $this->host . '/translate',
            headers: [
                'Content-Type' => 'application/json',
            ],
            body: json_encode($request, flags: JSON_THROW_ON_ERROR),
        );
        $response = $this->client->sendRequest($request);
        /**
         * @var array{
         *  translatedText: string,
         *  alternatives?: array<string>,
         *  detectedLanguage?: array{
         *      language: string,
         *      confidence: float,
         *  },
         *  error?: string,
         * }
         */
        $response = json_decode($response->getBody()->getContents(), true);

        $this->logger->debug(
            $this->prefixLog(
                'LibreTranslate',
                'translated',
            ),
            $response,
        );

        if (isset($response['error'])) {
            throw new \RuntimeException('Error: ' . $response['error']);
        }

        return new Translation(
            text: $response['translatedText'],
            alternatives: $response['alternatives'] ?? [],
            detectedLanguage: isset($response['detectedLanguage']) ? new Detection(
                    language: Language::fromAny($response['detectedLanguage']['language']),
                    confidence: (float) $response['detectedLanguage']['confidence'],
                ) : null,
        );
    }

    public function languages(): array
    {
        $request = new Request(
            method: 'GET',
            uri: $this->host . '/languages',
            headers: [
                'Content-Type' => 'application/json',
            ],
        );
        $response = $this->client->sendRequest($request);
        /**
         * @var array{
         *  code: string,
         *  name: string,
         *  targets: array<string>,
         * }[]
         */
        $response = json_decode($response->getBody()->getContents(), true);

        if (isset($response['error'])) {
            // @phpstan-ignore-next-line
            throw new \RuntimeException('Error: ' . $response['error']);
        }

        $languages = [];

        foreach ($response as $language) {
            $languages[] = new AvailableLanguage(
                language: Language::fromAny($language['code']),
                targets: Language::fromAnyArray($language['targets']),
            );
        }

        return $languages;
    }
}
