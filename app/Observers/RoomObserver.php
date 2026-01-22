<?php

namespace App\Observers;

use App\Models\Room;
use App\Services\DashboardService;

class RoomObserver
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}

    /**
     * Handle the Room "created" event.
     */
    public function created(Room $room): void
    {
        $this->dashboardService->clearCache();
    }

    /**
     * Handle the Room "updated" event.
     */
    public function updated(Room $room): void
    {
        $this->dashboardService->clearCache();
    }

    /**
     * Handle the Room "deleted" event.
     */
    public function deleted(Room $room): void
    {
        $this->dashboardService->clearCache();
    }

    /**
     * Handle the Room "restored" event.
     */
    public function restored(Room $room): void
    {
        $this->dashboardService->clearCache();
    }

    /**
     * Handle the Room "force deleted" event.
     */
    public function forceDeleted(Room $room): void
    {
        $this->dashboardService->clearCache();
    }
}
