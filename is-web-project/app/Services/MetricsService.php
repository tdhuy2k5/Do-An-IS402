<?php

namespace App\Services;

use Prometheus\CollectorRegistry;
use Prometheus\Storage\Redis as PrometheusRedis;
use Prometheus\Counter;
use Prometheus\Gauge;
use Prometheus\Histogram;
use Prometheus\RenderTextFormat;

class MetricsService
{
    private CollectorRegistry $registry;

    public function __construct()
    {
        $this->registry = new CollectorRegistry(
            new PrometheusRedis([
                'scheme' => 'tls',
                'host' => env('REDIS_HOST'),
                'port' => 6380,
                'password' => env('REDIS_PASSWORD'),
                'database' => (int) env('REDIS_DB', 0),

                'timeout' => 5,
                'read_timeout' => 5,

                'context' => stream_context_create([
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ]),
            ])
        );

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
            'info',
            'Application info',
            ['app_name', 'app_version', 'environment']
        )->set(1, [
            config('app.name'),
            config('app.version', '1.0.0'),
            config('app.env')
        ]);
    }

    public function render(): string
    {
        return (new RenderTextFormat())->render(
            $this->registry->getMetricFamilySamples()
        );
    }
}
