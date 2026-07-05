<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Tests\Unit\Drivers;

use Cardoso\StartupKit\Core\Contracts\HealthCheckResult;
use Cardoso\StartupKit\Core\Drivers\AbstractResilientDriver;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AbstractResilientDriverTest extends TestCase
{
    #[Test]
    public function health_check_can_move_open_circuit_to_half_open_after_cooldown(): void
    {
        $driver = new ProbeDriver([false, false, true]);

        $this->assertFalse($driver->healthCheck()->isHealthy());
        $this->assertSame('closed', $driver->circuitState());

        $this->assertFalse($driver->healthCheck()->isHealthy());
        $this->assertSame('open', $driver->circuitState());

        $this->assertFalse($driver->healthCheck()->isHealthy());
        $this->assertSame(2, $driver->probeCount());

        $driver->forceCooldownElapsed();

        $this->assertTrue($driver->healthCheck()->isHealthy());
        $this->assertSame('closed', $driver->circuitState());
        $this->assertSame(3, $driver->probeCount());
    }
}

final class ProbeDriver extends AbstractResilientDriver
{
    private int $probeCount = 0;

    /**
     * @param list<bool> $outcomes
     */
    public function __construct(
        private readonly array $outcomes,
    ) {
        $this->failureThreshold = 2;
        $this->cooldownSeconds = 30;
    }

    public function name(): string
    {
        return 'probe';
    }

    public function connect(): void {}

    public function disconnect(): void {}

    public function isAvailable(): bool
    {
        return $this->healthCheck()->isHealthy();
    }

    public function healthCheck(): HealthCheckResult
    {
        if ($this->isCircuitOpen()) {
            return new HealthCheckResult('probe', false, 'Circuit breaker is open.');
        }

        $healthy = $this->outcomes[$this->probeCount] ?? false;
        $this->probeCount++;

        if ($healthy) {
            $this->recordSuccess();
        } else {
            $this->recordFailure();
        }

        return new HealthCheckResult('probe', $healthy);
    }

    public function probeCount(): int
    {
        return $this->probeCount;
    }

    public function forceCooldownElapsed(): void
    {
        $property = new \ReflectionProperty(AbstractResilientDriver::class, 'openedAt');
        $property->setValue($this, new \DateTimeImmutable('-31 seconds'));
    }
}
