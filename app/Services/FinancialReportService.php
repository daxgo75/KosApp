<?php

namespace App\Services;

use App\Models\FinancialReport;
use App\Models\Payment;
use App\Models\OperationalCost;
use App\Models\Tenant;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialReportService
{
    /**
     * Generate financial report for a period
     */
    public function generateReport(
        string $reportType,
        Carbon $periodStart,
        Carbon $periodEnd,
        int $userId
    ): FinancialReport {
        return DB::transaction(function () use ($reportType, $periodStart, $periodEnd, $userId) {
            // Calculate total income from confirmed payments
            $totalIncome = Payment::confirmed()
                ->byPeriod($periodStart, $periodEnd)
                ->sum('amount');

            // Calculate total operational costs
            $totalOperationalCost = OperationalCost::approved()
                ->byPeriod($periodStart, $periodEnd)
                ->sum('amount');

            // Calculate net profit
            $netProfit = $totalIncome - $totalOperationalCost;

            // Calculate outstanding payments
            $outstandingPayment = Payment::pending()
                ->where('due_date', '<', now())
                ->sum('amount');

            // Get tenant statistics
            $totalTenants = Tenant::active()->count();

            // Get room statistics
            $occupiedRooms = Room::occupied()->count();
            $availableRooms = Room::available()->count();

            // Generate summary
            $summary = $this->generateSummary(
                $totalIncome,
                $totalOperationalCost,
                $netProfit,
                $totalTenants,
                $occupiedRooms
            );

            return FinancialReport::create([
                'report_type' => $reportType,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'total_income' => $totalIncome,
                'total_operational_cost' => $totalOperationalCost,
                'net_profit' => $netProfit,
                'outstanding_payment' => $outstandingPayment,
                'total_tenants' => $totalTenants,
                'occupied_rooms' => $occupiedRooms,
                'available_rooms' => $availableRooms,
                'summary' => $summary,
                'status' => 'draft',
                'created_by' => $userId,
            ]);
        });
    }

    /**
     * Generate monthly report
     */
    public function generateMonthlyReport(int $year, int $month, int $userId): FinancialReport
    {
        $periodStart = Carbon::create($year, $month, 1)->startOfMonth();
        $periodEnd = Carbon::create($year, $month, 1)->endOfMonth();

        return $this->generateReport('monthly', $periodStart, $periodEnd, $userId);
    }

    /**
     * Generate quarterly report
     */
    public function generateQuarterlyReport(int $year, int $quarter, int $userId): FinancialReport
    {
        $month = ($quarter - 1) * 3 + 1;
        $periodStart = Carbon::create($year, $month, 1)->startOfQuarter();
        $periodEnd = Carbon::create($year, $month, 1)->endOfQuarter();

        return $this->generateReport('quarterly', $periodStart, $periodEnd, $userId);
    }

    /**
     * Generate yearly report
     */
    public function generateYearlyReport(int $year, int $userId): FinancialReport
    {
        $periodStart = Carbon::create($year, 1, 1)->startOfYear();
        $periodEnd = Carbon::create($year, 12, 31)->endOfYear();

        return $this->generateReport('yearly', $periodStart, $periodEnd, $userId);
    }

    /**
     * Generate summary text
     */
    private function generateSummary(
        float $totalIncome,
        float $totalOperationalCost,
        float $netProfit,
        int $totalTenants,
        int $occupiedRooms
    ): string {
        $profitStatus = $netProfit >= 0 ? 'profit' : 'loss';
        $profitAmount = abs($netProfit);

        return "Financial summary: Total income Rp " . number_format($totalIncome, 0, ',', '.') .
            ", operational costs Rp " . number_format($totalOperationalCost, 0, ',', '.') .
            ", resulting in a {$profitStatus} of Rp " . number_format($profitAmount, 0, ',', '.') .
            ". Active tenants: {$totalTenants}, occupied rooms: {$occupiedRooms}.";
    }

    /**
     * Get financial dashboard data
     */
    public function getDashboardData(): array
    {
        $currentMonth = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();

        return [
            'current_month_income' => Payment::confirmed()
                ->byPeriod($currentMonth, $currentMonthEnd)
                ->sum('amount'),
            'current_month_costs' => OperationalCost::approved()
                ->byPeriod($currentMonth, $currentMonthEnd)
                ->sum('amount'),
            'outstanding_payments' => Payment::pending()
                ->where('due_date', '<', now())
                ->sum('amount'),
            'total_active_tenants' => Tenant::active()->count(),
            'occupied_rooms' => Room::occupied()->count(),
            'available_rooms' => Room::available()->count(),
        ];
    }
}
