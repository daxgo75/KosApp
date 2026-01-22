<?php

namespace App\Observers;

use App\Models\Payment;
use App\Services\DashboardService;

class PaymentObserver
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}

    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        $this->dashboardService->clearCache();
    }

    /**
     * Handle the Payment "updated" event.
     */
    public function updated(Payment $payment): void
    {
        $this->dashboardService->clearCache();
    }

    /**
     * Handle the Payment "deleted" event.
     */
    public function deleted(Payment $payment): void
    {
        $this->dashboardService->clearCache();
    }

    /**
     * Handle the Payment "restored" event.
     */
    public function restored(Payment $payment): void
    {
        $this->dashboardService->clearCache();
    }

    /**
     * Handle the Payment "force deleted" event.
     */
    public function forceDeleted(Payment $payment): void
    {
        $this->dashboardService->clearCache();
    }
}
