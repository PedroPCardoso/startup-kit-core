<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Primitives\Cqrs;

use Cardoso\StartupKit\Core\Primitives\Errors\NotFoundError;
use Cardoso\StartupKit\Core\Primitives\Result\Result;
use Illuminate\Contracts\Container\Container;

final class SimpleCommandBus implements CommandBus
{
    /** @var array<string, class-string<CommandHandler>> */
    private array $handlers = [];

    /** @var list<class-string<Middleware>> */
    private array $middleware = [];

    public function __construct(
        private readonly Container $container,
    ) {}

    public function register(string $commandClass, string $handlerClass): void
    {
        $this->handlers[$commandClass] = $handlerClass;
    }

    public function registerMiddleware(string $middlewareClass): void
    {
        $this->middleware[] = $middlewareClass;
    }

    public function dispatch(Command $command): Result
    {
        $handlerClass = $this->handlers[$command::class] ?? null;

        if ($handlerClass === null) {
            return Result::err(
                NotFoundError::make('CommandHandler', $command::class)
            );
        }

        /** @var CommandHandler */
        $handler = $this->container->make($handlerClass);

        $pipeline = $this->buildPipeline(fn() => $handler->handle($command));

        return $pipeline($command);
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
