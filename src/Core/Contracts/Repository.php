<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Contracts;

use Cardoso\StartupKit\Core\Primitives\Result\Result;

/**
 * @template T of object
 */
interface Repository
{
    /**
     * @param T $entity
     * @return Result<T>
     */
    public function save(object $entity): Result;

    /**
     * @return Result<T>
     */
    public function byId(string|int $id): Result;

    /**
     * @return Result<T>
     */
    public function delete(object $entity): Result;
}
