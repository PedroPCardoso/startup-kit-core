<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Contracts;

interface EventBus
{
    public function publish(object $event): void;

    /**
     * @param class-string $eventClass
     * @param callable $handler
     */
    public function subscribe(string $eventClass, callable $handler): void;
}
