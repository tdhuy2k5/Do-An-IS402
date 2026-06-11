<?php

namespace App\Services;

use Prometheus\CollectorRegistry;
use Prometheus\Storage\Redis;
use Prometheus\Counter;
use Prometheus\Gauge;
use Prometheus\Histogram;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\Redis as PrometheusRedis;
class MetricsService
{
    private CollectorRegistry $registry;

    private Counter $httpRequestsTotal;
    private Histogram $httpRequestDuration;
    private Counter $httpRequestsErrors;
    private Gauge $appInfo;
    private Counter $dbQueries;
    private Histogram $dbQueryDuration;
    private Gauge $activeConnections;

    private bool $initialized = false;

    public function __construct()
    {
        $this->registry = new CollectorRegistry(
            new PrometheusRedis([
                'host' => env('REDIS_HOST', '127.0.0.1'),
                'port' => env('REDIS_PORT', 6379),
                'password' => env('REDIS_PASSWORD', null),
                'database' => (int)env('REDIS_DB', 0),
                'timeout' => 0.1,
                'read_timeout' => 10,
                'persistent_connections' => true,
            ])
        );

        $this->initializeMetrics();
    }

    /**
     * Initialize all metrics (run only once)
     */
    private function initializeMetrics(): void
    {
        if ($this->initialized) {
            return;
        }

        $this->initialized = true;

        // HTTP Requests Total Counter
        $this->httpRequestsTotal = $this->registry->registerCounter(
            'app',
            'http_requests_total',
            'Total HTTP requests',
            ['method', 'endpoint', 'status']
        );

        // HTTP Request Duration Histogram
        $this->httpRequestDuration = $this->registry->registerHistogram(
            'app',
            'http_request_duration_seconds',
            'HTTP request duration in seconds',
            ['method', 'endpoint', 'status'],
            [0.001, 0.005, 0.01, 0.05, 0.1, 0.5, 1, 2, 5]
        );

        // HTTP Errors Counter
        $this->httpRequestsErrors = $this->registry->registerCounter(
            'app',
            'http_requests_errors_total',
            'Total HTTP errors',
            ['method', 'endpoint', 'status', 'error_type']
        );

        // App Info Gauge
        $this->appInfo = $this->registry->registerGauge(
            'app',
            'info',
            'Application info',
            ['app_name', 'app_version', 'environment']
        );

        $this->appInfo->set(
            1,
            [
                config('app.name'),
                config('app.version', '1.0.0'),
                config('app.env')
            ]
        );

        // DB Queries Counter
        $this->dbQueries = $this->registry->registerCounter(
            'app',
            'database_queries_total',
            'Total database queries',
            ['connection', 'operation']
        );

        // DB Query Duration Histogram
        $this->dbQueryDuration = $this->registry->registerHistogram(
            'app',
            'database_query_duration_seconds',
            'Database query duration in seconds',
            ['connection', 'operation'],
            [0.001, 0.005, 0.01, 0.05, 0.1, 0.5, 1]
        );

        // Active DB Connections Gauge
        $this->activeConnections = $this->registry->registerGauge(
            'app',
            'database_active_connections',
            'Number of active database connections',
            ['connection']
        );
    }

    /**
     * Record HTTP request
     */
    public function recordHttpRequest(
        string $method,
        string $endpoint,
        int $status,
        float $duration
    ): void {
        $labels = [$method, $endpoint, (string)$status];

        $this->httpRequestsTotal->inc($labels);
        $this->httpRequestDuration->observe($duration, $labels);

        if ($status >= 400) {
            $this->httpRequestsErrors->inc([
                $method,
                $endpoint,
                (string)$status,
                $this->getErrorType($status)
            ]);
        }
    }

    /**
     * Record database query
     */
    public function recordDatabaseQuery(
        string $connection,
        string $operation,
        float $duration
    ): void {
        $labels = [$connection, $operation];

        $this->dbQueries->inc($labels);
        $this->dbQueryDuration->observe($duration, $labels);
    }

    /**
     * Set active DB connections
     */
    public function setActiveConnections(string $connection, int $count): void
    {
        $this->activeConnections->set($count, [$connection]);
    }

    /**
     * Error classification
     */
    private function getErrorType(int $status): string
    {
        return match (true) {
            $status >= 500 => 'server_error',
            $status >= 400 => 'client_error',
            default => 'unknown',
        };
    }

    /**
     * Render metrics for Prometheus
     */
    public function render(): string
    {
        $renderer = new RenderTextFormat();

        return $renderer->render(
            $this->registry->getMetricFamilySamples()
        );
    }

    /**
     * Get registry (optional use)
     */
    public function getRegistry(): CollectorRegistry
    {
        return $this->registry;
    }
}