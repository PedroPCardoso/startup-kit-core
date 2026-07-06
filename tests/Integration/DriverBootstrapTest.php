<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Tests\Integration;

use PedroPCardoso\StartupKit\Core\Contracts\ResilientDriverRegistry;
use PedroPCardoso\StartupKit\Core\StartupKitCoreServiceProvider;
use Orchestra\Testbench\TestCase;

final class DriverBootstrapTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            StartupKitCoreServiceProvider::class,
        ];
    }

    public function test_provider_registers_default_enabled_drivers_in_registry(): void
    {
        $registry = $this->app->make(ResilientDriverRegistry::class);

        $this->assertSame(['redis'], array_keys($registry->all()));
    }
}
