<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Primitives\Cqrs;

use PedroPCardoso\StartupKit\Core\Primitives\Result\Result;

interface CommandBus
{
    public function dispatch(Command $command): Result;

    public function register(string $commandClass, string $handlerClass): void;

    public function registerMiddleware(string $middlewareClass): void;
}
