<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Drivers;

use PedroPCardoso\StartupKit\Core\Contracts\ResilientDriverRegistry;
use PedroPCardoso\StartupKit\Core\Drivers\Database\DatabaseDriver;
use PedroPCardoso\StartupKit\Core\Drivers\Redis\RedisDriver;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Redis\Factory as RedisFactory;
use Illuminate\Database\DatabaseManager;

final class DriverBootstrap
{
    public function __construct(
        private readonly ConfigRepository $config,
        private readonly Container $container,
    ) {}

    public function registerConfiguredDrivers(ResilientDriverRegistry $registry): void
    {
        /** @var array<string, array<string, mixed>> $drivers */
        $drivers = $this->config->get('startup-kit-core.drivers', []);

        foreach ($drivers as $name => $settings) {
            if (!$this->isEnabled($settings)) {
                continue;
            }

            $adapter = (string) ($settings['adapter'] ?? $name);
            $connection = isset($settings['connection']) ? (string) $settings['connection'] : null;

            $registry->register(
                $name,
                $this->makeDriver($name, $adapter, $connection),
            );
        }
    }

    /**
     * @param array<string, mixed> $settings
     */
    private function isEnabled(array $settings): bool
    {
        return (bool) ($settings['enabled'] ?? true);
    }

    private function makeDriver(string $name, string $adapter, ?string $connection): \PedroPCardoso\StartupKit\Core\Contracts\ResilientDriver
    {
        return match ($adapter) {
            'redis' => $this->makeRedisDriver($name, $adapter, $connection),
            'database' => $this->makeDatabaseDriver($name, $adapter, $connection),
            default => new UnavailableDriver(
                name: $name,
                message: sprintf('%s adapter is not installed or configured.', ucfirst($adapter)),
                metadata: [
                    'adapter' => 'unavailable',
                    'configured_adapter' => $adapter,
                    'connection' => $connection,
                ],
            ),
        };
    }

    private function makeRedisDriver(string $name, string $adapter, ?string $connection): \PedroPCardoso\StartupKit\Core\Contracts\ResilientDriver
    {
        if (!$this->container->bound(RedisFactory::class)) {
            return new UnavailableDriver(
                name: $name,
                message: 'Redis adapter is not installed or configured.',
                metadata: [
                    'adapter' => 'unavailable',
                    'configured_adapter' => $adapter,
                    'connection' => $connection,
                ],
            );
        }

        return new RedisDriver(
            redis: $this->container->make(RedisFactory::class),
            connection: $connection,
        );
    }

    private function makeDatabaseDriver(string $name, string $adapter, ?string $connection): \PedroPCardoso\StartupKit\Core\Contracts\ResilientDriver
    {
        if (!$this->container->bound('db')) {
            return new UnavailableDriver(
                name: $name,
                message: 'Database adapter is not installed or configured.',
                metadata: [
                    'adapter' => 'unavailable',
                    'configured_adapter' => $adapter,
                    'connection' => $connection,
                ],
            );
        }

        return new DatabaseDriver(
            name: $name,
            db: $this->container->make(DatabaseManager::class),
            connection: $connection,
        );
    }
}
