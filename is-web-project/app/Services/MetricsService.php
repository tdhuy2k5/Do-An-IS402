<?php

namespace App\Services;

use Prometheus\CollectorRegistry;
use Prometheus\Storage\InMemory;
use Prometheus\RenderTextFormat;

class MetricsService
{
    private CollectorRegistry $registry;

    public function __construct()
    {
        // ✅ IN-MEMORY (NO REDIS)
        $this->registry = new CollectorRegistry(new InMemory());

        $this->initializeMetrics();
    }

    private function initializeMetrics(): void
    {
        $this->registry->registerCounter(
            'app',
            'http_requests_total',
            'Total HTTP requests',
            ['method', 'endpoint', 'status']
        );

        $this->registry->registerHistogram(
            'app',
            'http_request_duration_seconds',
            'HTTP request duration',
            ['method', 'endpoint', 'status'],
            [0.001, 0.01, 0.1, 1, 5]
        );

        $this->registry->registerGauge(
            'app',
            'app_info',
            'Application info',
            ['app_name', 'app_version', 'environment']
        )->set(1, [
            config('app.name'),
            config('app.version', '1.0.0'),
            config('app.env')
        ]);
    }

    // ✅ FIX: ADD METHOD (your middleware requires it)
    public function recordHttpRequest(string $method, string $endpoint, int $status, float $duration): void
    {
        $counter = $this->registry->getOrRegisterCounter(
            'app',
            'http_requests_total',
            'Total HTTP requests',
            ['method', 'endpoint', 'status']
        );

        $counter->inc([$method, $endpoint, (string)$status]);

        $histogram = $this->registry->getOrRegisterHistogram(
            'app',
            'http_request_duration_seconds',
            'HTTP request duration',
            ['method', 'endpoint', 'status'],
            [0.001, 0.01, 0.1, 1, 5]
        );

        $histogram->observe($duration, [$method, $endpoint, (string)$status]);
    }

    public function render(): string
    {
        return (new RenderTextFormat())->render(
            $this->registry->getMetricFamilySamples()
        );
    }
}
