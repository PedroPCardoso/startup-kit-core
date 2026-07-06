<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Tests\Integration;

use PedroPCardoso\StartupKit\Core\Contracts\ResilientDriverRegistry;
use PedroPCardoso\StartupKit\Core\StartupKitCoreServiceProvider;
use Orchestra\Testbench\TestCase;

final class DriverBootstrapConfiguredDriversTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            StartupKitCoreServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('startup-kit-core.drivers', [
            'redis' => [
                'enabled' => false,
                'adapter' => 'redis',
                'connection' => 'default',
            ],
            'mysql' => [
                'enabled' => true,
                'adapter' => 'database',
                'connection' => 'testing',
            ],
            'mongodb' => [
                'enabled' => true,
                'adapter' => 'mongodb',
                'connection' => 'mongodb',
            ],
        ]);
    }

    public function test_enabled_drivers_are_registered_in_config_order_and_disabled_drivers_are_ignored(): void
    {
        $registry = $this->app->make(ResilientDriverRegistry::class);

        $this->assertSame(['mysql', 'mongodb'], array_keys($registry->all()));
    }

    public function test_health_checks_preserve_config_order_and_use_stable_common_shape(): void
    {
        $registry = $this->app->make(ResilientDriverRegistry::class);

        $checks = $registry->healthChecks();

        $this->assertSame(['mysql', 'mongodb'], array_keys($checks));

        $mysql = $checks['mysql']->toArray();
        $mongodb = $checks['mongodb']->toArray();

        $this->assertSame(['driver', 'healthy', 'message', 'metadata'], array_keys($mysql));
        $this->assertSame('mysql', $mysql['driver']);
        $this->assertIsBool($mysql['healthy']);
        $this->assertSame('database', $mysql['metadata']['adapter']);
        $this->assertSame('testing', $mysql['metadata']['connection']);
        $this->assertArrayHasKey('circuit_state', $mysql['metadata']);

        $this->assertSame(['driver', 'healthy', 'message', 'metadata'], array_keys($mongodb));
        $this->assertSame('mongodb', $mongodb['driver']);
        $this->assertFalse($mongodb['healthy']);
        $this->assertSame('unavailable', $mongodb['metadata']['adapter']);
        $this->assertSame('mongodb', $mongodb['metadata']['configured_adapter']);
        $this->assertSame('mongodb', $mongodb['metadata']['connection']);
    }
}
