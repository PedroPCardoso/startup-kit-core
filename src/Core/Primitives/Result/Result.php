<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Primitives\Result;

/**
 * @template T of mixed
 * @template E of ResultError
 *
 * @implements \IteratorAggregate<T>
 */
abstract class Result implements \JsonSerializable
{
    /**
     * @template TOk of mixed
     * @template TErr of ResultError
     * @param TOk $value
     * @return self<TOk, TErr>
     */
    final public static function ok(mixed $value): self
    {
        /** @var self<TOk, TErr> */
        return new Ok($value);
    }

    /**
     * @template TOk of mixed
     * @template TErr of ResultError
     * @param TErr $error
     * @return self<TOk, TErr>
     */
    final public static function err(ResultError $error): self
    {
        /** @var self<TOk, TErr> */
        return new Err($error);
    }

    abstract public function isOk(): bool;

    abstract public function isErr(): bool;

    /**
     * @template U
     * @param callable(T): U $fn
     * @return self<U, E>
     */
    abstract public function map(callable $fn): self;

    /**
     * @template U
     * @param callable(E): U $fn
     * @return self<T, U>
     */
    abstract public function mapErr(callable $fn): self;

    /**
     * @template U of mixed
     * @template F of ResultError
     * @param callable(T): self<U, F> $fn
     * @return self<U, F|E>
     */
    abstract public function flatMap(callable $fn): self;

    /**
     * @template U
     * @param callable(T): U $ok
     * @param callable(E): U $err
     * @return U
     */
    abstract public function match(callable $ok, callable $err): mixed;

    /**
     * @return T
     * @throws \RuntimeException if isErr()
     */
    abstract public function unwrap(): mixed;

    /**
     * @template U
     * @param U $default
     * @return T|U
     */
    abstract public function unwrapOr(mixed $default): mixed;

    /**
     * @return array{ok: bool, value: mixed}|array{ok: bool, error: array}
     */
    abstract public function jsonSerialize(): array;

    public function getIterator(): \Traversable
    {
        if ($this->isOk()) {
            yield $this->unwrap();
        }
    }
}
