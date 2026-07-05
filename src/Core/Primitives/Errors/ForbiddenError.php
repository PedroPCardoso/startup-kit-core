<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Primitives\Errors;

class ForbiddenError extends DomainError
{
    public static function make(?string $message = null): self
    {
        return new self(
            code: 'forbidden',
            message: $message ?? 'Insufficient permissions.',
            context: [],
        );
    }
}
