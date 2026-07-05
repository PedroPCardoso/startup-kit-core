<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Contracts;

interface Span
{
    public function setAttribute(string $key, mixed $value): void;

    public function end(): void;

    public function isRecording(): bool;

    public function spanId(): string;
}
