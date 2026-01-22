<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MonitoringService
{
    public static function getSystemMetrics(): array
    {
        return Cache::remember('system_metrics', 60, function () {
            return [
                'database_connections' => self::getDatabaseConnections(),
                'active_users' => self::getActiveUsers(),
                'total_tenants' => self::getTotalTenants(),
                'total_payments' => self::getTotalPayments(),
                'pending_payments' => self::getPendingPayments(),
                'memory_usage' => self::getMemoryUsage(),
                'uptime' => self::getUptime(),
            ];
        });
    }

    public static function getDashboardData(): array
    {
        return [
            'metrics' => self::getSystemMetrics(),
            'recent_activities' => self::getRecentActivities(10),
            'payment_summary' => self::getPaymentSummary(),
            'tenant_summary' => self::getTenantSummary(),
        ];
    }

    private static function getDatabaseConnections(): int
    {
        try {
            return DB::table('information_schema.PROCESSLIST')
                ->where('db', DB::getDatabaseName())
                ->where('command', '!=', 'Sleep')
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private static function getActiveUsers(): int
    {
        try {
            return DB::table('users')
                ->where('updated_at', '>=', now()->subHours(1))
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private static function getTotalTenants(): int
    {
        return Cache::remember('total_tenants', 3600, function () {
            return DB::table('tenants')->count();
        });
    }

    private static function getTotalPayments(): int
    {
        return Cache::remember('total_payments', 3600, function () {
            return DB::table('payments')->count();
        });
    }

    private static function getPendingPayments(): int
    {
        return Cache::remember('pending_payments', 300, function () {
            return DB::table('payments')->where('status', 'pending')->count();
        });
    }

    private static function getMemoryUsage(): array
    {
        $memory = memory_get_usage(true);
        $peak = memory_get_peak_usage(true);

        return [
            'current_mb' => round($memory / 1024 / 1024, 2),
            'peak_mb' => round($peak / 1024 / 1024, 2),
            'limit_mb' => (int) ini_get('memory_limit'),
        ];
    }

    private static function getUptime(): int
    {
        return time() - $_SERVER['REQUEST_TIME_FLOAT'] ?? 0;
    }

    private static function getRecentActivities(int $limit = 10): array
    {
        try {
            return Cache::remember('recent_activities', 300, function () use ($limit) {
                return DB::table('users')
                    ->orderBy('updated_at', 'desc')
                    ->limit($limit)
                    ->get(['name', 'email', 'updated_at'])
                    ->toArray();
            });
        } catch (\Exception $e) {
            return [];
        }
    }

    private static function getPaymentSummary(): array
    {
        return Cache::remember('payment_summary', 300, function () {
            $payments = DB::table('payments')
                ->selectRaw('status, COUNT(*) as count, SUM(amount) as total')
                ->groupBy('status')
                ->get();

            return $payments->mapWithKeys(fn($p) => [$p->status => $p->count])->toArray();
        });
    }

    private static function getTenantSummary(): array
    {
        return Cache::remember('tenant_summary', 3600, function () {
            $tenants = DB::table('tenants')
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get();

            return $tenants->mapWithKeys(fn($t) => [$t->status => $t->count])->toArray();
        });
    }
}
