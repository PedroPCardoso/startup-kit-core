<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Primitives\Cqrs;

use Cardoso\StartupKit\Core\Primitives\Result\Result;

interface Middleware
{
    /**
     * @param callable(): Result $next
     */
    public function handle(Command|Query $message, callable $next): Result;
}
