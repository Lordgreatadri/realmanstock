<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display the admin dashboard
     */
    public function index(): View
    {
        $data = $this->dashboardService->getAdminDashboardData();
        $chartData = $this->dashboardService->getRevenueChart();
        
        // Format chart data for Chart.js
        $revenueChart = [
            'labels' => $chartData->pluck('date')->map(function($date) {
                return \Carbon\Carbon::parse($date)->format('M d');
            })->toArray(),
            'data' => $chartData->pluck('revenue')->toArray(),
        ];

        return view('admin.dashboard', compact('data', 'revenueChart'));
    }
}
