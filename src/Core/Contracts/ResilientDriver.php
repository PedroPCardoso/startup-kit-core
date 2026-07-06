<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Contracts;

interface ResilientDriver
{
    public function name(): string;

    public function isAvailable(): bool;

    public function connect(): void;

    public function disconnect(): void;

    public function healthCheck(): HealthCheckResult;

    public function circuitState(): string;
}
