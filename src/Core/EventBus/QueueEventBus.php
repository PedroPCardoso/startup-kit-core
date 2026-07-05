<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\EventBus;

use Cardoso\StartupKit\Core\Contracts\EventBus;
use Illuminate\Contracts\Queue\Queue;

final class QueueEventBus implements EventBus
{
    /** @var array<class-string, list<callable>> */
    private array $handlers = [];

    public function __construct(
        private readonly Queue $queue,
    ) {}

    public function publish(object $event): void
    {
        $this->queue->push(new EventJob($event));
    }

    public function subscribe(string $eventClass, callable $handler): void
    {
        $this->handlers[$eventClass][] = $handler;
    }

    public function dispatch(object $event): void
    {
        $handlers = $this->handlers[$event::class] ?? [];

        foreach ($handlers as $handler) {
            $handler($event);
        }
    }

    /**
     * @return array<class-string, list<callable>>
     */
    public function handlers(): array
    {
        return $this->handlers;
    }
}
