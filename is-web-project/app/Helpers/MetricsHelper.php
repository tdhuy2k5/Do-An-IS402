<?php

if (!function_exists('metrics')) {
    /**
     * Get the Metrics service instance
     *
     * @return \App\Services\MetricsService
     */
    function metrics()
    {
        return app(\App\Services\MetricsService::class);
    }
}

if (!function_exists('record_db_query')) {
    /**
     * Record a database query metric
     *
     * @param string $connection The database connection name
     * @param string $operation The SQL operation (select, insert, update, delete, etc.)
     * @param float $duration The query duration in seconds
     * @return void
     */
    function record_db_query(string $connection, string $operation, float $duration)
    {
        metrics()->recordDatabaseQuery($connection, $operation, $duration);
    }
}

if (!function_exists('record_http_request')) {
    /**
     * Record an HTTP request metric
     *
     * @param string $method The HTTP method (GET, POST, etc.)
     * @param string $endpoint The endpoint/route name
     * @param int $status The HTTP status code
     * @param float $duration The request duration in seconds
     * @return void
     */
    function record_http_request(string $method, string $endpoint, int $status, float $duration)
    {
        metrics()->recordHttpRequest($method, $endpoint, $status, $duration);
    }
}
