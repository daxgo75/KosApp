<?php

namespace App\Filament\Widgets;

use App\Services\DashboardService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TenantStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';
    
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $dashboardService = app(DashboardService::class);
        
        $activeTenants = $dashboardService->getActiveTenantCount();
        $roomStats = $dashboardService->getRoomStatistics();
        $occupancyRate = $dashboardService->getOccupancyRate();

        return [
            Stat::make('Penyewa Aktif', $activeTenants)
                ->description('Total penyewa yang aktif')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, $activeTenants]),

            Stat::make('Kamar Terisi', $roomStats['occupied'] . ' / ' . $roomStats['total'])
                ->description($occupancyRate . '% tingkat hunian')
                ->descriptionIcon('heroicon-m-home')
                ->color($occupancyRate >= 80 ? 'success' : ($occupancyRate >= 50 ? 'warning' : 'danger')),

            Stat::make('Kamar Kosong', $roomStats['available'])
                ->description('Tersedia untuk disewa')
                ->descriptionIcon('heroicon-m-home-modern')
                ->color('info'),

            Stat::make('Kamar Maintenance', $roomStats['maintenance'])
                ->description('Dalam perbaikan')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('gray'),
        ];
    }
}
