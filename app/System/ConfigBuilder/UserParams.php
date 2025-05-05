<?php

declare(strict_types=1);

namespace App\System\ConfigBuilder;

class UserParams
{
    public string $translateEngine;
    public string $translateHost;
    public string $translateModel;
    public ?string $translateApiKey;

    public bool $translateWithCache = false;
    public int $translateCacheTime;

    public string $detectEngine = 'noop';
    public string $detectHost;
    public string $detectModel;
    public ?string $detectApiKey;

    public function isOpenAITranslate(): bool
    {
        return $this->translateEngine === 'openai';
    }

    public function isLibreTranslate(): bool
    {
        return $this->translateEngine === 'libre';
    }

    public function isNoopTranslate(): bool
    {
        return $this->translateEngine === 'noop';
    }

    public function isWithCache(): bool
    {
        return $this->translateWithCache;
    }

    public function isOpenAIDetect(): bool
    {
        return $this->detectEngine === 'openai';
    }

    public function isLibreDetect(): bool
    {
        return $this->detectEngine === 'libre';
    }

    public function isNoopDetect(): bool
    {
        return $this->detectEngine === 'noop';
    }
}
