<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core;

use Cardoso\StartupKit\Core\Api\Http\HealthController;
use Cardoso\StartupKit\Core\Api\Http\ShutdownController;
use Cardoso\StartupKit\Core\Api\Http\StartupController;
use Cardoso\StartupKit\Core\Api\Services\HealthService;
use Cardoso\StartupKit\Core\Api\Services\ShutdownService;
use Cardoso\StartupKit\Core\Api\Services\StartupService;
use Cardoso\StartupKit\Core\Contracts\EventBus;
use Cardoso\StartupKit\Core\Contracts\Logger;
use Cardoso\StartupKit\Core\Contracts\Outbox;
use Cardoso\StartupKit\Core\Contracts\ResilientDriverRegistry;
use Cardoso\StartupKit\Core\Contracts\Tracer;
use Cardoso\StartupKit\Core\Contracts\UnitOfWork;
use Cardoso\StartupKit\Core\Drivers\DriverRegistry;
use Cardoso\StartupKit\Core\EventBus\DatabaseUnitOfWork;
use Cardoso\StartupKit\Core\EventBus\DbOutbox;
use Cardoso\StartupKit\Core\EventBus\QueueEventBus;
use Cardoso\StartupKit\Core\Logging\MultiChannelLogger;
use Cardoso\StartupKit\Core\Primitives\Cqrs\CommandBus;
use Cardoso\StartupKit\Core\Primitives\Cqrs\QueryBus;
use Cardoso\StartupKit\Core\Primitives\Cqrs\SimpleCommandBus;
use Cardoso\StartupKit\Core\Primitives\Cqrs\SimpleQueryBus;
use Cardoso\StartupKit\Core\Primitives\Tracing\NoopTracer;
use Illuminate\Support\ServiceProvider;

final class StartupKitCoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/startup-kit-core.php', 'startup-kit-core');

        $this->app->singleton(Tracer::class, function () {
            return new NoopTracer();
        });

        $this->app->singleton(Logger::class, function ($app) {
            return new MultiChannelLogger(
                logger: $app->make(\Psr\Log\LoggerInterface::class),
                tracer: $app->make(Tracer::class),
            );
        });

        $this->app->singleton(CommandBus::class, function ($app) {
            return new SimpleCommandBus($app);
        });

        $this->app->singleton(QueryBus::class, function ($app) {
            return new SimpleQueryBus($app);
        });

        $this->app->singleton(EventBus::class, function ($app) {
            return new QueueEventBus(
                queue: $app->make(\Illuminate\Contracts\Queue\Queue::class),
            );
        });

        $this->app->singleton(Outbox::class, function ($app) {
            return new DbOutbox(
                db: $app->make(\Illuminate\Database\ConnectionInterface::class),
                queue: $app->make(\Illuminate\Contracts\Queue\Queue::class),
            );
        });

        $this->app->singleton(UnitOfWork::class, function ($app) {
            return new DatabaseUnitOfWork(
                db: $app->make(\Illuminate\Database\ConnectionInterface::class),
            );
        });

        $this->app->singleton(ResilientDriverRegistry::class, function () {
            return new DriverRegistry();
        });

        $this->app->singleton(HealthService::class, function ($app) {
            return new HealthService($app->make(ResilientDriverRegistry::class));
        });

        $this->app->singleton(ShutdownService::class, function ($app) {
            return new ShutdownService($app->make(ResilientDriverRegistry::class));
        });

        $this->app->singleton(StartupService::class, function ($app) {
            return new StartupService($app->make(ResilientDriverRegistry::class));
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/startup-kit-core.php' => config_path('startup-kit-core.php'),
        ], 'startup-kit-core-config');

        $this->publishes([
            __DIR__ . '/../../database/migrations/' => database_path('migrations'),
        ], 'startup-kit-core-migrations');

        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');
    }
}
