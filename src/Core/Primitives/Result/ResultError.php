<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Primitives\Result;

interface ResultError extends \JsonSerializable
{
    public function code(): string;

    public function message(): string;

    public function context(): array;
}
