<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Api\Services;

use PedroPCardoso\StartupKit\Core\Contracts\ResilientDriverRegistry;

final class HealthService
{
    public function __construct(
        private readonly ResilientDriverRegistry $registry,
    ) {}

    public function check(): array
    {
        $results = $this->registry->healthChecks();
        $allHealthy = true;

        $checks = [];

        foreach ($results as $name => $result) {
            $checks[] = $result->toArray();

            if (!$result->isHealthy()) {
                $allHealthy = false;
            }
        }

        return [
            'status' => $allHealthy ? 'ok' : 'degraded',
            'checks' => $checks,
        ];
    }
}
