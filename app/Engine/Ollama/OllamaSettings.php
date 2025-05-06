<?php

declare(strict_types=1);

namespace App\Engine\Ollama;

readonly class OllamaSettings
{
    /**
     * @param string[] $stop
     */
    public function __construct(
        public ?int $numKeep = null,
        public ?int $seed = null,
        public ?int $numPredict = null,
        public ?int $topK = null,
        public ?float $topP = null,
        public ?float $minP = null,
        public ?float $typicalP = null,
        public ?int $repeatLastN = null,
        public ?float $temperature = null,
        public ?float $repeatPenalty = null,
        public ?float $presencePenalty = null,
        public ?float $frequencyPenalty = null,
        public ?int $mirostat = null,
        public ?float $mirostatTau = null,
        public ?float $mirostatEta = null,
        public ?bool $penalizeNewline = null,
        public ?array $stop = null,
        public ?bool $numa = null,
        public ?int $numCtx = null,
        public ?int $numBatch = null,
        public ?int $numGPU = null,
        public ?int $mainGPU = null,
        public ?bool $lowVram = null,
        public ?bool $vocabOnly = null,
        public ?bool $useMmap = null,
        public ?bool $useMlock = null,
        public ?int $numThread = null,
    ) {
    }

    /**
     * @param string[] $stop
     */
    public static function new(
        ?int $numKeep = null,
        ?int $seed = null,
        ?int $numPredict = null,
        ?int $topK = null,
        ?float $topP = null,
        ?float $minP = null,
        ?float $typicalP = null,
        ?int $repeatLastN = null,
        ?float $temperature = null,
        ?float $repeatPenalty = null,
        ?float $presencePenalty = null,
        ?float $frequencyPenalty = null,
        ?int $mirostat = null,
        ?float $mirostatTau = null,
        ?float $mirostatEta = null,
        ?bool $penalizeNewline = null,
        ?array $stop = null,
        ?bool $numa = null,
        ?int $numCtx = null,
        ?int $numBatch = null,
        ?int $numGPU = null,
        ?int $mainGPU = null,
        ?bool $lowVram = null,
        ?bool $vocabOnly = null,
        ?bool $useMmap = null,
        ?bool $useMlock = null,
        ?int $numThread = null,
    ): self {
        return new self(
            numKeep: $numKeep,
            seed: $seed,
            numPredict: $numPredict,
            topK: $topK,
            topP: $topP,
            minP: $minP,
            typicalP: $typicalP,
            repeatLastN: $repeatLastN,
            temperature: $temperature,
            repeatPenalty: $repeatPenalty,
            presencePenalty: $presencePenalty,
            frequencyPenalty: $frequencyPenalty,
            mirostat: $mirostat,
            mirostatTau: $mirostatTau,
            mirostatEta: $mirostatEta,
            penalizeNewline: $penalizeNewline,
            stop: $stop,
            numa: $numa,
            numCtx: $numCtx,
            numBatch: $numBatch,
            numGPU: $numGPU,
            mainGPU: $mainGPU,
            lowVram: $lowVram,
            vocabOnly: $vocabOnly,
            useMmap: $useMmap,
            useMlock: $useMlock,
            numThread: $numThread,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter(
            [
                'num_keep' => $this->numKeep,
                'seed' => $this->seed,
                'num_predict' => $this->numPredict,
                'top_k' => $this->topK,
                'top_p' => $this->topP,
                'min_p' => $this->minP,
                'typical_p' => $this->typicalP,
                'repeat_last_n' => $this->repeatLastN,
                'temperature' => $this->temperature,
                'repeat_penalty' => $this->repeatPenalty,
                'presence_penalty' => $this->presencePenalty,
                'frequency_penalty' => $this->frequencyPenalty,
                'mirostat' => $this->mirostat,
                'mirostat_tau' => $this->mirostatTau,
                'mirostat_eta' => $this->mirostatEta,
                'penalize_newline' => $this->penalizeNewline,
                'stop' => $this->stop,
                'numa' => $this->numa,
                'num_ctx' => $this->numCtx,
                'num_batch' => $this->numBatch,
                'num_gpu' => $this->numGPU,
                'main_gpu' => $this->mainGPU,
                'low_vram' => $this->lowVram,
                'vocab_only' => $this->vocabOnly,
                'use_mmap' => $this->useMmap,
                'use_mlock' => $this->useMlock,
                'num_thread' => $this->numThread,
            ],
            static fn ($value) => $value !== null && $value !== [],
        );
    }
}
