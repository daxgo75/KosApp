<?php

namespace App\Filament\Widgets;

use App\Services\DashboardService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Number;

class FinancialStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';
    
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $dashboardService = app(DashboardService::class);
        
        $financial = $dashboardService->getCurrentMonthFinancialSummary();
        $overdueAmount = $dashboardService->getOverduePaymentsAmount();

        return [
            Stat::make('Pemasukan Bulan Ini', $this->formatCurrency($financial['income']))
                ->description($financial['period'])
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('Biaya Operasional', $this->formatCurrency($financial['operational_cost']))
                ->description('Total pengeluaran bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Laba Bersih', $this->formatCurrency($financial['net_profit']))
                ->description('Pemasukan - Operasional')
                ->descriptionIcon($financial['net_profit'] >= 0 ? 'heroicon-m-arrow-up-circle' : 'heroicon-m-arrow-down-circle')
                ->color($financial['net_profit'] >= 0 ? 'success' : 'danger'),

            Stat::make('Total Tunggakan', $this->formatCurrency($overdueAmount))
                ->description('Pembayaran yang belum lunas')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($overdueAmount > 0 ? 'danger' : 'success'),
        ];
    }

    private function formatCurrency(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
