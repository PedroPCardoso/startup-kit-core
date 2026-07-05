<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Primitives\Result;

/**
 * @template T of mixed
 * @template E of ResultError
 * @extends Result<T, E>
 */
final class Err extends Result
{
    /** @var E */
    private ResultError $error;

    /**
     * @param E $error
     */
    public function __construct(ResultError $error)
    {
        $this->error = $error;
    }

    public function isOk(): bool
    {
        return false;
    }

    public function isErr(): bool
    {
        return true;
    }

    public function map(callable $fn): Result
    {
        return $this;
    }

    public function mapErr(callable $fn): Result
    {
        /** @var self<T, E> */
        return new self($fn($this->error));
    }

    public function flatMap(callable $fn): Result
    {
        return $this;
    }

    public function match(callable $ok, callable $err): mixed
    {
        return $err($this->error);
    }

    public function unwrap(): mixed
    {
        throw new \RuntimeException(
            'Cannot unwrap Err result: ' . $this->error->code() . ' — ' . $this->error->message()
        );
    }

    public function unwrapOr(mixed $default): mixed
    {
        return $default;
    }

    /**
     * @return E
     */
    public function error(): ResultError
    {
        return $this->error;
    }

    public function jsonSerialize(): array
    {
        return [
            'ok' => false,
            'error' => [
                'code' => $this->error->code(),
                'message' => $this->error->message(),
                'context' => $this->error->context(),
            ],
        ];
    }
}
