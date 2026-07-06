<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Tests\Unit\Primitives\Cqrs;

use PedroPCardoso\StartupKit\Core\Primitives\Cqrs\Command;
use PedroPCardoso\StartupKit\Core\Primitives\Cqrs\CommandBus;
use PedroPCardoso\StartupKit\Core\Primitives\Cqrs\CommandHandler;
use PedroPCardoso\StartupKit\Core\Primitives\Cqrs\SimpleCommandBus;
use PedroPCardoso\StartupKit\Core\Primitives\Result\Result;
use Illuminate\Container\Container;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SimpleCommandBusTest extends TestCase
{
    #[Test]
    public function dispatch_resolves_handler_and_returns_result(): void
    {
        $container = new Container();
        $bus = new SimpleCommandBus($container);

        $bus->register(TestCommand::class, TestCommandHandler::class);

        $result = $bus->dispatch(new TestCommand('hello'));

        $this->assertTrue($result->isOk());
        $this->assertSame('handled: hello', $result->unwrap());
    }

    #[Test]
    public function unregistered_command_returns_err(): void
    {
        $container = new Container();
        $bus = new SimpleCommandBus($container);

        $result = $bus->dispatch(new TestCommand('test'));

        $this->assertTrue($result->isErr());
        $this->assertSame('not_found', $result->match(fn() => null, fn($e) => $e->code()));
    }

    #[Test]
    public function middleware_executes_in_order(): void
    {
        $container = new Container();
        $bus = new SimpleCommandBus($container);

        $bus->register(TestCommand::class, TestCommandHandler::class);
        $bus->registerMiddleware(TestMiddleware::class);

        $container->singleton(TestMiddleware::class, fn() => new TestMiddleware());

        $result = $bus->dispatch(new TestCommand('mw'));

        $this->assertTrue($result->isOk());
        $this->assertSame('handled: mw', $result->unwrap());
    }
}

final class TestCommand implements Command
{
    public function __construct(
        public readonly string $value,
    ) {}
}

final class TestCommandHandler implements CommandHandler
{
    public function handle(Command $command): Result
    {
        if ($command instanceof TestCommand) {
            return Result::ok('handled: ' . $command->value);
        }

        return Result::err(new \PedroPCardoso\StartupKit\Core\Primitives\Errors\NotFoundError('mismatch', ''));
    }
}

final class TestMiddleware implements \PedroPCardoso\StartupKit\Core\Primitives\Cqrs\Middleware
{
    public function handle(\PedroPCardoso\StartupKit\Core\Primitives\Cqrs\Command|\PedroPCardoso\StartupKit\Core\Primitives\Cqrs\Query $message, callable $next): Result
    {
        return $next($message);
    }
}
