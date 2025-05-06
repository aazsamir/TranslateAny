<?php

declare(strict_types=1);

namespace Tests\Mock;

use Stringable;
use Tempest\Log\Logger;

class NullLogger implements Logger
{
    /**
     * @var array{
     *  emergency: array{message: string|Stringable, context: mixed[]},
     *  alert: array{message: string|Stringable, context: mixed[]},
     *  critical: array{message: string|Stringable, context: mixed[]},
     *  error: array{message: string|Stringable, context: mixed[]},
     *  warning: array{message: string|Stringable, context: mixed[]},
     *  notice: array{message: string|Stringable, context: mixed[]},
     *  info: array{message: string|Stringable, context: mixed[]},
     *  debug: array{message: string|Stringable, context: mixed[]},
     * }
     */
    public array $logs = [
        'emergency' => [],
        'alert' => [],
        'critical' => [],
        'error' => [],
        'warning' => [],
        'notice' => [],
        'info' => [],
        'debug' => [],
    ];

    public function emergency(string|Stringable $message, array $context = []): void
    {
        $this->logs['emergency'][] = [
            'message' => $message,
            'context' => $context,
        ];
    }

    public function alert(string|Stringable $message, array $context = []): void
    {
        $this->logs['alert'][] = [
            'message' => $message,
            'context' => $context,
        ];
    }

    public function critical(string|Stringable $message, array $context = []): void
    {
        $this->logs['critical'][] = [
            'message' => $message,
            'context' => $context,
        ];
    }

    public function error(string|Stringable $message, array $context = []): void
    {
        $this->logs['error'][] = [
            'message' => $message,
            'context' => $context,
        ];
    }

    public function warning(string|Stringable $message, array $context = []): void
    {
        $this->logs['warning'][] = [
            'message' => $message,
            'context' => $context,
        ];
    }

    public function notice(string|Stringable $message, array $context = []): void
    {
        $this->logs['notice'][] = [
            'message' => $message,
            'context' => $context,
        ];
    }

    public function info(string|Stringable $message, array $context = []): void
    {
        $this->logs['info'][] = [
            'message' => $message,
            'context' => $context,
        ];
    }

    public function debug(string|Stringable $message, array $context = []): void
    {
        $this->logs['debug'][] = [
            'message' => $message,
            'context' => $context,
        ];
    }

    public function log($level, string|Stringable $message, array $context = []): void
    {
        $this->logs[$level][] = [
            'message' => $message,
            'context' => $context,
        ];
    }

    public function empty(): bool
    {
        foreach ($this->logs as $log) {
            if (count($log) > 0) {
                return false;
            }
        }

        return true;
    }
}
