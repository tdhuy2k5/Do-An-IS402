<?php

namespace App\Http\Middleware;

use App\Services\MetricsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordMetrics
{
    public function __construct(
        private MetricsService $metricsService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip metrics recording for the metrics endpoint itself
        if ($request->is('metrics')) {
            return $next($request);
        }

        $startTime = microtime(true);

        $response = $next($request);

        $duration = microtime(true) - $startTime;

        // Get route name or path as endpoint identifier
        $endpoint = $request->route()?->getName() ?? $request->path();

        // Record the metrics
        $this->metricsService->recordHttpRequest(
            $request->getMethod(),
            $endpoint,
            $response->getStatusCode(),
            $duration
        );

        return $response;
    }
}
