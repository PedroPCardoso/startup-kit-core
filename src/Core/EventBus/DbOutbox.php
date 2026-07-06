<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\EventBus;

use PedroPCardoso\StartupKit\Core\Contracts\Outbox;
use Illuminate\Database\ConnectionInterface as DatabaseConnection;
use Illuminate\Contracts\Queue\Queue;
use Carbon\CarbonImmutable;

final class DbOutbox implements Outbox
{
    private const TABLE = 'startup_kit_outbox';

    public function __construct(
        private readonly DatabaseConnection $db,
        private readonly Queue $queue,
        private readonly string $table = self::TABLE,
    ) {}

    public function add(object $event, ?string $aggregateType = null, ?string $aggregateId = null): void
    {
        $this->db->table($this->table)->insert([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'aggregate_type' => $aggregateType,
            'aggregate_id' => $aggregateId,
            'event_type' => $event::class,
            'payload' => serialize($event),
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function publishPending(): int
    {
        $rows = $this->db->table($this->table)
            ->where('status', 'pending')
            ->where('attempts', '<', 3)
            ->orderBy('created_at')
            ->limit(100)
            ->get();

        $count = 0;

        foreach ($rows as $row) {
            try {
                $event = unserialize($row->payload);
                $this->queue->push(new EventJob($event));

                $this->db->table($this->table)
                    ->where('id', $row->id)
                    ->update([
                        'status' => 'published',
                        'published_at' => now(),
                        'updated_at' => now(),
                    ]);

                $count++;
            } catch (\Throwable) {
                $this->db->table($this->table)
                    ->where('id', $row->id)
                    ->increment('attempts');

                $this->db->table($this->table)
                    ->where('id', $row->id)
                    ->update([
                        'status' => 'failed',
                        'updated_at' => now(),
                        'last_error' => 'Failed to publish',
                    ]);
            }
        }

        return $count;
    }

    public function cleanExpired(int $hours = 24): int
    {
        return $this->db->table($this->table)
            ->where('created_at', '<', now()->subHours($hours))
            ->whereIn('status', ['published', 'failed'])
            ->delete();
    }

    public function retryFailed(int $maxRetries = 3): int
    {
        $rows = $this->db->table($this->table)
            ->where('status', 'failed')
            ->where('attempts', '<', $maxRetries)
            ->get();

        $count = 0;

        foreach ($rows as $row) {
            $this->db->table($this->table)
                ->where('id', $row->id)
                ->update([
                    'status' => 'pending',
                    'updated_at' => now(),
                ]);
            $count++;
        }

        return $count;
    }
}
