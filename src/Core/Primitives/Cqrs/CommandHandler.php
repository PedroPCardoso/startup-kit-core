<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Primitives\Cqrs;

use PedroPCardoso\StartupKit\Core\Primitives\Result\Result;

interface CommandHandler
{
    public function handle(Command $command): Result;
}
