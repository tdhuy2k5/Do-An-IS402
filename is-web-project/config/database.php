<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection
    |--------------------------------------------------------------------------
    */
    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    */
    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DB_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT', 3306),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),

            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',

            // ✅ REQUIRED FOR AZURE MYSQL SECURE TRANSPORT
            'sslmode' => 'required',

            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT', 5432),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),

            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Table
    |--------------------------------------------------------------------------
    */
    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Configuration
    |--------------------------------------------------------------------------
    */
    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),

            'prefix' => Str::slug(env('APP_NAME', 'laravel'), '_') . '_database_',
        ],

        'default' => [
            'host' => env('REDIS_HOST'),
            'port' => env('REDIS_PORT', 6380),
            'password' => env('REDIS_PASSWORD'),
            'database' => env('REDIS_DB', 0),

            // Azure Cache for Redis: port 6380 is TLS-only.
            // `scheme` must be set explicitly — the `ssl` array alone
            // does not make phpredis upgrade the connection.
            'scheme' => 'tls',
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ],

        'cache' => [
            'host' => env('REDIS_HOST'),
            'port' => env('REDIS_PORT', 6380),
            'password' => env('REDIS_PASSWORD'),
            'database' => env('REDIS_CACHE_DB', 1),

            'scheme' => 'tls',
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ],
    ],
];
