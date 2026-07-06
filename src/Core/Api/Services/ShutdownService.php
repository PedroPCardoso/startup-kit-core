<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Api\Services;

use PedroPCardoso\StartupKit\Core\Contracts\ResilientDriverRegistry;

final class ShutdownService
{
    public function __construct(
        private readonly ResilientDriverRegistry $registry,
    ) {}

    public function shutdown(): array
    {
        foreach ($this->registry->all() as $driver) {
            try {
                $driver->disconnect();
            } catch (\Throwable) {
            }
        }

        return [
            'status' => 'shutting_down',
            'message' => 'Graceful shutdown initiated.',
        ];
    }
}
