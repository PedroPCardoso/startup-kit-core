<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Primitives\Errors;

class TimeoutError extends DomainError
{
    public static function make(string $operation, ?string $message = null): self
    {
        return new self(
            code: 'timeout',
            message: $message ?? sprintf('Operation "%s" timed out.', $operation),
            context: ['operation' => $operation],
        );
    }
}
