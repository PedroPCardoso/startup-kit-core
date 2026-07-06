<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Primitives\Errors;

class UnauthorizedError extends DomainError
{
    public static function make(?string $message = null): self
    {
        return new self(
            code: 'unauthorized',
            message: $message ?? 'Authentication required.',
            context: [],
        );
    }
}
