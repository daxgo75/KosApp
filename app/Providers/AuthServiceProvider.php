<?php

namespace App\Providers;

use App\Models\FinancialReport;
use App\Models\OperationalCost;
use App\Models\Payment;
use App\Models\Tenant;
use App\Models\User;
use App\Policies\FinancialReportPolicy;
use App\Policies\OperationalCostPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\TenantPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Tenant::class => TenantPolicy::class,
        Payment::class => PaymentPolicy::class,
        OperationalCost::class => OperationalCostPolicy::class,
        FinancialReport::class => FinancialReportPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
