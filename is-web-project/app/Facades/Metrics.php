<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;


class Metrics extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Services\MetricsService::class;
    }
}
