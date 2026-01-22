<?php

namespace App\Observers;

use App\Models\Tenant;
use App\Services\DashboardService;

class TenantObserver
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}

    /**
     * Handle the Tenant "created" event.
     */
    public function created(Tenant $tenant): void
    {
        $this->dashboardService->clearCache();
    }

    /**
     * Handle the Tenant "updated" event.
     */
    public function updated(Tenant $tenant): void
    {
        $this->dashboardService->clearCache();
    }

    /**
     * Handle the Tenant "deleted" event.
     */
    public function deleted(Tenant $tenant): void
    {
        $this->dashboardService->clearCache();
    }

    /**
     * Handle the Tenant "restored" event.
     */
    public function restored(Tenant $tenant): void
    {
        $this->dashboardService->clearCache();
    }

    /**
     * Handle the Tenant "force deleted" event.
     */
    public function forceDeleted(Tenant $tenant): void
    {
        $this->dashboardService->clearCache();
    }
}
