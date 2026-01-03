<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Animal;
use App\Models\StoreItem;
use App\Models\ProcessingRequest;
use App\Models\FreezerInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        return view('manager.reports.index');
    }

    public function sales(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

        // Sales summary
        $summary = [
            'total_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_revenue' => Order::whereBetween('created_at', [$startDate, $endDate])->sum('total'),
            'total_paid' => Order::whereBetween('created_at', [$startDate, $endDate])->sum('amount_paid'),
            'total_balance' => Order::whereBetween('created_at', [$startDate, $endDate])->sum('balance'),
            'completed_orders' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'delivered')->count(),
            'pending_orders' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->whereIn('status', ['pending', 'processing'])->count(),
        ];

        // Daily sales chart data
        $dailySales = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(total) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Sales by customer
        $customerSales = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('customer_id', DB::raw('SUM(total) as total_sales'), DB::raw('COUNT(*) as order_count'))
            ->with('customer')
            ->groupBy('customer_id')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get();

        // Sales by status
        $statusBreakdown = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('status')
            ->get();

        return view('manager.reports.sales', compact('summary', 'dailySales', 'customerSales', 'statusBreakdown', 'startDate', 'endDate'));
    }

    public function inventory(Request $request)
    {
        // Store items inventory
        $storeItems = StoreItem::with('category')
            ->select('category_id', DB::raw('COUNT(*) as item_count'), 
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('SUM(quantity * cost_price) as total_value'))
            ->groupBy('category_id')
            ->get();

        $lowStockItems = StoreItem::whereRaw('quantity <= reorder_level')
            ->where('quantity', '>', 0)
            ->with('category')
            ->get();

        $outOfStockItems = StoreItem::where('quantity', '<=', 0)
            ->with('category')
            ->get();

        // Freezer inventory
        $freezerInventory = FreezerInventory::with('category')
            ->select('status', DB::raw('COUNT(*) as count'), 
                    DB::raw('SUM(weight) as total_weight'),
                    DB::raw('SUM(weight * selling_price_per_kg) as total_value'))
            ->groupBy('status')
            ->get();

        $expiringItems = FreezerInventory::where('expiry_date', '<=', Carbon::now()->addDays(7))
            ->where('status', 'in_stock')
            ->with('category')
            ->orderBy('expiry_date')
            ->get();

        // Animals inventory
        $animalsInventory = Animal::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return view('manager.reports.inventory', compact(
            'storeItems', 'lowStockItems', 'outOfStockItems',
            'freezerInventory', 'expiringItems', 'animalsInventory'
        ));
    }

    public function customers(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

        // Customer summary
        $summary = [
            'total_customers' => Customer::count(),
            'new_customers' => Customer::whereBetween('created_at', [$startDate, $endDate])->count(),
            'active_customers' => Customer::whereHas('orders', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            })->count(),
        ];

        // Top customers by revenue
        $topCustomers = Customer::withSum(['orders' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }], 'total')
            ->withCount(['orders' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->orderByDesc('orders_sum_total')
            ->limit(10)
            ->get();

        // Customer credit analysis
        $customersWithCredit = Customer::where('allow_credit', true)->count();
        $totalCreditLimit = Customer::where('allow_credit', true)->sum('credit_limit');
        
        // Calculate used credit from orders with outstanding balance
        $totalCreditUsed = Customer::where('allow_credit', true)
            ->withSum(['orders' => fn($q) => $q->where('balance', '>', 0)], 'balance')
            ->get()
            ->sum('orders_sum_balance');
        
        $creditAnalysis = [
            'customer_count' => $customersWithCredit,
            'total_credit_limit' => $totalCreditLimit,
            'total_credit_used' => $totalCreditUsed,
            'available_credit' => $totalCreditLimit - $totalCreditUsed,
        ];

        return view('manager.reports.customers', compact('summary', 'topCustomers', 'creditAnalysis', 'startDate', 'endDate'));
    }

    public function processing(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

        // Processing summary
        $summary = [
            'total_requests' => ProcessingRequest::whereBetween('created_at', [$startDate, $endDate])->count(),
            'completed' => ProcessingRequest::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')->count(),
            'in_progress' => ProcessingRequest::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'in_progress')->count(),
            'pending' => ProcessingRequest::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'pending')->count(),
        ];

        // Processing efficiency
        $efficiency = ProcessingRequest::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->selectRaw('AVG((dressed_weight / live_weight) * 100) as avg_dressing_percentage')
            ->first();

        // Processing by status
        $statusBreakdown = ProcessingRequest::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // Monthly processing trend
        $monthlyTrend = ProcessingRequest::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count, SUM(live_weight) as total_live_weight, SUM(dressed_weight) as total_dressed_weight')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('manager.reports.processing', compact('summary', 'efficiency', 'statusBreakdown', 'monthlyTrend', 'startDate', 'endDate'));
    }

    public function financial(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

        // Revenue analysis
        $revenue = [
            'total_sales' => Order::whereBetween('created_at', [$startDate, $endDate])->sum('total'),
            'total_paid' => Order::whereBetween('created_at', [$startDate, $endDate])->sum('amount_paid'),
            'total_balance' => Order::whereBetween('created_at', [$startDate, $endDate])->sum('balance'),
            'processing_fees' => Order::whereBetween('created_at', [$startDate, $endDate])->sum('processing_fee'),
            'delivery_fees' => Order::whereBetween('created_at', [$startDate, $endDate])->sum('delivery_fee'),
        ];

        // Inventory value
        $inventoryValue = [
            'store_items' => StoreItem::selectRaw('SUM(quantity * cost_price) as value')->value('value') ?? 0,
            'freezer_inventory' => FreezerInventory::selectRaw('SUM(weight * cost_price) as value')->value('value') ?? 0,
        ];

        // Monthly revenue trend
        $monthlyRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total) as revenue, SUM(amount_paid) as paid')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Payment status breakdown
        $paymentStatus = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("
                CASE 
                    WHEN balance = 0 THEN 'paid'
                    WHEN amount_paid > 0 AND balance > 0 THEN 'partial'
                    ELSE 'pending'
                END as payment_status,
                COUNT(*) as count,
                SUM(total) as total
            ")
            ->groupBy('payment_status')
            ->get();

        return view('manager.reports.financial', compact('revenue', 'inventoryValue', 'monthlyRevenue', 'paymentStatus', 'startDate', 'endDate'));
    }

    public function exportSales(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

        $orders = Order::with(['customer', 'items'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        return Excel::download(new \App\Exports\SalesReportExport($orders, $startDate, $endDate), 
            'sales_report_' . Carbon::now()->format('Y-m-d') . '.xlsx');
    }

    public function exportInventory(Request $request)
    {
        $storeItems = StoreItem::with('category')->get();
        $freezerItems = FreezerInventory::with('category')->get();

        return Excel::download(new \App\Exports\InventoryReportExport($storeItems, $freezerItems), 
            'inventory_report_' . Carbon::now()->format('Y-m-d') . '.xlsx');
    }

    public function exportCustomers(Request $request)
    {
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfMonth();

        $customers = Customer::withCount(['orders' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withSum(['orders' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }], 'total')
            ->orderByDesc('orders_sum_total')
            ->get();

        return Excel::download(new \App\Exports\CustomerReportExport($customers, $startDate, $endDate), 
            'customers_report_' . Carbon::now()->format('Y-m-d') . '.xlsx');
    }

    public function exportProcessing(Request $request)
    {
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfMonth();

        $requests = ProcessingRequest::with(['customer', 'animal'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        return Excel::download(new \App\Exports\ProcessingReportExport($requests, $startDate, $endDate), 
            'processing_report_' . Carbon::now()->format('Y-m-d') . '.xlsx');
    }

    public function exportFinancial(Request $request)
    {
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfMonth();

        // Revenue summary
        $revenue = [
            'total_sales' => Order::whereBetween('created_at', [$startDate, $endDate])->sum('total'),
            'amount_paid' => Order::whereBetween('created_at', [$startDate, $endDate])->sum('amount_paid'),
            'outstanding' => Order::whereBetween('created_at', [$startDate, $endDate])->sum('balance'),
            'processing_fees' => Order::whereBetween('created_at', [$startDate, $endDate])->sum('processing_fee'),
            'delivery_fees' => Order::whereBetween('created_at', [$startDate, $endDate])->sum('delivery_fee'),
        ];

        // Inventory value
        $inventoryValue = [
            'store_items' => StoreItem::selectRaw('SUM(quantity * cost_price) as value')->value('value') ?? 0,
            'freezer_inventory' => FreezerInventory::selectRaw('SUM(weight * cost_price) as value')->value('value') ?? 0,
        ];

        $orders = Order::with(['customer'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        return Excel::download(new \App\Exports\FinancialReportExport($orders, $revenue, $inventoryValue, $startDate, $endDate), 
            'financial_report_' . Carbon::now()->format('Y-m-d') . '.xlsx');
    }

    public function printSales(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

        $summary = [
            'total_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_revenue' => Order::whereBetween('created_at', [$startDate, $endDate])->sum('total'),
            'total_paid' => Order::whereBetween('created_at', [$startDate, $endDate])->sum('amount_paid'),
        ];

        $orders = Order::with(['customer'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('manager.reports.pdf.sales', compact('summary', 'orders', 'startDate', 'endDate'));
        return $pdf->download('sales_report_' . Carbon::now()->format('Y-m-d') . '.pdf');
    }

    public function printFinancial(Request $request)
    {
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfMonth();

        // Revenue summary
        $revenue = [
            'total_sales' => Order::whereBetween('created_at', [$startDate, $endDate])->sum('total'),
            'amount_paid' => Order::whereBetween('created_at', [$startDate, $endDate])->sum('amount_paid'),
            'outstanding' => Order::whereBetween('created_at', [$startDate, $endDate])->sum('balance'),
            'processing_fees' => Order::whereBetween('created_at', [$startDate, $endDate])->sum('processing_fee'),
            'delivery_fees' => Order::whereBetween('created_at', [$startDate, $endDate])->sum('delivery_fee'),
        ];

        // Inventory value
        $inventoryValue = [
            'store_items' => StoreItem::selectRaw('SUM(quantity * cost_price) as value')->value('value') ?? 0,
            'freezer_inventory' => FreezerInventory::selectRaw('SUM(weight * cost_price) as value')->value('value') ?? 0,
        ];

        // Payment status breakdown
        $paymentStatus = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("
                CASE 
                    WHEN balance = 0 THEN 'paid'
                    WHEN amount_paid > 0 AND balance > 0 THEN 'partial'
                    ELSE 'pending'
                END as payment_status,
                COUNT(*) as count,
                SUM(total) as total
            ")
            ->groupBy('payment_status')
            ->get();

        $pdf = Pdf::loadView('manager.reports.pdf.financial', compact('revenue', 'inventoryValue', 'paymentStatus', 'startDate', 'endDate'));
        return $pdf->download('financial_report_' . Carbon::now()->format('Y-m-d') . '.pdf');
    }
}
