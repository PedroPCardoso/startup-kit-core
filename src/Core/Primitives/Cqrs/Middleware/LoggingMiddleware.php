<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Primitives\Cqrs\Middleware;

use PedroPCardoso\StartupKit\Core\Contracts\Logger;
use PedroPCardoso\StartupKit\Core\Primitives\Cqrs\Command;
use PedroPCardoso\StartupKit\Core\Primitives\Cqrs\Middleware;
use PedroPCardoso\StartupKit\Core\Primitives\Cqrs\Query;
use PedroPCardoso\StartupKit\Core\Primitives\Result\Result;

final class LoggingMiddleware implements Middleware
{
    public function __construct(
        private readonly Logger $logger,
    ) {}

    public function handle(Command|Query $message, callable $next): Result
    {
        $start = microtime(true);

        $result = $next($message);

        $duration = (microtime(true) - $start) * 1000;

        $context = [
            'message' => $message::class,
            'duration_ms' => round($duration, 2),
            'is_ok' => $result->isOk(),
        ];

        if ($result->isErr()) {
            $result->match(
                ok: fn() => null,
                err: fn($error) => $context['error_code'] = $error->code(),
            );
            $this->logger->error('Command/Query failed', $context);
        } else {
            $this->logger->info('Command/Query succeeded', $context);
        }

        return $result;
    }
}
