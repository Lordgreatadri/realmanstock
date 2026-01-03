<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Services\ManagerDashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(ManagerDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display the manager dashboard
     */
    public function index(): View
    {
        $data = $this->dashboardService->getManagerDashboardData();
        $chartData = $this->dashboardService->getOperationalCharts();
        
        // Format chart data for Chart.js
        $salesChart = [
            'labels' => $chartData['sales']->pluck('date')->map(function($date) {
                return \Carbon\Carbon::parse($date)->format('M d');
            })->toArray(),
            'data' => $chartData['sales']->pluck('total')->toArray(),
        ];

        $processingChart = [
            'labels' => $chartData['processing']->pluck('status')->toArray(),
            'data' => $chartData['processing']->pluck('count')->toArray(),
        ];

        return view('manager.dashboard', compact('data', 'salesChart', 'processingChart'));
    }
}
