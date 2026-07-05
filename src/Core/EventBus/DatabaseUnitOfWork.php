<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\EventBus;

use Cardoso\StartupKit\Core\Contracts\UnitOfWork;
use Illuminate\Database\ConnectionInterface as DatabaseConnection;

final class DatabaseUnitOfWork implements UnitOfWork
{
    private bool $active = false;

    private int $transactionLevel = 0;

    public function __construct(
        private readonly DatabaseConnection $db,
    ) {}

    public function begin(): void
    {
        $this->db->beginTransaction();
        $this->active = true;
        $this->transactionLevel++;
    }

    public function commit(): void
    {
        if (!$this->active) {
            return;
        }

        $this->db->commit();
        $this->transactionLevel--;

        if ($this->transactionLevel === 0) {
            $this->active = false;
        }
    }

    public function rollback(): void
    {
        if (!$this->active) {
            return;
        }

        $this->db->rollBack();
        $this->transactionLevel = 0;
        $this->active = false;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
