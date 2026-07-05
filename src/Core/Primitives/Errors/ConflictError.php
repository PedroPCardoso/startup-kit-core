<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Primitives\Errors;

class ConflictError extends DomainError
{
    public static function make(string $entity, string $reason, ?string $message = null): self
    {
        return new self(
            code: 'conflict',
            message: $message ?? sprintf('Conflict on %s: %s', $entity, $reason),
            context: ['entity' => $entity, 'reason' => $reason],
        );
    }
}
