<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Primitives\Errors;

abstract class DomainError implements Error
{
    public function __construct(
        private readonly string $code,
        private readonly string $message,
        private readonly array $context = [],
    ) {}

    final public function code(): string
    {
        return $this->code;
    }

    final public function message(): string
    {
        return $this->message;
    }

    final public function context(): array
    {
        return $this->context;
    }

    public function jsonSerialize(): array
    {
        return [
            'code' => $this->code,
            'message' => $this->message,
            'context' => $this->context,
        ];
    }
}
