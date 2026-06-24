<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{

    public function check(): JsonResponse
    {
        $status = [
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
            'checks' => [],
        ];


        $status['checks']['app'] = [
            'status' => 'ok',
            'message' => 'Application is running',
        ];


        try {
            DB::connection()->getPdo();
            $status['checks']['database'] = [
                'status' => 'ok',
                'message' => 'Database connection successful',
            ];
        } catch (\Exception $e) {
            $status['checks']['database'] = [
                'status' => 'error',
                'message' => 'Database connection failed: '.$e->getMessage(),
            ];

            return response()->json($status, 503);
        }

        return response()->json($status, 200);
    }
}
