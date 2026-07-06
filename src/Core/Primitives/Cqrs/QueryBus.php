<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Primitives\Cqrs;

use PedroPCardoso\StartupKit\Core\Primitives\Result\Result;

interface QueryBus
{
    public function ask(Query $query): Result;

    public function register(string $queryClass, string $handlerClass): void;

    public function registerMiddleware(string $middlewareClass): void;
}
