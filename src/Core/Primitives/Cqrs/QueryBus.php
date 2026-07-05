<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Primitives\Cqrs;

use Cardoso\StartupKit\Core\Primitives\Result\Result;

interface QueryBus
{
    public function ask(Query $query): Result;

    public function register(string $queryClass, string $handlerClass): void;

    public function registerMiddleware(string $middlewareClass): void;
}
