<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Logging;

use Cardoso\StartupKit\Core\Contracts\Logger;
use Cardoso\StartupKit\Core\Contracts\Tracer;
use Psr\Log\LoggerInterface as PsrLogger;

final class MultiChannelLogger implements Logger
{
    public function __construct(
        private readonly PsrLogger $logger,
        private readonly Tracer $tracer,
    ) {}

    public function emergency(string $message, array $context = []): void
    {
        $this->log('emergency', $message, $context);
    }

    public function alert(string $message, array $context = []): void
    {
        $this->log('alert', $message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->log('critical', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }

    public function notice(string $message, array $context = []): void
    {
        $this->log('notice', $message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->log('debug', $message, $context);
    }

    public function log(string $level, string $message, array $context = []): void
    {
        $span = $this->tracer->currentSpan();

        $enriched = array_merge($context, [
            'trace_id' => $span->spanId(),
            'timestamp' => now()->toIso8601String(),
        ]);

        $this->logger->log($level, $message, $enriched);
    }
}
