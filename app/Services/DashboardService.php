<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Animal;
use App\Models\StoreItem;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getAdminDashboardData()
    {
        $summary = $this->getSummaryStats();
        
        return [
            'users' => [
                'total' => \App\Models\User::count(),
                'pending' => \App\Models\User::where('is_approved', false)->count(),
                'new_this_month' => \App\Models\User::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
            ],
            'animals' => [
                'total' => Animal::count(),
                'available' => Animal::where('status', 'available')->count(),
                'sold' => Animal::where('status', 'sold')->count(),
            ],
            'orders' => [
                'total' => Order::count(),
                'pending' => Order::where('status', 'pending')->count(),
                'completed' => Order::where('status', 'delivered')->count(),
            ],
            'revenue' => [
                'total' => Order::where('status', '!=', 'cancelled')->sum('total'),
                'this_month' => $this->getMonthlyRevenue(),
                'last_month' => Order::whereMonth('created_at', now()->subMonth()->month)
                    ->whereYear('created_at', now()->subMonth()->year)
                    ->where('status', '!=', 'cancelled')
                    ->sum('total'),
                'growth_percentage' => $this->calculateGrowthPercentage(),
            ],
            'summary' => $summary,
            'recent_orders' => $this->getRecentOrders(),
            'recent_activities' => $this->getRecentActivities(),
            'low_stock_alerts' => $this->getLowStockAlerts(),
            'today_revenue' => $this->getTodayRevenue(),
            'monthly_revenue' => $this->getMonthlyRevenue(),
            'top_products' => $this->getTopSellingProducts(),
            'pending_approvals' => $this->getPendingUserApprovals(),
        ];
    }
    
    private function getRecentActivities($limit = 10)
    {
        $activities = collect();
        
        // Get recent orders
        $recentOrders = Order::with('customer')
            ->latest()
            ->limit(5)
            ->get();
            
        foreach ($recentOrders as $order) {
            $activities->push([
                'description' => "New order #{$order->order_number} from " . ($order->customer->name ?? 'Unknown'),
                'time' => $order->created_at->diffForHumans(),
                'created_at' => $order->created_at,
            ]);
        }
        
        // Get recent user registrations
        $recentUsers = \App\Models\User::latest()
            ->limit(3)
            ->get();
            
        foreach ($recentUsers as $user) {
            $activities->push([
                'description' => "New user registered: {$user->name}",
                'time' => $user->created_at->diffForHumans(),
                'created_at' => $user->created_at,
            ]);
        }
        
        // Get recent animals added
        $recentAnimals = Animal::latest()
            ->limit(3)
            ->get();
            
        foreach ($recentAnimals as $animal) {
            $activities->push([
                'description' => "New animal added: {$animal->tag_number} ({$animal->breed})",
                'time' => $animal->created_at->diffForHumans(),
                'created_at' => $animal->created_at,
            ]);
        }
        
        // Sort by created_at and take the most recent
        return $activities->sortByDesc('created_at')->take($limit)->values();
    }
    
    private function calculateGrowthPercentage()
    {
        $thisMonth = $this->getMonthlyRevenue();
        $lastMonth = Order::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->where('status', '!=', 'cancelled')
            ->sum('total');
            
        if ($lastMonth == 0) {
            return $thisMonth > 0 ? 100 : 0;
        }
        
        return (($thisMonth - $lastMonth) / $lastMonth) * 100;
    }

    private function getSummaryStats()
    {
        return [
            'total_animals' => Animal::count(),
            'available_animals' => Animal::available()->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_customers' => Customer::count(),
            'today_orders' => Order::whereDate('created_at', today())->count(),
        ];
    }

    private function getRecentOrders($limit = 10)
    {
        return Order::with(['customer', 'items'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    private function getLowStockAlerts()
    {
        $inventoryService = new InventoryService();
        return $inventoryService->getInventoryAlerts();
    }

    private function getTodayRevenue()
    {
        return Order::whereDate('created_at', today())
            ->where('status', '!=', 'cancelled')
            ->sum('total');
    }

    private function getMonthlyRevenue()
    {
        return Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', '!=', 'cancelled')
            ->sum('total');
    }

    private function getTopSellingProducts($limit = 5)
    {
        return DB::table('order_items')
            ->select('item_name', DB::raw('COUNT(*) as total_sales'), DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('item_name')
            ->orderBy('total_sales', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getPendingUserApprovals()
    {
        return \App\Models\User::pending()
            ->latest()
            ->limit(5)
            ->get();
    }

    public function getRevenueChart($period = 'month')
    {
        $query = Order::where('status', '!=', 'cancelled')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as revenue')
            );

        if ($period === 'month') {
            $query->where('created_at', '>=', now()->startOfMonth());
        } elseif ($period === 'week') {
            $query->where('created_at', '>=', now()->startOfWeek());
        } elseif ($period === 'year') {
            $query->where('created_at', '>=', now()->startOfYear());
        }

        return $query->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function getProductCategoryDistribution()
    {
        return DB::table('order_items')
            ->join('categories', function ($join) {
                $join->on('order_items.item_type', '=', DB::raw("'animal'"))
                     ->join('animals', 'order_items.item_id', '=', 'animals.id')
                     ->on('animals.category_id', '=', 'categories.id');
            })
            ->select('categories.name', DB::raw('COUNT(*) as count'))
            ->groupBy('categories.name')
            ->get();
    }
}
