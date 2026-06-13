<?php

namespace App\Services;

use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\Redis as PrometheusRedis;
use Throwable;

class MetricsService
{
    private ?CollectorRegistry $registry = null;

    private bool $enabled = true;

    public function __construct()
    {
        try {
            // 🔥 IMPORTANT: fail safely, do NOT crash app
            $this->registry = new CollectorRegistry(
                new PrometheusRedis([
                    'host' => env('REDIS_HOST', '127.0.0.1'),
                    'port' => env('REDIS_PORT', 6379),
                    'password' => env('REDIS_PASSWORD', null),
                    'database' => (int) env('REDIS_DB', 0),
                    'timeout' => 0.2,
                    'read_timeout' => 1,
                    'persistent_connections' => false,
                ])
            );
        } catch (Throwable $e) {
            // ❌ NEVER crash app because of metrics
            $this->enabled = false;
            $this->registry = null;
        }
    }

    private function safe(callable $fn): void
    {
        if (!$this->enabled || !$this->registry) {
            return;
        }

        try {
            $fn();
        } catch (Throwable $e) {
            // swallow metrics errors
        }
    }

    public function recordHttpRequest(
        string $method,
        string $endpoint,
        int $status,
        float $duration
    ): void {
        $this->safe(function () use ($method, $endpoint, $status, $duration) {
            $labels = [$method, $endpoint, (string)$status];

            $counter = $this->registry->getOrRegisterCounter(
                'app',
                'http_requests_total',
                'Total HTTP requests',
                ['method', 'endpoint', 'status']
            );

            $counter->inc($labels);
        });
    }

    public function recordDatabaseQuery(
        string $connection,
        string $operation,
        float $duration
    ): void {
        $this->safe(function () use ($connection, $operation) {
            $counter = $this->registry->getOrRegisterCounter(
                'app',
                'database_queries_total',
                'Database queries',
                ['connection', 'operation']
            );

            $counter->inc([$connection, $operation]);
        });
    }

    public function render(): string
    {
        if (!$this->registry) {
            return '';
        }

        try {
            $renderer = new RenderTextFormat();
            return $renderer->render($this->registry->getMetricFamilySamples());
        } catch (Throwable $e) {
            return '';
        }
    }
}
