<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Tests\Integration;

use Cardoso\StartupKit\Core\Contracts\EventBus;
use Cardoso\StartupKit\Core\Contracts\Logger;
use Cardoso\StartupKit\Core\Contracts\Outbox;
use Cardoso\StartupKit\Core\Contracts\ResilientDriverRegistry;
use Cardoso\StartupKit\Core\Contracts\Tracer;
use Cardoso\StartupKit\Core\Contracts\UnitOfWork;
use Cardoso\StartupKit\Core\Primitives\Cqrs\CommandBus;
use Cardoso\StartupKit\Core\Primitives\Cqrs\QueryBus;
use Cardoso\StartupKit\Core\StartupKitCoreServiceProvider;
use Orchestra\Testbench\TestCase;

final class ServiceProviderBootTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            StartupKitCoreServiceProvider::class,
        ];
    }

    public function test_container_resolves_command_bus(): void
    {
        $instance = $this->app->make(CommandBus::class);

        $this->assertInstanceOf(CommandBus::class, $instance);
    }

    public function test_container_resolves_query_bus(): void
    {
        $instance = $this->app->make(QueryBus::class);

        $this->assertInstanceOf(QueryBus::class, $instance);
    }

    public function test_container_resolves_event_bus(): void
    {
        $instance = $this->app->make(EventBus::class);

        $this->assertInstanceOf(EventBus::class, $instance);
    }

    public function test_container_resolves_outbox(): void
    {
        $instance = $this->app->make(Outbox::class);

        $this->assertInstanceOf(Outbox::class, $instance);
    }

    public function test_container_resolves_unit_of_work(): void
    {
        $instance = $this->app->make(UnitOfWork::class);

        $this->assertInstanceOf(UnitOfWork::class, $instance);
    }

    public function test_container_resolves_logger(): void
    {
        $instance = $this->app->make(Logger::class);

        $this->assertInstanceOf(Logger::class, $instance);
    }

    public function test_container_resolves_tracer(): void
    {
        $instance = $this->app->make(Tracer::class);

        $this->assertInstanceOf(Tracer::class, $instance);
    }

    public function test_container_resolves_driver_registry(): void
    {
        $instance = $this->app->make(ResilientDriverRegistry::class);

        $this->assertInstanceOf(ResilientDriverRegistry::class, $instance);
    }

    public function test_bindings_are_singletons(): void
    {
        $first = $this->app->make(CommandBus::class);
        $second = $this->app->make(CommandBus::class);

        $this->assertSame($first, $second);
    }
}
