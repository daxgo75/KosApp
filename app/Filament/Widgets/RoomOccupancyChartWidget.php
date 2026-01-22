<?php

namespace App\Filament\Widgets;

use App\Services\DashboardService;
use Filament\Widgets\ChartWidget;

class RoomOccupancyChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Status Kamar';
    
    protected static ?int $sort = 8;

    protected static ?string $pollingInterval = '60s';

    protected function getData(): array
    {
        $dashboardService = app(DashboardService::class);
        $roomStats = $dashboardService->getRoomStatistics();

        return [
            'datasets' => [
                [
                    'data' => [
                        $roomStats['occupied'],
                        $roomStats['available'],
                        $roomStats['maintenance'],
                    ],
                    'backgroundColor' => [
                        '#10B981', // Green - Occupied
                        '#3B82F6', // Blue - Available
                        '#6B7280', // Gray - Maintenance
                    ],
                    'borderColor' => [
                        '#059669',
                        '#2563EB',
                        '#4B5563',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Terisi', 'Kosong', 'Maintenance'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'maintainAspectRatio' => true,
            'cutout' => '60%',
        ];
    }
}
