<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core;

use PedroPCardoso\StartupKit\Core\Api\Http\HealthController;
use PedroPCardoso\StartupKit\Core\Api\Http\ShutdownController;
use PedroPCardoso\StartupKit\Core\Api\Http\StartupController;
use PedroPCardoso\StartupKit\Core\Api\Services\HealthService;
use PedroPCardoso\StartupKit\Core\Api\Services\ShutdownService;
use PedroPCardoso\StartupKit\Core\Api\Services\StartupService;
use PedroPCardoso\StartupKit\Core\Contracts\EventBus;
use PedroPCardoso\StartupKit\Core\Contracts\Logger;
use PedroPCardoso\StartupKit\Core\Contracts\Outbox;
use PedroPCardoso\StartupKit\Core\Contracts\ResilientDriverRegistry;
use PedroPCardoso\StartupKit\Core\Contracts\Tracer;
use PedroPCardoso\StartupKit\Core\Contracts\UnitOfWork;
use PedroPCardoso\StartupKit\Core\Drivers\DriverBootstrap;
use PedroPCardoso\StartupKit\Core\Drivers\DriverRegistry;
use PedroPCardoso\StartupKit\Core\EventBus\DatabaseUnitOfWork;
use PedroPCardoso\StartupKit\Core\EventBus\DbOutbox;
use PedroPCardoso\StartupKit\Core\EventBus\QueueEventBus;
use PedroPCardoso\StartupKit\Core\Logging\MultiChannelLogger;
use PedroPCardoso\StartupKit\Core\Primitives\Cqrs\CommandBus;
use PedroPCardoso\StartupKit\Core\Primitives\Cqrs\QueryBus;
use PedroPCardoso\StartupKit\Core\Primitives\Cqrs\SimpleCommandBus;
use PedroPCardoso\StartupKit\Core\Primitives\Cqrs\SimpleQueryBus;
use PedroPCardoso\StartupKit\Core\Primitives\Tracing\NoopTracer;
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

        $this->app->singleton(DriverBootstrap::class, function ($app) {
            return new DriverBootstrap(
                config: $app->make(\Illuminate\Contracts\Config\Repository::class),
                container: $app,
            );
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

        $this->app->afterResolving(ResilientDriverRegistry::class, function (ResilientDriverRegistry $registry, $app): void {
            $app->make(DriverBootstrap::class)->registerConfiguredDrivers($registry);
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
