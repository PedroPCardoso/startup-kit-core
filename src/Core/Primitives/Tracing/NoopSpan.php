<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Primitives\Tracing;

use Cardoso\StartupKit\Core\Contracts\Span;

final class NoopSpan implements Span
{
    public function setAttribute(string $key, mixed $value): void {}

    public function end(): void {}

    public function isRecording(): bool
    {
        return false;
    }

    public function spanId(): string
    {
        return '0000000000000000';
    }
}
