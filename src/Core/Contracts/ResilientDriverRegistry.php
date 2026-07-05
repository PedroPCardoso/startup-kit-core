<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Contracts;

interface ResilientDriverRegistry
{
    public function register(string $name, ResilientDriver $driver): void;

    public function get(string $name): ResilientDriver;

    public function has(string $name): bool;

    /**
     * @return array<string, ResilientDriver>
     */
    public function all(): array;

    /**
     * @return array<string, HealthCheckResult>
     */
    public function healthChecks(): array;
}
