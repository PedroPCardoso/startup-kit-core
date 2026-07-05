<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Primitives\Cqrs\Middleware;

use Cardoso\StartupKit\Core\Contracts\Tracer;
use Cardoso\StartupKit\Core\Primitives\Cqrs\Command;
use Cardoso\StartupKit\Core\Primitives\Cqrs\Middleware;
use Cardoso\StartupKit\Core\Primitives\Cqrs\Query;
use Cardoso\StartupKit\Core\Primitives\Result\Result;

final class TracingMiddleware implements Middleware
{
    public function __construct(
        private readonly Tracer $tracer,
    ) {}

    public function handle(Command|Query $message, callable $next): Result
    {
        $name = $message instanceof Command
            ? 'command.' . class_basename($message::class)
            : 'query.' . class_basename($message::class);

        $span = $this->tracer->startSpan($name, [
            'message.class' => $message::class,
        ]);

        $result = $next($message);

        if ($result->isOk()) {
            $span->setAttribute('result.status', 'ok');
        } else {
            $span->setAttribute('result.status', 'err');
            $result->match(
                ok: fn() => null,
                err: fn($error) => $span->setAttribute('error.code', $error->code()),
            );
        }

        $span->end();

        return $result;
    }
}
