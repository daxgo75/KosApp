<?php

namespace App\Providers;

use App\Models\OperationalCost;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use App\Observers\OperationalCostObserver;
use App\Observers\PaymentObserver;
use App\Observers\RoomObserver;
use App\Observers\TenantObserver;
use App\Observers\UserObserver;
use App\Services\DashboardService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(DashboardService::class, function ($app) {
            return new DashboardService();
        });
    }

    public function boot(): void
    {
        Tenant::observe(TenantObserver::class);
        Payment::observe(PaymentObserver::class);
        Room::observe(RoomObserver::class);
        OperationalCost::observe(OperationalCostObserver::class);
        User::observe(UserObserver::class);

        // Prevent lazy loading in production
        if (config('app.env') !== 'testing') {
            Model::preventLazyLoading();
        }

        // Enable strict mode
        Model::preventSilentlyDiscardingAttributes();
        Model::preventAccessingMissingAttributes();
    }
}
