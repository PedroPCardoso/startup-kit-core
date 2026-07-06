<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\EventBus;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class EventJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly object $event,
    ) {}

    public function handle(QueueEventBus $bus): void
    {
        $bus->dispatch($this->event);
    }
}
