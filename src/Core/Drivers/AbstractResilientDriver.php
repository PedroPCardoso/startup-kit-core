<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Drivers;

use Cardoso\StartupKit\Core\Contracts\HealthCheckResult;
use Cardoso\StartupKit\Core\Contracts\ResilientDriver;

abstract class AbstractResilientDriver implements ResilientDriver
{
    protected const STATE_CLOSED = 'closed';
    protected const STATE_OPEN = 'open';
    protected const STATE_HALF_OPEN = 'half_open';

    private string $circuitState = self::STATE_CLOSED;
    private int $failureCount = 0;
    private ?\DateTimeImmutable $openedAt = null;

    protected int $failureThreshold = 5;
    protected int $cooldownSeconds = 30;

    public function circuitState(): string
    {
        return $this->circuitState;
    }

    protected function recordSuccess(): void
    {
        $this->failureCount = 0;

        if ($this->circuitState === self::STATE_HALF_OPEN) {
            $this->circuitState = self::STATE_CLOSED;
        }
    }

    protected function recordFailure(): void
    {
        $this->failureCount++;

        if ($this->failureCount >= $this->failureThreshold) {
            $this->circuitState = self::STATE_OPEN;
            $this->openedAt = new \DateTimeImmutable();
        }
    }

    protected function isCircuitOpen(): bool
    {
        if ($this->circuitState === self::STATE_CLOSED) {
            return false;
        }

        if ($this->circuitState === self::STATE_OPEN && $this->openedAt !== null) {
            $elapsed = (new \DateTimeImmutable())->getTimestamp() - $this->openedAt->getTimestamp();

            if ($elapsed >= $this->cooldownSeconds) {
                $this->circuitState = self::STATE_HALF_OPEN;
                return false;
            }

            return true;
        }

        return false;
    }

    public abstract function name(): string;

    public abstract function connect(): void;

    public abstract function disconnect(): void;

    public abstract function isAvailable(): bool;

    public abstract function healthCheck(): HealthCheckResult;
}
