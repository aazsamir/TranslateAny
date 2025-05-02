<?php

declare(strict_types=1);

namespace App\Engine\LibreTranslate;

use App\Engine\AvailableLanguage;
use App\Engine\Detection;
use App\Engine\DetectionEngine;
use App\Engine\TranslateEngine;
use App\Engine\TranslatePayload;
use App\Engine\Translation;
use App\System\Language;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;

readonly class LibreTranslateEngine implements TranslateEngine, DetectionEngine
{
    public function __construct(
        private string $url,
        private ClientInterface $client,
    ) {
    }

    public function detect(string $text): array
    {
        $request = new Request(
            method: 'POST',
            uri: $this->url . '/detect',
            headers: [
                'Content-Type' => 'application/json',
            ],
            body: json_encode([
                'q' => $text,
            ]),
        );
        $response = $this->client->sendRequest($request);
        $response = json_decode($response->getBody()->getContents(), true);

        if (isset($response['error'])) {
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

        $request = new Request(
            method: 'POST',
            uri: $this->url . '/translate',
            headers: [
                'Content-Type' => 'application/json',
            ],
            body: json_encode($request),
        );
        $response = $this->client->sendRequest($request);
        $response = json_decode($response->getBody()->getContents(), true);

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
            uri: $this->url . '/languages',
            headers: [
                'Content-Type' => 'application/json',
            ],
        );
        $response = $this->client->sendRequest($request);
        $response = json_decode($response->getBody()->getContents(), true);

        if (isset($response['error'])) {
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
