<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Drivers;

use Cardoso\StartupKit\Core\Contracts\HealthCheckResult;
use Cardoso\StartupKit\Core\Contracts\ResilientDriver;
use Cardoso\StartupKit\Core\Contracts\ResilientDriverRegistry;
use RuntimeException;

final class DriverRegistry implements ResilientDriverRegistry
{
    /** @var array<string, ResilientDriver> */
    private array $drivers = [];

    public function register(string $name, ResilientDriver $driver): void
    {
        $this->drivers[$name] = $driver;
    }

    public function get(string $name): ResilientDriver
    {
        if (!isset($this->drivers[$name])) {
            throw new RuntimeException(sprintf('Driver "%s" is not registered.', $name));
        }

        return $this->drivers[$name];
    }

    public function has(string $name): bool
    {
        return isset($this->drivers[$name]);
    }

    public function all(): array
    {
        return $this->drivers;
    }

    public function healthChecks(): array
    {
        $results = [];

        foreach ($this->drivers as $name => $driver) {
            try {
                $results[$name] = $driver->healthCheck();
            } catch (\Throwable $e) {
                $results[$name] = new HealthCheckResult(
                    driver: $name,
                    healthy: false,
                    message: $e->getMessage(),
                );
            }
        }

        return $results;
    }
}
