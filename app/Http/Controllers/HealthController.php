<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function check(): JsonResponse
    {
        $health = [
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
            'checks' => [
                'database' => $this->checkDatabase(),
                'cache' => $this->checkCache(),
                'storage' => $this->checkStorage(),
            ],
        ];

        $overallStatus = collect($health['checks'])->every(fn($check) => $check['status'] === 'ok') ? 'healthy' : 'degraded';
        $health['status'] = $overallStatus;

        $statusCode = $overallStatus === 'healthy' ? 200 : 503;

        return response()->json($health, $statusCode);
    }

    public function liveness(): JsonResponse
    {
        return response()->json(['status' => 'alive'], 200);
    }

    public function readiness(): JsonResponse
    {
        try {
            DB::connection()->getPdo();
            return response()->json(['status' => 'ready'], 200);
        } catch (QueryException $e) {
            return response()->json(['status' => 'not_ready', 'reason' => 'database_unavailable'], 503);
        }
    }

    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'ok', 'message' => 'Database connection successful'];
        } catch (QueryException $e) {
            return ['status' => 'error', 'message' => 'Database connection failed'];
        }
    }

    private function checkCache(): array
    {
        try {
            $testKey = 'health_check_' . time();
            Cache::put($testKey, 'test', 10);
            Cache::forget($testKey);
            return ['status' => 'ok', 'message' => 'Cache operational'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Cache check failed'];
        }
    }

    private function checkStorage(): array
    {
        try {
            $testFile = storage_path('logs/.health_check');
            if (!is_writable(storage_path('logs'))) {
                return ['status' => 'error', 'message' => 'Storage not writable'];
            }
            return ['status' => 'ok', 'message' => 'Storage writable'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Storage check failed'];
        }
    }
}
