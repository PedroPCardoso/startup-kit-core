<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Primitives\Tracing;

use Cardoso\StartupKit\Core\Contracts\Span;
use Cardoso\StartupKit\Core\Contracts\Tracer;

final class NoopTracer implements Tracer
{
    private static ?NoopSpan $noopSpan = null;

    public function startSpan(string $name, array $attributes = []): Span
    {
        self::$noopSpan ??= new NoopSpan();
        return self::$noopSpan;
    }

    public function currentSpan(): ?Span
    {
        return self::$noopSpan ??= new NoopSpan();
    }
}
