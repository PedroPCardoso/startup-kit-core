<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Drivers\Redis;

use Cardoso\StartupKit\Core\Contracts\HealthCheckResult;
use Cardoso\StartupKit\Core\Drivers\AbstractResilientDriver;
use Illuminate\Contracts\Redis\Factory as RedisFactory;

final class RedisDriver extends AbstractResilientDriver
{
    private bool $connected = false;

    public function __construct(
        private readonly RedisFactory $redis,
        private readonly ?string $connection = null,
    ) {}

    public function name(): string
    {
        return 'redis';
    }

    public function connect(): void
    {
        $this->redis->connection($this->connection)->ping();
        $this->connected = true;
    }

    public function disconnect(): void
    {
        $this->redis->connection($this->connection)->disconnect();
        $this->connected = false;
    }

    public function isAvailable(): bool
    {
        try {
            $this->connect();
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    public function healthCheck(): HealthCheckResult
    {
        try {
            $start = microtime(true);
            $this->redis->connection($this->connection)->ping();
            $latency = (microtime(true) - $start) * 1000;

            $this->recordSuccess();

            return new HealthCheckResult(
                driver: 'redis',
                healthy: true,
                message: null,
                metadata: ['latency_ms' => round($latency, 2)],
            );
        } catch (\Throwable $e) {
            $this->recordFailure();

            return new HealthCheckResult(
                driver: 'redis',
                healthy: false,
                message: $e->getMessage(),
            );
        }
    }
}
