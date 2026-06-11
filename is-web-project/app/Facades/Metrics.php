<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void recordHttpRequest(string $method, string $endpoint, int $status, float $duration)
 * @method static void recordDatabaseQuery(string $connection, string $operation, float $duration)
 * @method static void setActiveConnections(string $connection, int $count)
 * @method static string render()
 * 
 * @see \App\Services\MetricsService
 */
class Metrics extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Services\MetricsService::class;
    }
}
