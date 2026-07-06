<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Primitives\Cqrs;

use PedroPCardoso\StartupKit\Core\Primitives\Errors\NotFoundError;
use PedroPCardoso\StartupKit\Core\Primitives\Result\Result;
use Illuminate\Contracts\Container\Container;

final class SimpleQueryBus implements QueryBus
{
    /** @var array<string, class-string<QueryHandler>> */
    private array $handlers = [];

    /** @var list<class-string<Middleware>> */
    private array $middleware = [];

    public function __construct(
        private readonly Container $container,
    ) {}

    public function register(string $queryClass, string $handlerClass): void
    {
        $this->handlers[$queryClass] = $handlerClass;
    }

    public function registerMiddleware(string $middlewareClass): void
    {
        $this->middleware[] = $middlewareClass;
    }

    public function ask(Query $query): Result
    {
        $handlerClass = $this->handlers[$query::class] ?? null;

        if ($handlerClass === null) {
            return Result::err(
                NotFoundError::make('QueryHandler', $query::class)
            );
        }

        /** @var QueryHandler */
        $handler = $this->container->make($handlerClass);

        $pipeline = $this->buildPipeline(fn() => $handler->handle($query));

        return $pipeline($query);
    }

    private function buildPipeline(callable $core): callable
    {
        $pipeline = $core;

        foreach (array_reverse($this->middleware) as $middlewareClass) {
            $pipeline = (function (callable $next) use ($middlewareClass, $pipeline): callable {
                return function (Command|Query $message) use ($middlewareClass, $next, $pipeline): Result {
                    /** @var Middleware $instance */
                    $instance = $this->container->make($middlewareClass);
                    return $instance->handle($message, fn() => $pipeline($message));
                };
            })($pipeline);
        }

        return $pipeline;
    }
}
