<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Primitives\Errors;

class NotFoundError extends DomainError
{
    public static function make(string $entity, string|int $id, ?string $message = null): self
    {
        return new self(
            code: 'not_found',
            message: $message ?? sprintf('%s [%s] not found.', $entity, (string) $id),
            context: ['entity' => $entity, 'id' => $id],
        );
    }
}
