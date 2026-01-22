<?php

namespace App\Services;

use App\Models\OperationalCost;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Cache TTL in seconds (5 minutes for dashboard data)
     */
    private const CACHE_TTL = 300;

    /**
     * Get count of active tenants
     */
    public function getActiveTenantCount(): int
    {
        return Cache::remember('dashboard.active_tenants', self::CACHE_TTL, function () {
            return Tenant::active()->count();
        });
    }

    /**
     * Get room statistics (occupied, available, maintenance)
     */
    public function getRoomStatistics(): array
    {
        return Cache::remember('dashboard.room_statistics', self::CACHE_TTL, function () {
            $rooms = Room::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            return [
                'total' => array_sum($rooms),
                'occupied' => $rooms['occupied'] ?? 0,
                'available' => $rooms['available'] ?? 0,
                'maintenance' => $rooms['maintenance'] ?? 0,
            ];
        });
    }

    /**
     * Get total income from confirmed payments
     */
    public function getTotalIncome(?Carbon $startDate = null, ?Carbon $endDate = null): float
    {
        $cacheKey = 'dashboard.total_income.' . ($startDate?->format('Y-m') ?? 'all');
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($startDate, $endDate) {
            $query = Payment::confirmed();

            if ($startDate && $endDate) {
                $query->whereBetween('payment_date', [$startDate, $endDate]);
            }

            return (float) $query->sum('amount');
        });
    }

    /**
     * Get total operational costs
     */
    public function getTotalOperationalCost(?Carbon $startDate = null, ?Carbon $endDate = null): float
    {
        $cacheKey = 'dashboard.total_operational.' . ($startDate?->format('Y-m') ?? 'all');
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($startDate, $endDate) {
            $query = OperationalCost::whereIn('status', ['recorded', 'approved']);

            if ($startDate && $endDate) {
                $query->whereBetween('cost_date', [$startDate, $endDate]);
            }

            return (float) $query->sum('amount');
        });
    }

    /**
     * Get net profit (income - operational costs)
     */
    public function getNetProfit(?Carbon $startDate = null, ?Carbon $endDate = null): float
    {
        $income = $this->getTotalIncome($startDate, $endDate);
        $costs = $this->getTotalOperationalCost($startDate, $endDate);

        return $income - $costs;
    }

    /**
     * Get financial summary for current month
     */
    public function getCurrentMonthFinancialSummary(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        return [
            'income' => $this->getTotalIncome($startOfMonth, $endOfMonth),
            'operational_cost' => $this->getTotalOperationalCost($startOfMonth, $endOfMonth),
            'net_profit' => $this->getNetProfit($startOfMonth, $endOfMonth),
            'period' => $startOfMonth->format('F Y'),
        ];
    }

    /**
     * Get payments due today
     */
    public function getPaymentsDueToday(): Collection
    {
        return Payment::with(['tenant', 'room'])
            ->where('status', 'pending')
            ->whereDate('due_date', Carbon::today())
            ->orderBy('tenant_id')
            ->get();
    }

    /**
     * Get count of payments due today
     */
    public function getPaymentsDueTodayCount(): int
    {
        return Cache::remember('dashboard.due_today_count', self::CACHE_TTL, function () {
            return Payment::pending()
                ->whereDate('due_date', Carbon::today())
                ->count();
        });
    }

    /**
     * Get overdue payments (tunggakan)
     */
    public function getOverduePayments(): Collection
    {
        return Payment::with(['tenant', 'room'])
            ->overdue()
            ->orderBy('due_date', 'asc')
            ->get();
    }

    /**
     * Get count of overdue payments
     */
    public function getOverduePaymentsCount(): int
    {
        return Cache::remember('dashboard.overdue_count', self::CACHE_TTL, function () {
            return Payment::overdue()->count();
        });
    }

    /**
     * Get total amount of overdue payments
     */
    public function getOverduePaymentsAmount(): float
    {
        return Cache::remember('dashboard.overdue_amount', self::CACHE_TTL, function () {
            return (float) Payment::overdue()->sum('amount');
        });
    }

    /**
     * Get recent payments (last 10)
     */
    public function getRecentPayments(int $limit = 10): Collection
    {
        return Payment::with(['tenant', 'room'])
            ->confirmed()
            ->orderBy('payment_date', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get monthly income trend (last 6 months)
     */
    public function getMonthlyIncomeTrend(int $months = 6): array
    {
        return Cache::remember('dashboard.income_trend', self::CACHE_TTL, function () use ($months) {
            $data = [];
            
            for ($i = $months - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $startOfMonth = $date->copy()->startOfMonth();
                $endOfMonth = $date->copy()->endOfMonth();

                $income = Payment::confirmed()
                    ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                    ->sum('amount');

                $expense = OperationalCost::whereIn('status', ['recorded', 'approved'])
                    ->whereBetween('cost_date', [$startOfMonth, $endOfMonth])
                    ->sum('amount');

                $data[] = [
                    'month' => $date->format('M Y'),
                    'income' => (float) $income,
                    'expense' => (float) $expense,
                    'profit' => (float) ($income - $expense),
                ];
            }

            return $data;
        });
    }

    /**
     * Get room occupancy rate
     */
    public function getOccupancyRate(): float
    {
        $stats = $this->getRoomStatistics();
        
        if ($stats['total'] === 0) {
            return 0;
        }

        return round(($stats['occupied'] / $stats['total']) * 100, 1);
    }

    /**
     * Get tenants with payments due in next 7 days
     */
    public function getUpcomingDuePayments(int $days = 7): Collection
    {
        return Payment::with(['tenant', 'room'])
            ->where('status', 'pending')
            ->whereBetween('due_date', [Carbon::today(), Carbon::today()->addDays($days)])
            ->orderBy('due_date', 'asc')
            ->get();
    }

    /**
     * Clear dashboard cache
     */
    public function clearCache(): void
    {
        $keys = [
            'dashboard.active_tenants',
            'dashboard.room_statistics',
            'dashboard.total_income.*',
            'dashboard.total_operational.*',
            'dashboard.due_today_count',
            'dashboard.overdue_count',
            'dashboard.overdue_amount',
            'dashboard.income_trend',
        ];

        foreach ($keys as $key) {
            if (str_contains($key, '*')) {
                // For wildcard keys, we'd need to use cache tags or pattern deletion
                // For simplicity, we'll just forget the base key
                Cache::forget(str_replace('.*', '', $key));
            } else {
                Cache::forget($key);
            }
        }
    }

    /**
     * Get dashboard summary for quick overview
     */
    public function getDashboardSummary(): array
    {
        return [
            'active_tenants' => $this->getActiveTenantCount(),
            'room_statistics' => $this->getRoomStatistics(),
            'occupancy_rate' => $this->getOccupancyRate(),
            'financial' => $this->getCurrentMonthFinancialSummary(),
            'due_today' => $this->getPaymentsDueTodayCount(),
            'overdue' => [
                'count' => $this->getOverduePaymentsCount(),
                'amount' => $this->getOverduePaymentsAmount(),
            ],
        ];
    }
}
