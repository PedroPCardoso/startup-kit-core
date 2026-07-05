<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Primitives\Cqrs\Middleware;

use Cardoso\StartupKit\Core\Contracts\UnitOfWork;
use Cardoso\StartupKit\Core\Primitives\Cqrs\Command;
use Cardoso\StartupKit\Core\Primitives\Cqrs\Middleware;
use Cardoso\StartupKit\Core\Primitives\Cqrs\Query;
use Cardoso\StartupKit\Core\Primitives\Result\Result;

final class TransactionalMiddleware implements Middleware
{
    public function __construct(
        private readonly UnitOfWork $unitOfWork,
    ) {}

    public function handle(Command|Query $message, callable $next): Result
    {
        if ($message instanceof Query) {
            return $next($message);
        }

        $this->unitOfWork->begin();

        try {
            $result = $next($message);

            if ($result->isOk()) {
                $this->unitOfWork->commit();
            } else {
                $this->unitOfWork->rollback();
            }

            return $result;
        } catch (\Throwable $e) {
            $this->unitOfWork->rollback();
            throw $e;
        }
    }
}
