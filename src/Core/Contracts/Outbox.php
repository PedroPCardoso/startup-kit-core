<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Contracts;

interface Outbox
{
    public function add(object $event, ?string $aggregateType = null, ?string $aggregateId = null): void;

    public function publishPending(): int;

    public function cleanExpired(int $hours = 24): int;

    public function retryFailed(int $maxRetries = 3): int;
}
