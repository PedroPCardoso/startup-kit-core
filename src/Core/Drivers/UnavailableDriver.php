<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Drivers;

use Cardoso\StartupKit\Core\Contracts\HealthCheckResult;
use Cardoso\StartupKit\Core\Contracts\ResilientDriver;

final class UnavailableDriver implements ResilientDriver
{
    public function __construct(
        private readonly string $name,
        private readonly string $message,
        private readonly array $metadata = [],
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    public function isAvailable(): bool
    {
        return false;
    }

    public function connect(): void
    {
        throw new \RuntimeException($this->message);
    }

    public function disconnect(): void {}

    public function healthCheck(): HealthCheckResult
    {
        return new HealthCheckResult(
            driver: $this->name,
            healthy: false,
            message: $this->message,
            metadata: $this->metadata,
        );
    }

    public function circuitState(): string
    {
        return 'unavailable';
    }
}
