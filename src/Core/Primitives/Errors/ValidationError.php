<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Primitives\Errors;

class ValidationError extends DomainError
{
    public static function make(string $field, string $reason, ?string $message = null): self
    {
        return new self(
            code: 'validation.' . $field,
            message: $message ?? sprintf('Validation failed for field "%s": %s', $field, $reason),
            context: ['field' => $field, 'reason' => $reason],
        );
    }

    public static function general(string $message, array $context = []): self
    {
        return new self(
            code: 'validation.error',
            message: $message,
            context: $context,
        );
    }
}
