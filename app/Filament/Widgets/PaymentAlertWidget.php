<?php

namespace App\Filament\Widgets;

use App\Services\DashboardService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PaymentAlertWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';
    
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $dashboardService = app(DashboardService::class);
        
        $dueTodayCount = $dashboardService->getPaymentsDueTodayCount();
        $overdueCount = $dashboardService->getOverduePaymentsCount();
        $upcomingPayments = $dashboardService->getUpcomingDuePayments(7);

        return [
            Stat::make('Jatuh Tempo Hari Ini', $dueTodayCount)
                ->description('Penyewa yang harus bayar hari ini')
                ->descriptionIcon('heroicon-m-clock')
                ->color($dueTodayCount > 0 ? 'warning' : 'success')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "\$dispatch('openModal', { component: 'due-today-modal' })",
                ]),

            Stat::make('Nunggak / Telat Bayar', $overdueCount)
                ->description('Penyewa yang menunggak')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color($overdueCount > 0 ? 'danger' : 'success'),

            Stat::make('Jatuh Tempo 7 Hari', $upcomingPayments->count())
                ->description('Pembayaran dalam 7 hari ke depan')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
        ];
    }
}
