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
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,

            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT', '5432'),
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
    | Migrations
    |--------------------------------------------------------------------------
    */
    'migrations' => [
        'table' => 'migrations',
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Configuration (FIXED FOR AZURE REDIS + PHPREDIS)
    |--------------------------------------------------------------------------
    */
    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),

            'prefix' => Str::slug(env('APP_NAME', 'laravel'), '_') . '_database_',
        ],

        /*
        |-------------------------
        | DEFAULT REDIS (CACHE + SESSION + QUEUE)
        |-------------------------
        */
        'default' => [
            'host' => env('REDIS_HOST'),
            'port' => env('REDIS_PORT', 6380),
            'password' => env('REDIS_PASSWORD'),
            'database' => env('REDIS_DB', 0),

            // IMPORTANT: Azure Redis TLS support for phpredis
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ],

        /*
        |-------------------------
        | CACHE REDIS DB
        |-------------------------
        */
        'cache' => [
            'host' => env('REDIS_HOST'),
            'port' => env('REDIS_PORT', 6380),
            'password' => env('REDIS_PASSWORD'),
            'database' => env('REDIS_CACHE_DB', 1),

            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ],
    ],
];
