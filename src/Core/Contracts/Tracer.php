<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Contracts;

interface Tracer
{
    public function startSpan(string $name, array $attributes = []): Span;

    public function currentSpan(): ?Span;
}
