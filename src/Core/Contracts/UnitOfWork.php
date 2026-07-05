<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Contracts;

interface UnitOfWork
{
    public function begin(): void;

    public function commit(): void;

    public function rollback(): void;

    public function isActive(): bool;
}
