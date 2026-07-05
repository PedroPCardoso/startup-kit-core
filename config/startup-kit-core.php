<?php

declare(strict_types=1);

return [
    'routes' => [
        'prefix' => '',
        'middleware' => ['api'],
    ],

    'event_bus' => [
        'queue' => env('STARTUP_KIT_EVENT_QUEUE', 'default'),
        'connection' => env('STARTUP_KIT_EVENT_CONNECTION', 'sync'),
    ],

    'outbox' => [
        'table' => 'startup_kit_outbox',
        'cleanup_hours' => 24,
        'max_retries' => 3,
        'batch_size' => 100,
    ],

    'drivers' => [
        'redis' => [
            'enabled' => env('STARTUP_KIT_REDIS_ENABLED', true),
            'adapter' => 'redis',
            'connection' => env('STARTUP_KIT_REDIS_CONNECTION', 'default'),
        ],
        'mysql' => [
            'enabled' => env('STARTUP_KIT_MYSQL_ENABLED', false),
            'adapter' => 'database',
            'connection' => env('STARTUP_KIT_MYSQL_CONNECTION', 'mysql'),
        ],
        'postgres' => [
            'enabled' => env('STARTUP_KIT_POSTGRES_ENABLED', false),
            'adapter' => 'database',
            'connection' => env('STARTUP_KIT_POSTGRES_CONNECTION', 'pgsql'),
        ],
        'mongodb' => [
            'enabled' => env('STARTUP_KIT_MONGO_ENABLED', false),
            'adapter' => 'mongodb',
            'connection' => env('STARTUP_KIT_MONGO_CONNECTION', 'mongodb'),
        ],
        'immudb' => [
            'enabled' => env('STARTUP_KIT_IMMUDB_ENABLED', false),
            'adapter' => 'immudb',
            'connection' => env('STARTUP_KIT_IMMUDB_CONNECTION', 'immudb'),
        ],
        'clickhouse' => [
            'enabled' => env('STARTUP_KIT_CLICKHOUSE_ENABLED', false),
            'adapter' => 'clickhouse',
            'connection' => env('STARTUP_KIT_CLICKHOUSE_CONNECTION', 'clickhouse'),
        ],
    ],

    'health' => [
        'enabled' => true,
    ],

    'shutdown' => [
        'enabled' => true,
        'graceful_timeout' => 30,
    ],

    'startup' => [
        'enabled' => true,
    ],

    'logging' => [
        'channel' => env('STARTUP_KIT_LOG_CHANNEL', 'stack'),
        'include_trace' => true,
    ],
];
