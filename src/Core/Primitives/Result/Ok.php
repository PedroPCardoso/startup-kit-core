<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Primitives\Result;

/**
 * @template T of mixed
 * @template E of ResultError
 * @extends Result<T, E>
 */
final class Ok extends Result
{
    /** @var T */
    private mixed $value;

    /**
     * @param T $value
     */
    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    public function isOk(): bool
    {
        return true;
    }

    public function isErr(): bool
    {
        return false;
    }

    public function map(callable $fn): Result
    {
        /** @var self<T, E> */
        return new self($fn($this->value));
    }

    public function mapErr(callable $fn): Result
    {
        return $this;
    }

    public function flatMap(callable $fn): Result
    {
        return $fn($this->value);
    }

    public function match(callable $ok, callable $err): mixed
    {
        return $ok($this->value);
    }

    public function unwrap(): mixed
    {
        return $this->value;
    }

    public function unwrapOr(mixed $default): mixed
    {
        return $this->value;
    }

    public function jsonSerialize(): array
    {
        return [
            'ok' => true,
            'value' => $this->value,
        ];
    }
}
