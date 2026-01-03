<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Animal;
use App\Models\StoreItem;
use App\Models\Customer;
use App\Models\ProcessingRequest;
use App\Models\FreezerInventory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ManagerDashboardService
{
    /**
     * Get manager dashboard data focused on operations
     */
    public function getManagerDashboardData()
    {
        return [
            // Today's operations
            'today' => [
                'orders' => Order::whereDate('created_at', today())->count(),
                'revenue' => Order::whereDate('created_at', today())
                    ->where('status', '!=', 'cancelled')
                    ->sum('total'),
                'processing' => ProcessingRequest::whereDate('created_at', today())->count(),
                'deliveries' => Order::whereDate('delivery_date', today())->count(),
            ],

            // Inventory status
            'inventory' => [
                'animals_available' => Animal::where('status', 'available')->count(),
                'animals_quarantine' => Animal::where('status', 'quarantine')->count(),
                'freezer_items' => FreezerInventory::sum('weight'),
                'low_stock_items' => StoreItem::where('quantity', '<=', DB::raw('reorder_level'))->count(),
            ],

            // Orders overview
            'orders' => [
                'pending' => Order::where('status', 'pending')->count(),
                'processing' => Order::where('status', 'processing')->count(),
                'ready_delivery' => Order::where('status', 'ready')->count(),
                'completed_today' => Order::whereDate('updated_at', today())
                    ->where('status', 'delivered')
                    ->count(),
            ],

            // Processing overview
            'processing' => [
                'pending' => ProcessingRequest::where('status', 'pending')->count(),
                'in_progress' => ProcessingRequest::where('status', 'in_progress')->count(),
                'completed_today' => ProcessingRequest::whereDate('updated_at', today())
                    ->where('status', 'completed')
                    ->count(),
            ],

            // Customer metrics
            'customers' => [
                'total' => Customer::count(),
                'new_this_month' => Customer::whereMonth('created_at', now()->month)->count(),
                'with_pending_balance' => Customer::whereHas('orders', function($q) {
                    $q->where('balance', '>', 0);
                })->count(),
            ],

            // Weekly performance
            'weekly' => $this->getWeeklyPerformance(),

            // Lists for quick access
            'recent_orders' => $this->getRecentOrders(5),
            'urgent_tasks' => $this->getUrgentTasks(),
            'low_stock_alerts' => $this->getLowStockAlerts(),
            'today_deliveries' => $this->getTodayDeliveries(),
            'pending_processing' => $this->getPendingProcessing(),
        ];
    }

    /**
     * Get operational charts data
     */
    public function getOperationalCharts()
    {
        return [
            'sales' => $this->getSalesChart(),
            'processing' => $this->getProcessingChart(),
            'inventory' => $this->getInventoryTrends(),
        ];
    }

    /**
     * Get last 7 days sales chart
     */
    private function getSalesChart()
    {
        return Order::selectRaw('DATE(created_at) as date, SUM(total) as total, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->where('status', '!=', 'cancelled')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Get processing status distribution
     */
    private function getProcessingChart()
    {
        return ProcessingRequest::selectRaw('status, COUNT(*) as count')
            ->whereMonth('created_at', now()->month)
            ->groupBy('status')
            ->get();
    }

    /**
     * Get inventory trends
     */
    private function getInventoryTrends()
    {
        return Animal::selectRaw('category_id, status, COUNT(*) as count')
            ->groupBy('category_id', 'status')
            ->with('category')
            ->get();
    }

    /**
     * Get weekly performance comparison
     */
    private function getWeeklyPerformance()
    {
        $thisWeek = Order::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->where('status', '!=', 'cancelled')->sum('total');

        $lastWeek = Order::whereBetween('created_at', [
            Carbon::now()->subWeek()->startOfWeek(),
            Carbon::now()->subWeek()->endOfWeek()
        ])->where('status', '!=', 'cancelled')->sum('total');

        $growth = $lastWeek > 0 ? (($thisWeek - $lastWeek) / $lastWeek) * 100 : 0;

        return [
            'this_week' => $thisWeek,
            'last_week' => $lastWeek,
            'growth_percentage' => round($growth, 2),
        ];
    }

    /**
     * Get recent orders
     */
    private function getRecentOrders($limit = 5)
    {
        return Order::with(['customer', 'items.item'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get urgent tasks (overdue deliveries, pending approvals, etc.)
     */
    private function getUrgentTasks()
    {
        $tasks = collect();

        // Overdue deliveries
        $overdueDeliveries = Order::where('delivery_date', '<', today())
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->count();

        if ($overdueDeliveries > 0) {
            $tasks->push([
                'type' => 'overdue_delivery',
                'title' => 'Overdue Deliveries',
                'count' => $overdueDeliveries,
                'priority' => 'high',
                'url' => route('manager.orders.index', ['filter' => 'overdue']),
            ]);
        }

        // Low stock items
        $lowStock = StoreItem::where('quantity', '<=', DB::raw('reorder_level'))->count();
        if ($lowStock > 0) {
            $tasks->push([
                'type' => 'low_stock',
                'title' => 'Low Stock Items',
                'count' => $lowStock,
                'priority' => 'medium',
                'url' => route('manager.store-items.index', ['filter' => 'low-stock']),
            ]);
        }

        // Animals in quarantine
        $quarantine = Animal::where('status', 'quarantine')->count();
        if ($quarantine > 0) {
            $tasks->push([
                'type' => 'quarantine',
                'title' => 'Animals in Quarantine',
                'count' => $quarantine,
                'priority' => 'medium',
                'url' => route('manager.animals.index', ['status' => 'quarantine']),
            ]);
        }

        // Pending processing requests
        $pendingProcessing = ProcessingRequest::where('status', 'pending')
            ->where('scheduled_date', '<=', today()->addDays(1))
            ->count();

        if ($pendingProcessing > 0) {
            $tasks->push([
                'type' => 'processing',
                'title' => 'Processing Due Soon',
                'count' => $pendingProcessing,
                'priority' => 'high',
                'url' => route('manager.processing.index', ['status' => 'pending']),
            ]);
        }

        return $tasks->sortByDesc('priority');
    }

    /**
     * Get low stock alerts
     */
    private function getLowStockAlerts()
    {
        return StoreItem::where('quantity', '<=', DB::raw('reorder_level'))
            ->orderBy('quantity', 'asc')
            ->limit(10)
            ->get();
    }

    /**
     * Get today's deliveries
     */
    private function getTodayDeliveries()
    {
        return Order::with('customer')
            ->whereDate('delivery_date', today())
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->get();
    }

    /**
     * Get pending processing requests
     */
    private function getPendingProcessing()
    {
        return ProcessingRequest::with(['order.customer', 'animal.category'])
            ->where('status', 'pending')
            ->orderBy('scheduled_date', 'asc')
            ->limit(10)
            ->get();
    }
}
