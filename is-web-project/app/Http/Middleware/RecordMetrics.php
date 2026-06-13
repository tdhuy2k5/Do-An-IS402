<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\MetricsService;
use Illuminate\Http\Request;

class RecordMetrics
{
    private MetricsService $metrics;

    public function __construct(MetricsService $metrics)
    {
        $this->metrics = $metrics;
    }

    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);

        $response = $next($request);

        $duration = microtime(true) - $start;

        $this->metrics->recordHttpRequest(
            $request->method(),
            $request->path(),
            $response->getStatusCode(),
            $duration
        );

        return $response;
    }
}
