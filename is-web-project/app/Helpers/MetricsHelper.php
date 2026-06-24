<?php

if (!function_exists('metrics')) {

    function metrics()
    {
        return app(\App\Services\MetricsService::class);
    }
}

if (!function_exists('record_db_query')) {

    function record_db_query(string $connection, string $operation, float $duration)
    {
        metrics()->recordDatabaseQuery($connection, $operation, $duration);
    }
}

if (!function_exists('record_http_request')) {

    function record_http_request(string $method, string $endpoint, int $status, float $duration)
    {
        metrics()->recordHttpRequest($method, $endpoint, $status, $duration);
    }
}
