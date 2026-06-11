<?php

namespace App\Http\Controllers;

use App\Services\MetricsService;
use Illuminate\Http\Response;

class MetricsController extends Controller
{
    public function __construct(
        private MetricsService $metricsService
    ) {}

    /**
     * Expose metrics in Prometheus format
     */
    public function index(): Response
    {
        return response(
            $this->metricsService->render(),
            200,
            ['Content-Type' => 'text/plain; version=0.0.4; charset=utf-8']
        );
    }
}
