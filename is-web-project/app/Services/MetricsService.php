<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\InMemory;
use Prometheus\Storage\Redis;

class MetricsService
{
    private CollectorRegistry $registry;

    public function __construct()
    {
        $driver = env('PROMETHEUS_STORAGE_DRIVER', 'memory');
        $storage = null;

        if ($driver === 'redis') {
            try {
                $host = env('REDIS_HOST', '127.0.0.1');
                $scheme = env('REDIS_SCHEME', 'tls');
                $port = (int) env('REDIS_PORT', 6380);
                $password = env('REDIS_PASSWORD') === 'null' || env('REDIS_PASSWORD') === '' ? null : env('REDIS_PASSWORD');

                if ($scheme === 'tls' || $scheme === 'rediss') {
                    if (!str_starts_with($host, 'tls://')) {
                        $host = 'tls://' . $host;
                    }

                    $redis = new \Redis();


                    $context = [
                        'stream' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'min_proto_version' => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
                            'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
                        ]
                    ];


                    $connected = $redis->connect($host, $port, 0.1, null, 0, 0, $context);
                    if (!$connected) {
                        throw new \Exception("Could not connect to Redis server: " . $redis->getLastError());
                    }

                    if ($password !== null) {
                        $redis->auth($password);
                    }

                    if (env('PROMETHEUS_REDIS_DB') !== null) {
                        $redis->select((int) env('PROMETHEUS_REDIS_DB'));
                    }

                    if (env('PROMETHEUS_REDIS_PREFIX') !== null) {
                        Redis::setPrefix(env('PROMETHEUS_REDIS_PREFIX'));
                    }

                    $storage = Redis::fromExistingConnection($redis);
                } else {
                    $options = [
                        'host' => $host,
                        'port' => $port,
                        'password' => $password,
                        'timeout' => 0.1,
                        'read_timeout' => '10',
                        'persistent_connections' => false,
                    ];

                    if (env('PROMETHEUS_REDIS_DB') !== null) {
                        $options['database'] = (int) env('PROMETHEUS_REDIS_DB');
                    }

                    if (env('PROMETHEUS_REDIS_PREFIX') !== null) {
                        Redis::setPrefix(env('PROMETHEUS_REDIS_PREFIX'));
                    }

                    $storage = new Redis($options);
                }
            } catch (\Throwable $e) {
                \Log::warning('Prometheus Redis connection failed, falling back to InMemory storage: ' . $e->getMessage());
                $storage = new InMemory();
            }
        }

        if (!$storage) {
            $storage = new InMemory();
        }

        $this->registry = new CollectorRegistry($storage);
    }

    public function recordHttpRequest(string $method, string $path, int $status, float $duration): void
    {
        try {
            $labels = [$method, $path, (string) $status];


            $this->registry
                ->getOrRegisterCounter(
                    'http',
                    'requests_total',
                    'Total number of HTTP requests',
                    ['method', 'route', 'status']
                )
                ->incBy(1, $labels);


            $this->registry
                ->getOrRegisterHistogram(
                    'http',
                    'request_duration_seconds',
                    'HTTP request duration in seconds',
                    ['method', 'route', 'status'],
                    [0.01, 0.05, 0.1, 0.25, 0.5, 1.0, 2.5, 5.0, 10.0]
                )
                ->observe($duration, $labels);
        } catch (\Throwable $e) {
            \Log::error('Failed to record Prometheus metrics: ' . $e->getMessage());
        }
    }

    public function render(): string
    {
        try {
            $this->collectApplicationMetrics();

            return (new RenderTextFormat())->render(
                $this->registry->getMetricFamilySamples()
            );
        } catch (\Throwable $e) {
            \Log::error('Failed to render Prometheus metrics: ' . $e->getMessage());
            return "# Failed to render metrics\n";
        }
    }

    private function collectApplicationMetrics(): void
    {

        $this->registry
            ->getOrRegisterGauge(
                'app',
                'info',
                'Application information',
                ['name', 'env']
            )
            ->set(1, [
                config('app.name'),
                config('app.env'),
            ]);


        $this->registry
            ->getOrRegisterGauge(
                'app',
                'memory_usage_bytes',
                'Current memory usage'
            )
            ->set(memory_get_usage(true));

        $this->registry
            ->getOrRegisterGauge(
                'app',
                'memory_peak_usage_bytes',
                'Peak memory usage'
            )
            ->set(memory_get_peak_usage(true));


        $load = sys_getloadavg();

        $this->registry
            ->getOrRegisterGauge(
                'app',
                'cpu_load_1m',
                'CPU load average 1 minute'
            )
            ->set($load[0] ?? 0);


        $this->registry
            ->getOrRegisterGauge(
                'app',
                'users_total',
                'Total users'
            )
            ->set(User::count());

        $this->registry
            ->getOrRegisterGauge(
                'app',
                'products_total',
                'Total products'
            )
            ->set(Product::count());

        $this->registry
            ->getOrRegisterGauge(
                'app',
                'orders_total',
                'Total orders'
            )
            ->set(Order::count());

        $this->registry
            ->getOrRegisterGauge(
                'app',
                'pending_orders_total',
                'Pending orders'
            )
            ->set(
                Order::where('status', 'pending')->count()
            );

        $this->registry
            ->getOrRegisterGauge(
                'app',
                'completed_orders_total',
                'Completed orders'
            )
            ->set(
                Order::where('status', 'completed')->count()
            );


        try {
            \DB::connection()->getPdo();

            $this->registry
                ->getOrRegisterGauge(
                    'app',
                    'database_up',
                    'Database availability'
                )
                ->set(1);
        } catch (\Throwable $e) {
            $this->registry
                ->getOrRegisterGauge(
                    'app',
                    'database_up',
                    'Database availability'
                )
                ->set(0);
        }


        try {
            cache()->put('metrics_health_check', 1, 10);

            $this->registry
                ->getOrRegisterGauge(
                    'app',
                    'cache_up',
                    'Cache availability'
                )
                ->set(
                    cache()->get('metrics_health_check') ? 1 : 0
                );
        } catch (\Throwable $e) {
            $this->registry
                ->getOrRegisterGauge(
                    'app',
                    'cache_up',
                    'Cache availability'
                )
                ->set(0);
        }
    }
}
