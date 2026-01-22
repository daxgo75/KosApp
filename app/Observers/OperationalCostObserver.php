<?php

namespace App\Observers;

use App\Models\OperationalCost;
use App\Services\DashboardService;

class OperationalCostObserver
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}

    /**
     * Handle the OperationalCost "created" event.
     */
    public function created(OperationalCost $operationalCost): void
    {
        $this->dashboardService->clearCache();
    }

    /**
     * Handle the OperationalCost "updated" event.
     */
    public function updated(OperationalCost $operationalCost): void
    {
        $this->dashboardService->clearCache();
    }

    /**
     * Handle the OperationalCost "deleted" event.
     */
    public function deleted(OperationalCost $operationalCost): void
    {
        $this->dashboardService->clearCache();
    }

    /**
     * Handle the OperationalCost "restored" event.
     */
    public function restored(OperationalCost $operationalCost): void
    {
        $this->dashboardService->clearCache();
    }

    /**
     * Handle the OperationalCost "force deleted" event.
     */
    public function forceDeleted(OperationalCost $operationalCost): void
    {
        $this->dashboardService->clearCache();
    }
}
