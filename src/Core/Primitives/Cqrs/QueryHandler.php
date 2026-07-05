<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Primitives\Cqrs;

use Cardoso\StartupKit\Core\Primitives\Result\Result;

interface QueryHandler
{
    public function handle(Query $query): Result;
}
