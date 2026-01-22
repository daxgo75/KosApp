<?php

namespace App\Http\Controllers;

use App\Services\MonitoringService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $dashboardData = MonitoringService::getDashboardData();

        return view('dashboard', $dashboardData);
    }
}
