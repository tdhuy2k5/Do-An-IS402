<?php

namespace App\Providers;

use App\Services\MetricsService;
use Illuminate\Support\ServiceProvider;

class MetricsServiceProvider extends ServiceProvider
{
    /**
     * Register services
     */
    public function register(): void
    {
        // ✅ Singleton = one instance per container (per pod)
        $this->app->singleton(MetricsService::class, function () {
            return new MetricsService();
        });
    }

    /**
     * Bootstrap services
     *
     * Keep empty to avoid early initialization issues
     */
    public function boot(): void
    {
        // nothing here
    }
}