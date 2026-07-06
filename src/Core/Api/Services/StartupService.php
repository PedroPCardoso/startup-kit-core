<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Api\Services;

use PedroPCardoso\StartupKit\Core\Contracts\ResilientDriverRegistry;

final class StartupService
{
    private bool $bootstrapped = false;

    public function __construct(
        private readonly ResilientDriverRegistry $registry,
    ) {}

    public function status(): array
    {
        $results = $this->registry->healthChecks();
        $checks = [];

        foreach ($results as $name => $result) {
            $checks[] = [
                'driver' => $result->driver(),
                'reachable' => $result->isHealthy(),
            ];
        }

        return [
            'status' => $this->bootstrapped ? 'ready' : 'booting',
            'bootstrapped' => $this->bootstrapped,
            'checks' => $checks,
        ];
    }

    public function markBootstrapped(): void
    {
        $this->bootstrapped = true;
    }
}
