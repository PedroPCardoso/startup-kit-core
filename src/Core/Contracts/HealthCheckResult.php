<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Contracts;

final class HealthCheckResult
{
    public function __construct(
        private readonly string $driver,
        private readonly bool $healthy,
        private readonly ?string $message = null,
        private readonly array $metadata = [],
    ) {}

    public function driver(): string
    {
        return $this->driver;
    }

    public function isHealthy(): bool
    {
        return $this->healthy;
    }

    public function message(): ?string
    {
        return $this->message;
    }

    public function metadata(): array
    {
        return $this->metadata;
    }

    public function toArray(): array
    {
        return [
            'driver' => $this->driver,
            'healthy' => $this->healthy,
            'message' => $this->message,
            'metadata' => $this->metadata,
        ];
    }
}
