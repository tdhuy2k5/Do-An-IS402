<?php

namespace App\Providers;

use App\Services\MetricsService;
use Illuminate\Support\ServiceProvider;

class MetricsServiceProvider extends ServiceProvider
{

    public function register(): void
    {

        $this->app->singleton(MetricsService::class, function () {
            return new MetricsService();
        });
    }


    public function boot(): void
    {

    }
}