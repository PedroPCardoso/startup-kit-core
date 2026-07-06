<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Primitives\Cqrs;

use PedroPCardoso\StartupKit\Core\Primitives\Result\Result;

interface Middleware
{
    /**
     * @param callable(): Result $next
     */
    public function handle(Command|Query $message, callable $next): Result;
}
