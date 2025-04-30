<?php

declare(strict_types=1);

namespace App\Engine\LibreTranslate;

use App\Engine\AvailableLanguage;
use App\Engine\Detection;
use App\Engine\DetectionEngine;
use App\Engine\TranslateEngine;
use App\Engine\Translation;
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
                language: $detection['language'],
                confidence: (float) $detection['confidence'],
            );
        }

        return $detections;
    }

    public function translate(string $text, string $targetLanguage, ?string $sourceLanguage = null, ?string $format = null, ?int $alternatives = null): Translation
    {
        $request = [
            'q' => $text,
            'target' => $targetLanguage,
        ];
        if ($sourceLanguage) {
            $request['source'] = $sourceLanguage;
        } else {
            $request['source'] = 'auto';
        }
        if ($format) {
            $request['format'] = $format;
        }
        if ($alternatives) {
            $request['alternatives'] = $alternatives;
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
                    language: $response['detectedLanguage']['language'],
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
                language: $language['code'],
                targets: $language['targets'],
            );
        }

        return $languages;
    }
}
