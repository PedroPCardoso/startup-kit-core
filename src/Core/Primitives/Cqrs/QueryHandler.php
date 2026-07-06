<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Primitives\Cqrs;

use PedroPCardoso\StartupKit\Core\Primitives\Result\Result;

interface QueryHandler
{
    public function handle(Query $query): Result;
}
