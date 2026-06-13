<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\InMemory;

class MetricsService
{
    private CollectorRegistry $registry;

    public function __construct()
    {
        $this->registry = new CollectorRegistry(
            new InMemory()
        );
    }

    public function render(): string
    {
        $this->collectApplicationMetrics();

        return (new RenderTextFormat())->render(
            $this->registry->getMetricFamilySamples()
        );
    }

    private function collectApplicationMetrics(): void
    {
        /*
         * Application Info
         */
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

        /*
         * Memory
         */
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

        /*
         * CPU Load
         */
        $load = sys_getloadavg();

        $this->registry
            ->getOrRegisterGauge(
                'app',
                'cpu_load_1m',
                'CPU load average 1 minute'
            )
            ->set($load[0] ?? 0);

        /*
         * Database metrics
         */
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

        /*
         * DB Connection
         */
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

        /*
         * Cache
         */
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
