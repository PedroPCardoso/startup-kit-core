<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Drivers\Database;

use Cardoso\StartupKit\Core\Contracts\HealthCheckResult;
use Cardoso\StartupKit\Core\Drivers\AbstractResilientDriver;
use Illuminate\Database\DatabaseManager;

final class DatabaseDriver extends AbstractResilientDriver
{
    public function __construct(
        private readonly string $name,
        private readonly DatabaseManager $db,
        private readonly ?string $connection = null,
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    public function connect(): void
    {
        $this->db->connection($this->connection)->select('select 1');
    }

    public function disconnect(): void
    {
        $this->db->disconnect($this->connection);
    }

    public function isAvailable(): bool
    {
        return $this->healthCheck()->isHealthy();
    }

    public function healthCheck(): HealthCheckResult
    {
        if ($this->isCircuitOpen()) {
            return new HealthCheckResult(
                driver: $this->name,
                healthy: false,
                message: 'Circuit breaker is open.',
                metadata: [
                    'adapter' => 'database',
                    'connection' => $this->connection,
                    'circuit_state' => $this->circuitState(),
                ],
            );
        }

        try {
            $start = microtime(true);
            $this->db->connection($this->connection)->select('select 1');
            $latency = (microtime(true) - $start) * 1000;

            $this->recordSuccess();

            return new HealthCheckResult(
                driver: $this->name,
                healthy: true,
                message: null,
                metadata: [
                    'adapter' => 'database',
                    'connection' => $this->connection,
                    'circuit_state' => $this->circuitState(),
                    'latency_ms' => round($latency, 2),
                ],
            );
        } catch (\Throwable $e) {
            $this->recordFailure();

            return new HealthCheckResult(
                driver: $this->name,
                healthy: false,
                message: $e->getMessage(),
                metadata: [
                    'adapter' => 'database',
                    'connection' => $this->connection,
                    'circuit_state' => $this->circuitState(),
                ],
            );
        }
    }
}
